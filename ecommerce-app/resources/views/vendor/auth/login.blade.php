<x-guest-layout>
<div class="w-full">
    <h1 class="text-2xl font-bold mb-6">Acceso vendedor</h1>
    <form method="POST" action="{{ route('vendor.login.submit') }}" class="space-y-4">
        @csrf
        <input name="email" type="email" placeholder="Email" class="w-full border rounded px-3 py-2" required>
        <input name="password" type="password" placeholder="Contraseña" class="w-full border rounded px-3 py-2" required>
        <label class="text-sm"><input type="checkbox" name="remember"> Recordarme</label>
        <button class="w-full bg-black text-white py-2 rounded">Ingresar</button>
    </form>
    <p class="mt-4 text-sm">¿Sin cuenta? <a class="text-blue-600" href="{{ route('vendor.register') }}">Regístrate</a></p>
</div>
</x-guest-layout>
