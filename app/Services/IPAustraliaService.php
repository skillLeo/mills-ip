<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IPAustraliaService
{
    private string $clientId;
    private string $clientSecret;
    private string $tokenUrl;
    private string $baseUrl;

    public function __construct()
    {
        $this->clientId     = config('services.ip_australia.client_id', '');
        $this->clientSecret = config('services.ip_australia.client_secret', '');
        $this->tokenUrl     = config('services.ip_australia.token_url', '');
        $this->baseUrl      = config('services.ip_australia.base_url', '');
    }

    private function getAccessToken(): string
    {
        return Cache::remember('ip_australia_token', 3540, function () {
            $response = $this->http()->asForm()->post($this->tokenUrl, [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if ($response->failed()) {
                Log::error('IP Australia token fetch failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                throw new \Exception('Unable to connect to IP Australia API.');
            }

            return $response->json('access_token');
        });
    }

    private function http(?string $token = null): PendingRequest
    {
        $request = Http::withOptions([
            'proxy' => '',
            'curl' => [
                CURLOPT_PROXY => '',
                CURLOPT_NOPROXY => '*',
            ],
        ]);

        return $token ? $request->withToken($token) : $request;
    }

    public function search(string $query): array
    {
        if (empty($this->clientId) || empty($this->tokenUrl) || empty($this->baseUrl)) {
            return [];
        }

        $cacheKey = 'ip_au_search_' . md5(strtolower(trim($query)));

        return Cache::remember($cacheKey, 600, function () use ($query) {
            return $this->fetchFromApi($query);
        });
    }

    private function fetchFromApi(string $query): array
    {
        try {
            $token = $this->getAccessToken();

            // Step 1 - Get list of trademark IDs.
            $response = $this->http($token)->post($this->baseUrl . '/search/quick', [
                'query' => $query,
                'sort' => [
                    'field' => 'NUMBER',
                    'direction' => 'ASCENDING',
                ],
                'filters' => [
                    'quickSearchType' => ['WORD'],
                    'status' => ['REGISTERED'],
                ],
            ]);

            if ($response->failed()) {
                Log::error('IP Australia search failed', [
                    'query'  => $query,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return [];
            }

            $ids = $this->extractIds($response->json() ?? []);

            if (empty($ids)) {
                return [];
            }

            // Step 2 - Fetch top 8 details.
            $topIds = array_values(array_slice($ids, 0, 8));

            $results = [];
            foreach ($topIds as $id) {
                $response = $this->http($token)->get($this->baseUrl . '/trade-mark/' . $id);
                if ($response->successful()) {
                    $tm = $response->json();
                    if (is_array($tm)) {
                        $results[] = $this->mapTrademark($tm, $id);
                    }
                }
            }

            return array_values(array_filter($results));

        } catch (\Exception $e) {
            Log::error('IPAustraliaService error: ' . $e->getMessage());
            return [];
        }
    }

    private function extractIds(array $data): array
    {
        $items = $data['trademarkIds']       ??
                 $data['tradeMarks']         ??
                 $data['ipRightIdentifiers'] ??
                 $data['data']               ??
                 $data['results']            ??
                 $data;

        if (!is_array($items)) {
            return [];
        }

        $ids = [];
        foreach ($items as $item) {
            if (is_int($item) || is_string($item)) {
                $ids[] = $item;
            } elseif (is_array($item)) {
                $id = $item['ipRightIdentifier'] ?? $item['id'] ?? $item['number'] ?? null;
                if ($id) $ids[] = $id;
            }
        }

        return $ids;
    }

    private function mapTrademark(array $tm, string|int $id): array
    {
        $name = !empty($tm['words']) ? implode(' ', (array) $tm['words']) : ($tm['name'] ?? '');

        return [
            'trademark_number'  => $tm['number']              ?? $id,
            'trademark_name'    => strtoupper($name),
            'status'            => $tm['statusGroup']          ?? $tm['statusCode']       ?? '',
            'owner'             => $this->extractOwner($tm),
            'class'             => $this->extractClasses($tm),
            'class_description' => '',
            'application_date'  => $tm['filingDate']           ?? $tm['lodgementDate']    ?? '',
            'registration_date' => $tm['enteredOnRegisterDate'] ?? $tm['registeredFromDate'] ?? '',
        ];
    }

    private function extractOwner(array $tm): string
    {
        $owners = $tm['owner'] ?? $tm['owners'] ?? [];
        if (!empty($owners) && is_array($owners)) {
            $first = $owners[0];
            return is_array($first) ? ($first['name'] ?? '') : (string) $first;
        }
        return $tm['ownerName'] ?? $tm['applicantName'] ?? '';
    }

    private function extractClasses(array $tm): string
    {
        if (!empty($tm['goodsAndServices']) && is_array($tm['goodsAndServices'])) {
            $classes = array_column($tm['goodsAndServices'], 'class');
            if (empty(array_filter($classes))) {
                $classes = array_column($tm['goodsAndServices'], 'classNumber');
            }
            return implode(', ', array_unique(array_filter($classes)));
        }
        if (!empty($tm['classes'])) {
            return implode(', ', (array) $tm['classes']);
        }
        return $tm['class'] ?? '';
    }
}
