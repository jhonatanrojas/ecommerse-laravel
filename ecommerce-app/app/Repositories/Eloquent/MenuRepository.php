<?php

namespace App\Repositories\Eloquent;

use App\Models\Menu;
use App\Repositories\Contracts\MenuRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MenuRepository implements MenuRepositoryInterface
{
    protected $model;

    public function __construct(Menu $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find($id): ?Menu
    {
        return $this->model->find($id);
    }

    public function findByUuid($uuid): ?Menu
    {
        return $this->model->where('uuid', $uuid)->first();
    }

    public function findByKey($key): ?Menu
    {
        return $this->model->where('key', $key)->first();
    }

    public function findByLocation($location): ?Menu
    {
        return $this->model->byLocation($location)->first();
    }

    public function getActive(): Collection
    {
        return $this->model->active()->get();
    }

    public function create(array $data): Menu
    {
        return $this->model->create($data);
    }

    public function update($id, array $data): bool
    {
        $menu = $this->find($id);
        if ($menu) {
            return $menu->update($data);
        }
        return false;
    }

    public function delete($id): bool
    {
        $menu = $this->find($id);
        if ($menu) {
            return $menu->delete();
        }
        return false;
    }
}
