<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\IPAustraliaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __construct(private IPAustraliaService $ipAustralia) {}

    public function index(): View
    {
        return view('public.search');
    }

    public function results(Request $request): View
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $query    = trim($request->input('q'));
        $data     = $this->ipAustralia->search($query);
        $results  = $data['results'] ?? [];
        $total    = $data['total']   ?? count($results);

        return view('public.results', compact('query', 'results', 'total'));
    }
}
