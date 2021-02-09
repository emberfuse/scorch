<?php

namespace Cratespace\Sentinel\Models\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Cratespace\Sentinel\Contracts\Auth\Access;
use Cratespace\Sentinel\Actions\CreateAccessToken;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Cratespace\Sentinel\Auth\Tokens\PersonalAccessToken;

trait HasApiTokens
{
    /**
     * The access token the user is using for the current request.
     *
     * @var \Cratespace\Sentinel\Contracts\Auth\Access
     */
    protected $accessToken;

    /**
     * Get the access tokens that belong to model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function tokens(): MorphMany
    {
        return $this->morphMany(PersonalAccessToken::class, 'tokenable');
    }

    /**
     * Determine if the current API token has a given scope.
     *
     * @param string $ability
     *
     * @return bool
     */
    public function tokenCan(string $ability): bool
    {
        return $this->accessToken ? $this->accessToken->can($ability) : false;
    }

    /**
     * Create a new personal access token for the user.
     *
     * @param string $name
     * @param array  $abilities
     *
     * @return \Cratespace\Sentinel\Actions\CreateAccessToken
     */
    public function createToken(string $name, array $abilities = ['*']): CreateAccessToken
    {
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(40)),
            'abilities' => $abilities,
        ]);

        return new CreateAccessToken($token, $token->id . '|' . $plainTextToken);
    }

    /**
     * Get the access token currently associated with the user.
     *
     * @return \Cratespace\Sentinel\Contracts\Auth\Access
     */
    public function currentAccessToken(): Access
    {
        return $this->accessToken;
    }

    /**
     * Set the current access token for the user.
     *
     * @param \Cratespace\Sentinel\Contracts\Auth\Access $accessToken
     *
     * @return $this
     */
    public function withAccessToken($accessToken): Model
    {
        $this->accessToken = $accessToken;

        return $this;
    }
}
