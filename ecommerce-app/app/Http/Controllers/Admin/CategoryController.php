<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Services\Contracts\CategoryServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryServiceInterface $categoryService
    ) {}

    public function index(Request $request): View
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 15);

        $categories = $this->categoryService->getPaginatedCategories($perPage, $search);

        return view('admin.categories.index', compact('categories', 'search'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        try {
            $this->categoryService->createCategory($request->validated());

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Categoría creada exitosamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear la categoría: ' . $e->getMessage());
        }
    }

    public function edit(int $id): View
    {
        $category = $this->categoryService->getCategoryById($id);

        if (!$category) {
            abort(404, 'Categoría no encontrada');
        }

        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, int $id): RedirectResponse
    {
        try {
            $result = $this->categoryService->updateCategory($id, $request->validated());

            if (!$result) {
                return back()
                    ->withInput()
                    ->with('error', 'Categoría no encontrada.');
            }

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Categoría actualizada exitosamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar la categoría: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $result = $this->categoryService->deleteCategory($id);

            if (!$result) {
                return back()->with('error', 'Categoría no encontrada.');
            }

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Categoría eliminada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar la categoría: ' . $e->getMessage());
        }
    }

    public function toggleStatus(int $id): RedirectResponse
    {
        try {
            $result = $this->categoryService->toggleStatus($id);

            if (!$result) {
                return back()->with('error', 'Categoría no encontrada.');
            }

            return back()->with('success', 'Estado actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar el estado: ' . $e->getMessage());
        }
    }
}
