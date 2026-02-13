<x-guest-layout>
<div class="w-full max-w-2xl mx-auto py-4">
    <h1 class="text-2xl font-bold mb-6">Registro de vendedor</h1>
    <form method="POST" action="{{ route('vendor.register.submit') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        <input name="name" placeholder="Nombre" class="border rounded px-3 py-2" required>
        <input name="email" type="email" placeholder="Email" class="border rounded px-3 py-2" required>
        <input name="password" type="password" placeholder="Contraseña" class="border rounded px-3 py-2" required>
        <input name="password_confirmation" type="password" placeholder="Confirmar contraseña" class="border rounded px-3 py-2" required>
        <input name="business_name" placeholder="Nombre comercial" class="border rounded px-3 py-2 md:col-span-2" required>
        <input name="document" placeholder="Documento / RIF" class="border rounded px-3 py-2" required>
        <input name="phone" placeholder="Teléfono" class="border rounded px-3 py-2">
        <textarea name="address" placeholder="Dirección" class="border rounded px-3 py-2 md:col-span-2"></textarea>
        <select name="payout_cycle" class="border rounded px-3 py-2 md:col-span-2">
            <option value="manual">Liquidación manual</option>
            <option value="weekly">Semanal</option>
            <option value="monthly">Mensual</option>
        </select>
        <button class="bg-black text-white py-2 rounded md:col-span-2">Enviar solicitud</button>
    </form>
</div>
</x-guest-layout>
