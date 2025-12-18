<?php

namespace App\Livewire;

use App\Models\QuoteRequest;
use App\Models\QuoteResponse;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $supplierStats = Supplier::withCount('quoteResponses')
            ->with(['quoteResponses' => function ($query) {
                $query->where('status', 'received');
            }])
            ->get()
            ->map(function ($supplier) {
                $responses = $supplier->quoteResponses;
                return [
                    'name' => $supplier->name,
                    'integration_type' => $supplier->integration_type,
                    'total_responses' => $responses->count(),
                    'avg_response_time' => $responses->avg('response_time_seconds'),
                    'avg_price' => $responses->whereNotNull('quoted_price')->avg('quoted_price'),
                ];
            });

        $recentQuotes = QuoteRequest::with('responses')
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'total_requests' => QuoteRequest::count(),
            'total_responses' => QuoteResponse::where('status', 'received')->count(),
            'avg_responses_per_request' => QuoteRequest::avg('responses_count'),
            'suppliers_count' => Supplier::where('is_active', true)->count(),
        ];

        return view('livewire.dashboard', [
            'supplierStats' => $supplierStats,
            'recentQuotes' => $recentQuotes,
            'stats' => $stats,
        ]);
    }
}
