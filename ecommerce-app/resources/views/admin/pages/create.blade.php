@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.pages.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
        ← Volver a páginas
    </a>
    <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Crear Página</h1>
</div>

<div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">
    <form action="{{ route('admin.pages.store') }}" method="POST">
        @csrf
        @include('admin.pages._form', ['submitButtonText' => 'Crear Página'])
    </form>
</div>
@endsection
