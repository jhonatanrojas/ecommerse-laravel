<?php

namespace App\Repositories\Contracts;

interface MenuRepositoryInterface
{
    public function all();
    public function find($id);
    public function findByUuid($uuid);
    public function findByKey($key);
    public function findByLocation($location);
    public function getActive();
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
