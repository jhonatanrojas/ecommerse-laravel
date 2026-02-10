@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
        <a href="{{ route('admin.users.index') }}" class="hover:text-blue-600">Usuarios</a>
        <span>/</span>
        <span>Crear Usuario</span>
    </div>
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Crear Nuevo Usuario</h1>
</div>

<div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg">
    <form action="{{ route('admin.users.store') }}" method="POST" class="p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nombre -->
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nombre Completo <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('name') border-red-500 @enderror" required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('email') border-red-500 @enderror" required>
                @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Teléfono -->
            <div>
                <label for="phone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Teléfono
                </label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('phone') border-red-500 @enderror">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Estado -->
            <div>
                <label for="is_active" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Estado
                </label>
                <select name="is_active" id="is_active" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Contraseña <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password" id="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('password') border-red-500 @enderror" required>
                @error('password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirmar Contraseña -->
            <div>
                <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Confirmar Contraseña <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
            </div>
        </div>

        <!-- Roles -->
        <div class="mt-6">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                Roles <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($roles as $role)
                    <div class="flex items-center p-3 border border-gray-300 rounded-lg dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <input type="checkbox" name="roles[]" id="role_{{ $role->id }}" value="{{ $role->id }}" {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="role_{{ $role->id }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
                            {{ ucfirst($role->name) }}
                        </label>
                    </div>
                @endforeach
            </div>
            @error('roles')
                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Permisos Individuales (Opcional) -->
        <div class="mt-6">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                Permisos Adicionales (Opcional)
            </label>
            <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-lg p-3 dark:border-gray-600">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach($permissions as $permission)
                        <div class="flex items-center">
                            <input type="checkbox" name="permissions[]" id="permission_{{ $permission->id }}" value="{{ $permission->id }}" {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="permission_{{ $permission->id }}" class="ml-2 text-sm text-gray-900 dark:text-gray-300 cursor-pointer">
                                {{ $permission->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex items-center gap-3 mt-6 pt-6 border-t dark:border-gray-700">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                Crear Usuario
            </button>
            <a href="{{ route('admin.users.index') }}" class="text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-600 dark:hover:bg-gray-700 dark:text-white">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
