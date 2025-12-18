<x-layouts.app>
    <x-slot name="title">Quote Request #{{ $quoteRequest->id }} - Parts Sync Platform</x-slot>

    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
            ← Back to Dashboard
        </a>
    </div>

    <livewire:quote-request-show :quoteRequest="$quoteRequest" />
</x-layouts.app>
