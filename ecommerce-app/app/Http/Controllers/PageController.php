<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Page;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(): View
    {
        $pages = Page::query()
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(12);

        return view('pages.index', [
            'pages' => $pages,
            'headerMenuItems' => $this->menuItemsByLocation('header'),
            'footerMenuItems' => $this->menuItemsByLocation('footer'),
        ]);
    }

    public function show(string $slug): View
    {
        $page = Page::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('pages.show', [
            'page' => $page,
            'headerMenuItems' => $this->menuItemsByLocation('header'),
            'footerMenuItems' => $this->menuItemsByLocation('footer'),
        ]);
    }

    protected function menuItemsByLocation(string $location): Collection
    {
        $menu = Menu::query()
            ->active()
            ->byLocation($location)
            ->with(['items' => function ($query) {
                $query->where('is_active', true)
                    ->with(['children' => function ($subQuery) {
                        $subQuery->where('is_active', true)->orderBy('order');
                    }])
                    ->orderBy('order');
            }])
            ->first();

        return $menu?->items ?? collect();
    }
}
