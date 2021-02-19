<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Cratespace\Sentinel\Contracts\Actions\CreatesNewUsers;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableUser;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Create a newly registered user.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function create(array $data): AuthenticatableUser
    {
        return DB::transaction(function () use ($data) {
            return tap($this->createUser($data), function (AuthenticatableUser $user) {
                return $user;
            });
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
            'username' => $this->makeUsername($data['name']),
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Generate unique username from first name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function makeUsername(string $name): string
    {
        $name = trim($name);

        if (User::where('username', 'like', '%' . $name . '%')->count() !== 0) {
            return Str::studly("{$name}-" . Str::random('5'));
        }

        return Str::studly($name);
    }
}
