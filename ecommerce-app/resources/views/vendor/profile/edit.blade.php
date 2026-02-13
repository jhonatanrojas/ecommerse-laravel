@extends('layouts.vendor')

@section('content')
<h1 class="text-2xl font-bold mb-4">Perfil vendedor</h1>
<form method="POST" action="{{ route('vendor.profile.update') }}" class="bg-white border rounded p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
    @csrf
    @method('PUT')
    <input name="business_name" value="{{ old('business_name', $vendor->business_name) }}" class="border rounded px-3 py-2" placeholder="Nombre comercial" required>
    <input name="document" value="{{ old('document', $vendor->document) }}" class="border rounded px-3 py-2" placeholder="Documento" required>
    <input name="phone" value="{{ old('phone', $vendor->phone) }}" class="border rounded px-3 py-2" placeholder="Teléfono">
    <input name="email" value="{{ old('email', $vendor->email) }}" class="border rounded px-3 py-2" placeholder="Email" required>
    <textarea name="address" class="border rounded px-3 py-2 md:col-span-2" placeholder="Dirección">{{ old('address', $vendor->address) }}</textarea>
    <select name="payout_cycle" class="border rounded px-3 py-2">
        @foreach(['weekly' => 'Semanal', 'monthly' => 'Mensual', 'manual' => 'Manual'] as $value => $label)
            <option value="{{ $value }}" @selected(old('payout_cycle', $vendor->payout_cycle) === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <input name="payout_method[provider]" value="{{ old('payout_method.provider', data_get($vendor->payout_method,'provider')) }}" class="border rounded px-3 py-2" placeholder="Proveedor (manual/stripe/paypal)">
    <input name="payout_method[account]" value="{{ old('payout_method.account', data_get($vendor->payout_method,'account')) }}" class="border rounded px-3 py-2 md:col-span-2" placeholder="Cuenta">
    <input name="payout_method[beneficiary]" value="{{ old('payout_method.beneficiary', data_get($vendor->payout_method,'beneficiary')) }}" class="border rounded px-3 py-2 md:col-span-2" placeholder="Beneficiario">
    <button class="px-4 py-2 bg-black text-white rounded md:col-span-2">Guardar</button>
</form>
@endsection
