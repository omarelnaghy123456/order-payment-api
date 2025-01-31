<?php

namespace App\Repositories\Contracts;

interface BaseContract
{
    public function all();
    public function create(array $data);
    public function update(array $data, $id);
    public function delete($id);
    public function find($id);
    public function findBy(array $criteria);
    public function paginate($perPage = 15);
}