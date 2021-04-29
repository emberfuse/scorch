<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Cratespace\Sentinel\Support\Util;
use Cratespace\Sentinel\Contracts\Actions\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Create a newly registered user.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return $this->createUser($data);
        });
    }

    /**
     * Create new user profile.
     *
     * @param array $data
     *
     * @return \App\Models\User
     */
    protected function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'username' => Util::makeUsername($data['name']),
            'password' => Hash::make($data['password']),
        ]);
    }
}
