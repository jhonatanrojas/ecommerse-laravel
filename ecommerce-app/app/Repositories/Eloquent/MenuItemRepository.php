<?php

namespace App\Repositories\Eloquent;

use App\Models\MenuItem;
use App\Repositories\Contracts\MenuItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MenuItemRepository implements MenuItemRepositoryInterface
{
    protected $model;

    public function __construct(MenuItem $model)
    {
        $this->model = $model;
    }

    public function find($id): ?MenuItem
    {
        return $this->model->find($id);
    }

    public function findByUuid($uuid): ?MenuItem
    {
        return $this->model->where('uuid', $uuid)->first();
    }

    public function getByMenu($menuId): Collection
    {
        return $this->model->where('menu_id', $menuId)->orderBy('order')->get();
    }

    public function create(array $data): MenuItem
    {
        return $this->model->create($data);
    }

    public function update($id, array $data): bool
    {
        $item = $this->find($id);
        if ($item) {
            return $item->update($data);
        }
        return false;
    }

    public function delete($id): bool
    {
        $item = $this->find($id);
        if ($item) {
            return $item->delete();
        }
        return false;
    }

    public function updateOrder(array $items): void
    {
        foreach ($items as $itemData) {
            // Check if finding by id or uuid. Assuming id since updateOrder usually passes primary keys internally or logic uses model binding.
            // But frontend usually uses uuid. Let's assume input has id (PK) or we need to find by uuid if provided.
            // Given the task says "updateOrder($orderData) -> Para drag & drop", and item data likely comes from frontend which has uuids.
            // However, typical sortable libraries might send IDs if we bind them.
            // Let's assume ID is passed for simplicity as eloquent `find` uses PK. 
            // If UUID is needed, we would use `findByUuid`.
            // But let's check input structure. Usually it's an array of items with new order/parent.
            // Checking the implementation:
            $item = $this->model->find($itemData['id']);
            if ($item) {
                $item->update([
                    'parent_id' => $itemData['parent_id'] ?? null,
                    'order' => $itemData['order'],
                    'depth' => $itemData['depth'] ?? 0,
                ]);
            }
        }
    }
}
