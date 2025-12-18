<div class="space-y-6">
    <div class="bg-white shadow-sm rounded-lg p-6">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Quote Request #{{ $quoteRequest->id }}</h2>
                <p class="mt-1 text-sm text-gray-500">Created {{ $quoteRequest->created_at->diffForHumans() }}</p>
            </div>
            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium
                {{ $quoteRequest->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                {{ $quoteRequest->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                {{ $quoteRequest->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $quoteRequest->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                {{ ucfirst($quoteRequest->status) }}
            </span>
        </div>

        <div class="mt-6 border-t border-gray-200 pt-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Buyer Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $quoteRequest->buyer_name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Buyer Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $quoteRequest->buyer_email }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Part Description</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $quoteRequest->part_description }}</dd>
                </div>
                @if($quoteRequest->vehicle_info)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Vehicle Information</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $quoteRequest->vehicle_info['year'] ?? '' }}
                            {{ $quoteRequest->vehicle_info['make'] ?? '' }}
                            {{ $quoteRequest->vehicle_info['model'] ?? '' }}
                        </dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    @if($bestResponse)
        <div class="bg-green-50 border-2 border-green-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-green-900 mb-2">Best Quote</h3>
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-2xl font-bold text-green-700">${{ number_format($bestResponse->quoted_price, 2) }}</p>
                    <p class="text-sm text-green-600">from {{ $bestResponse->supplier->name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-green-700">Stock: {{ $bestResponse->stock_available ?? 'N/A' }}</p>
                    <p class="text-sm text-green-600">Response time: {{ $bestResponse->response_time_seconds }}s</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            Supplier Responses ({{ $responses->count() }})
            <span wire:loading class="ml-2 text-sm text-blue-600">Updating...</span>
        </h3>

        @if($responses->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Waiting for responses</h3>
                <p class="mt-1 text-sm text-gray-500">Suppliers are being contacted...</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($responses as $response)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <h4 class="text-base font-semibold text-gray-900">{{ $response->supplier->name }}</h4>
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                        {{ $response->supplier->integration_type === 'database' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $response->supplier->integration_type === 'api' ? 'bg-purple-100 text-purple-700' : '' }}
                                        {{ $response->supplier->integration_type === 'manual' ? 'bg-orange-100 text-orange-700' : '' }}">
                                        {{ ucfirst($response->supplier->integration_type) }}
                                    </span>
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium
                                        {{ $response->status === 'received' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $response->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                        {{ $response->status === 'timeout' ? 'bg-red-100 text-red-700' : '' }}">
                                        {{ ucfirst($response->status) }}
                                    </span>
                                </div>
                                @if($response->quoted_price)
                                    <p class="mt-2 text-2xl font-bold text-gray-900">${{ number_format($response->quoted_price, 2) }}</p>
                                @endif
                                @if($response->notes)
                                    <p class="mt-1 text-sm text-gray-600">{{ $response->notes }}</p>
                                @endif
                            </div>
                            <div class="text-right ml-4">
                                @if($response->stock_available)
                                    <p class="text-sm text-gray-700">Stock: {{ $response->stock_available }}</p>
                                @endif
                                @if($response->response_time_seconds)
                                    <p class="text-sm text-gray-500">{{ $response->response_time_seconds }}s</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
