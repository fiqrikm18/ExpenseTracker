@extends('layouts.dashboard')

@section('title')
    Dashboard
@endsection

@section('content')
<h1 class="text-2xl font-bold mb-4">Welcome to your Dashboard</h1>
<p class="text-gray-600 mb-4">Here’s a quick overview of what’s going on.</p>

<!-- Example stats section -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <div class="p-4 bg-blue-100 rounded-xl">
        <p class="text-sm text-blue-800">Users</p>
        <p class="text-2xl font-bold text-blue-900">1,200</p>
    </div>
    <div class="p-4 bg-green-100 rounded-xl">
        <p class="text-sm text-green-800">Sales</p>
        <p class="text-2xl font-bold text-green-900">$9,400</p>
    </div>
    <div class="p-4 bg-yellow-100 rounded-xl">
        <p class="text-sm text-yellow-800">Orders</p>
        <p class="text-2xl font-bold text-yellow-900">320</p>
    </div>
</div>
@endsection
