<?php

namespace App\Repositories\Contracts;

interface MenuItemRepositoryInterface
{
    public function find($id);
    public function findByUuid($uuid);
    public function getByMenu($menuId);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function updateOrder(array $orderData);
}
