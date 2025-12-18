@extends('layouts.app')

@section('title', 'Dashboard - Parts Sync Platform')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-2 text-sm text-gray-700">Real-time parts sourcing analytics</p>
    </div>

    <livewire:dashboard />
@endsection
