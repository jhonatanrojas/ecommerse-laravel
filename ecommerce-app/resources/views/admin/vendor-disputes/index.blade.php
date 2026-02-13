@extends('layouts.admin')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Disputas de vendedores</h1>
<div class="space-y-4">
    @foreach($disputes as $dispute)
        <div class="bg-white rounded border p-4">
            <div class="flex justify-between">
                <div>
                    <h2 class="font-semibold">{{ $dispute->title }}</h2>
                    <p class="text-sm text-gray-500">{{ $dispute->vendor->business_name }} | {{ $dispute->created_at }}</p>
                </div>
                <span class="text-sm">{{ $dispute->status }}</span>
            </div>
            <p class="mt-2">{{ $dispute->description }}</p>
            <form method="POST" action="{{ route('admin.vendor-disputes.update', $dispute) }}" class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-2">
                @csrf @method('PATCH')
                <select name="status" class="border rounded px-3 py-2">
                    @foreach(['open','in_review','resolved','rejected'] as $status)
                        <option value="{{ $status }}" @selected($status === $dispute->status)>{{ $status }}</option>
                    @endforeach
                </select>
                <input name="admin_response" value="{{ $dispute->admin_response }}" class="border rounded px-3 py-2 md:col-span-2" placeholder="Respuesta admin">
                <button class="px-4 py-2 bg-black text-white rounded md:col-span-3">Actualizar disputa</button>
            </form>
        </div>
    @endforeach
</div>
<div class="mt-4">{{ $disputes->links() }}</div>
@endsection
