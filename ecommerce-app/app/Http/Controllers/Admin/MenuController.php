<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMenuRequest;
use App\Http\Requests\Admin\UpdateMenuRequest;
use App\Repositories\Contracts\MenuRepositoryInterface;
use App\Services\Menu\MenuService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    protected $menuRepository;
    protected $menuService;

    public function __construct(
        MenuRepositoryInterface $menuRepository,
        MenuService $menuService
        )
    {
        $this->menuRepository = $menuRepository;
        $this->menuService = $menuService;
    }

    public function index()
    {
        $menus = $this->menuRepository->all();
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        return view('admin.menus.edit');
    }

    public function store(StoreMenuRequest $request)
    {
        $menu = $this->menuRepository->create($request->validated());
        return redirect()->route('admin.menus.edit', $menu->uuid)->with('success', 'Menu created successfully.');
    }

    public function edit($uuid)
    {
        $menu = $this->menuRepository->findByUuid($uuid);
        if (!$menu) {
            abort(404);
        }

        // Load items for the menu
        $items = $menu->allItems;

        // Build tree to avoid N+1 in recursive view
        $this->menuService->buildMenuTree($menu, $items);

        return view('admin.menus.edit', compact('menu', 'items'));
    }

    public function update(UpdateMenuRequest $request, $uuid)
    {
        $menu = $this->menuRepository->findByUuid($uuid);
        if (!$menu) {
            abort(404);
        }

        $this->menuRepository->update($menu->id, $request->validated());
        return back()->with('success', 'Menu updated successfully.');
    }

    public function destroy($uuid)
    {
        $menu = $this->menuRepository->findByUuid($uuid);
        if ($menu) {
            $this->menuRepository->delete($menu->id);
        }
        return redirect()->route('admin.menus.index')->with('success', 'Menu deleted successfully.');
    }

    public function toggle($uuid)
    {
        $menu = $this->menuRepository->findByUuid($uuid);
        if ($menu) {
            $this->menuRepository->update($menu->id, ['is_active' => !$menu->is_active]);
        }
        return back()->with('success', 'Menu status updated.');
    }

    public function clearCache()
    {
        $this->menuService->clearCache();
        return back()->with('success', 'Menu cache cleared.');
    }
}
