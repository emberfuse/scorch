<?php

namespace Emberfuse\Scorch\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Emberfuse\Scorch\Models\Traits\Redirectable;

class MockModel extends Model
{
    use Redirectable;

    protected $table = 'mock';

    /**
     * Route name of single resource.
     *
     * @var string
     */
    protected $resourceView = 'show';

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id';
    }
}
