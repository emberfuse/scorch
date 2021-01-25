<?php

namespace Cratespace\Citadel\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Cratespace\Citadel\Http\Controllers\Concerns\InteractsWithContainer;

abstract class Controller extends BaseController
{
    use InteractsWithContainer;
}
