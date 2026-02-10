<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->get('search'),
            'role' => $request->get('role'),
            'status' => $request->get('status'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];

        $perPage = $request->get('per_page', 15);

        $users = $this->userService->getPaginatedUsers($perPage, $filters);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles', 'filters'));
    }

    public function create(): View
    {
        $roles = Role::all();
        $permissions = Permission::all();

        return view('admin.users.create', compact('roles', 'permissions'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            $this->userService->createUser($request->validated());

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Usuario creado exitosamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
    }

    public function edit(int $id): View
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            abort(404, 'Usuario no encontrado');
        }

        $roles = Role::all();
        $permissions = Permission::all();

        return view('admin.users.edit', compact('user', 'roles', 'permissions'));
    }

    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        try {
            $result = $this->userService->updateUser($id, $request->validated());

            if (!$result) {
                return back()
                    ->withInput()
                    ->with('error', 'Usuario no encontrado.');
            }

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Usuario actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $result = $this->userService->deleteUser($id);

            if (!$result) {
                return back()->with('error', 'Usuario no encontrado.');
            }

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Usuario eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }

    public function toggleStatus(int $id): RedirectResponse
    {
        try {
            $result = $this->userService->toggleUserStatus($id);

            if (!$result) {
                return back()->with('error', 'Usuario no encontrado.');
            }

            return back()->with('success', 'Estado del usuario actualizado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar el estado: ' . $e->getMessage());
        }
    }
}
