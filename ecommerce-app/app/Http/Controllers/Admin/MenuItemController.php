<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMenuItemRequest;
use App\Http\Requests\Admin\UpdateMenuItemRequest;
use App\Repositories\Contracts\MenuItemRepositoryInterface;
use App\Repositories\Contracts\MenuRepositoryInterface;
use App\Services\Menu\MenuService;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    protected $menuItemRepository;
    protected $menuRepository;
    protected $menuService;

    public function __construct(
        MenuItemRepositoryInterface $menuItemRepository,
        MenuRepositoryInterface $menuRepository,
        MenuService $menuService
        )
    {
        $this->menuItemRepository = $menuItemRepository;
        $this->menuRepository = $menuRepository;
        $this->menuService = $menuService;
    }

    public function store(StoreMenuItemRequest $request)
    {
        $data = $request->validated();

        $item = $this->menuItemRepository->create($data);

        return back()->with('success', 'Item added successfully.');
    }

    public function update(UpdateMenuItemRequest $request, $uuid)
    {
        $item = $this->menuItemRepository->findByUuid($uuid);
        if (!$item) {
            abort(404);
        }

        $this->menuItemRepository->update($item->id, $request->validated());

        return back()->with('success', 'Item updated successfully.');
    }

    public function destroy($uuid)
    {
        $item = $this->menuItemRepository->findByUuid($uuid);
        if ($item) {
            $this->menuItemRepository->delete($item->id);
        }
        return back()->with('success', 'Item deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required',
            'items.*.order' => 'required|integer',
            'items.*.parent_id' => 'nullable|exists:menu_items,id',
            'items.*.depth' => 'required|integer',
        ]);

        $this->menuService->reorderItems($request->input('items'));

        return response()->json(['success' => true, 'message' => 'Menu order updated.']);
    }

    public function toggle($uuid)
    {
        $item = $this->menuItemRepository->findByUuid($uuid);
        if ($item) {
            $this->menuItemRepository->update($item->id, ['is_active' => !$item->is_active]);
        }
        return back()->with('success', 'Item status updated.');
    }
}
