<div class="space-y-6">
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Quote Requests</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($stats['total_requests']) }}</dd>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Responses</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($stats['total_responses']) }}</dd>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Avg Responses per Request</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($stats['avg_responses_per_request'], 1) }}</dd>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Active Suppliers</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($stats['suppliers_count']) }}</dd>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Supplier Performance</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Responses</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Response Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Price</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($supplierStats as $stat)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $stat['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                    {{ $stat['integration_type'] === 'database' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $stat['integration_type'] === 'api' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ $stat['integration_type'] === 'manual' ? 'bg-orange-100 text-orange-700' : '' }}">
                                    {{ ucfirst($stat['integration_type']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stat['total_responses'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $stat['avg_response_time'] ? number_format($stat['avg_response_time'], 2) . 's' : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $stat['avg_price'] ? '$' . number_format($stat['avg_price'], 2) : 'N/A' }}
                            </td>
                        </tr>
                    @endforeach
                    @if($supplierStats->isEmpty())
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No supplier data available</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Quote Requests</h3>
        <div class="space-y-4">
            @foreach($recentQuotes as $quote)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('quote-request.show', $quote) }}" class="text-base font-semibold text-primary-600 hover:text-primary-700">
                                    Quote #{{ $quote->id }}
                                </a>
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                    {{ $quote->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $quote->status === 'processing' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $quote->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $quote->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ ucfirst($quote->status) }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">{{ Str::limit($quote->part_description, 80) }}</p>
                            <p class="mt-1 text-sm text-gray-500">{{ $quote->buyer_name }} - {{ $quote->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-sm font-medium text-gray-900">{{ $quote->responses_count }} responses</p>
                        </div>
                    </div>
                </div>
            @endforeach
            @if($recentQuotes->isEmpty())
                <p class="text-center text-sm text-gray-500 py-8">No quote requests yet</p>
            @endif
        </div>
    </div>
</div>
