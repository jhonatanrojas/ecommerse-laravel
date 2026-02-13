<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPageController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $perPage = (int) $request->get('per_page', 15);

        $pages = Page::query()
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.pages.index', compact('pages', 'search'));
    }

    public function create(): View
    {
        return view('admin.pages.create');
    }

    public function store(StorePageRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $adminId = auth('admin')->id();

            $data['created_by'] = $adminId;
            $data['updated_by'] = $adminId;
            $data['published_at'] = $data['is_published'] ? now() : null;

            Page::query()->create($data);

            return redirect()
                ->route('admin.pages.index')
                ->with('success', 'Página creada exitosamente.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear la página: ' . $e->getMessage());
        }
    }

    public function edit(string $uuid): View
    {
        $page = $this->findByUuid($uuid);

        return view('admin.pages.edit', compact('page'));
    }

    public function update(UpdatePageRequest $request, string $uuid): RedirectResponse
    {
        try {
            $page = $this->findByUuid($uuid);
            $data = $request->validated();

            $data['updated_by'] = auth('admin')->id();
            $data['published_at'] = $data['is_published']
                ? ($page->published_at ?: now())
                : null;

            $page->update($data);

            return redirect()
                ->route('admin.pages.index')
                ->with('success', 'Página actualizada exitosamente.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar la página: ' . $e->getMessage());
        }
    }

    public function togglePublish(string $uuid): RedirectResponse
    {
        try {
            $page = $this->findByUuid($uuid);
            $isPublishing = ! $page->is_published;

            $page->update([
                'is_published' => $isPublishing,
                'published_at' => $isPublishing ? now() : null,
                'updated_by' => auth('admin')->id(),
            ]);

            return back()->with(
                'success',
                $isPublishing ? 'Página publicada exitosamente.' : 'Página despublicada exitosamente.'
            );
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al cambiar el estado de publicación: ' . $e->getMessage());
        }
    }

    public function destroy(string $uuid): RedirectResponse
    {
        try {
            $page = $this->findByUuid($uuid);
            $page->delete();

            return redirect()
                ->route('admin.pages.index')
                ->with('success', 'Página eliminada exitosamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al eliminar la página: ' . $e->getMessage());
        }
    }

    protected function findByUuid(string $uuid): Page
    {
        return Page::query()->where('uuid', $uuid)->firstOrFail();
    }
}
