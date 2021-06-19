<?php

namespace Emberfuse\Scorch\Contracts\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface HasApiTokens
{
    /**
     * Get the access tokens that belong to model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function tokens(): MorphMany;

    /**
     * Determine if the current API token has a given scope.
     *
     * @param string $ability
     *
     * @return bool
     */
    public function tokenCan(string $ability): bool;

    /**
     * Create a new personal access token for the user.
     *
     * @param string $name
     * @param array  $abilities
     *
     * @return mixed
     */
    public function createToken(string $name, array $abilities = ['*']);

    /**
     * Get the access token currently associated with the user.
     *
     * @return \Emberfuse\Scorch\Contracts\Auth\Access
     */
    public function currentAccessToken(): Access;

    /**
     * Set the current access token for the user.
     *
     * @param \Emberfuse\Scorch\Contracts\Auth\Access $accessToken
     *
     * @return $this
     */
    public function withAccessToken(Access $accessToken): Model;
}
