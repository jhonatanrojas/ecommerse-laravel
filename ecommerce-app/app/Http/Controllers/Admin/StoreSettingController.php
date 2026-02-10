<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStoreSettingRequest;
use App\Services\Contracts\StoreSettingServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Class StoreSettingController
 * 
 * Cumple con el Principio de Responsabilidad Única (SRP):
 * Solo se encarga de manejar las peticiones HTTP relacionadas con ajustes.
 * 
 * Cumple con el Principio de Inversión de Dependencias (DIP):
 * Depende de la abstracción StoreSettingServiceInterface en lugar de
 * una implementación concreta.
 */
class StoreSettingController extends Controller
{
    public function __construct(
        private StoreSettingServiceInterface $storeSettingService
    ) {}

    /**
     * Show the form for editing store settings.
     */
    public function edit(): View
    {
        $settings = $this->storeSettingService->getSettings();

        return view('admin.settings.store.edit', compact('settings'));
    }

    /**
     * Update store settings in storage.
     */
    public function update(UpdateStoreSettingRequest $request): RedirectResponse
    {
        try {
            $this->storeSettingService->updateSettings($request->validated());

            return redirect()
                ->route('admin.settings.store.edit')
                ->with('success', 'Ajustes de la tienda actualizados correctamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar los ajustes: ' . $e->getMessage());
        }
    }
}
