<?php

namespace App\Repositories;

use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;

class AuthRepository implements AuthRepositoryInterface
{
    public function getAll()
    {
        return User::all();
    }
    public function getById($id)
    {
        return User::findOrFail($id);
    }
    public function store(array $data)
    {
        return User::create($data);
    }
    public function update(array $data, $id)
    {
        return User::whereId($id)->update($data);
    }
    public function delete($id)
    {
        return User::destroy($id);

    }
}
