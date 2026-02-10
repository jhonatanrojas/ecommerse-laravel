<?php

namespace App\Services\Menu;

use App\Repositories\Contracts\MenuItemRepositoryInterface;
use App\Repositories\Contracts\MenuRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class MenuService
{
    protected $menuRepository;
    protected $menuItemRepository;

    public function __construct(
        MenuRepositoryInterface $menuRepository,
        MenuItemRepositoryInterface $menuItemRepository
        )
    {
        $this->menuRepository = $menuRepository;
        $this->menuItemRepository = $menuItemRepository;
    }

    public function getByLocation(string $location)
    {
        return Cache::tags(['menus'])->remember("menu.location.{$location}", 3600, function () use ($location) {
            $menu = $this->menuRepository->findByLocation($location);
            if (!$menu || !$menu->is_active) {
                return null;
            }
            return $this->buildMenuTree($menu);
        });
    }

    public function getByKey(string $key)
    {
        return Cache::tags(['menus'])->remember("menu.key.{$key}", 3600, function () use ($key) {
            $menu = $this->menuRepository->findByKey($key);
            if (!$menu || !$menu->is_active) {
                return null;
            }
            return $this->buildMenuTree($menu);
        });
    }

    public function clearCache(?string $key = null)
    {
        Cache::tags(['menus'])->flush();
    }

    public function reorderItems(array $items)
    {
        $this->menuItemRepository->updateOrder($items);
        $this->clearCache();
    }

    public function buildMenuTree($menu, $items = null)
    {
        if (is_null($items)) {
            $items = $this->menuItemRepository->getByMenu($menu->id);
        }

        $menu->setRelation('items', $this->buildTree($items));

        return $menu;
    }

    protected function buildTree($items, $parentId = null)
    {
        $branch = [];

        foreach ($items as $item) {
            if ($item->parent_id == $parentId) {
                $children = $this->buildTree($items, $item->id);
                if ($children) {
                    $item->setRelation('children', collect($children));
                }
                else {
                    $item->setRelation('children', collect([]));
                }
                $branch[] = $item;
            }
        }

        return collect($branch);
    }
}
