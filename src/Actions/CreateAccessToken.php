<?php

namespace Emberfuse\Scorch\Actions;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Emberfuse\Scorch\API\Tokens\PersonalAccessToken;

class CreateAccessToken implements Arrayable, Jsonable
{
    /**
     * The access token instance.
     *
     * @var \Emberfuse\Scorch\API\Tokens\PersonalAccessToken
     */
    public $accessToken;

    /**
     * The plain text version of the token.
     *
     * @var string
     */
    public $plainTextToken;

    /**
     * Create a new access token result.
     *
     * @param \Emberfuse\Scorch\API\Tokens\PersonalAccessToken $accessToken
     * @param string                                              $plainTextToken
     *
     * @return void
     */
    public function __construct(PersonalAccessToken $accessToken, string $plainTextToken)
    {
        $this->accessToken = $accessToken;
        $this->plainTextToken = $plainTextToken;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'accessToken' => $this->accessToken,
            'plainTextToken' => $this->plainTextToken,
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}
