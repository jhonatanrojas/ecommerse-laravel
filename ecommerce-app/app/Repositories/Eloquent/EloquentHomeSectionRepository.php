<?php

namespace App\Repositories\Eloquent;

use App\Models\HomeSection;
use App\Repositories\Contracts\HomeSectionRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentHomeSectionRepository implements HomeSectionRepositoryInterface
{
    public function getAllActive(): Collection
    {
        return HomeSection::active()->ordered()->get();
    }

    public function getById(int $id): HomeSection
    {
        return HomeSection::findOrFail($id);
    }

    public function create(array $data): HomeSection
    {
        return HomeSection::create($data);
    }

    public function update(int $id, array $data): HomeSection
    {
        $section = HomeSection::findOrFail($id);
        $section->update($data);
        return $section;
    }

    public function delete(int $id): bool
    {
        $section = HomeSection::findOrFail($id);
        // Eliminar los HomeSectionItem asociados antes de eliminar la HomeSection
        // Esto es importante si no se usa onDelete('cascade') en la migración
        // o si se necesita lógica adicional al eliminar los ítems.
        // Asumo que HomeSection tiene una relación 'items' con HomeSectionItem
        if ($section->items()->count() > 0) {
            $section->items()->delete();
        }
        return $section->delete();
    }

    public function reorder(array $sectionIds): void
    {
        foreach ($sectionIds as $index => $id) {
            HomeSection::where('id', $id)->update(['order' => $index + 1]);
        }
    }
}
