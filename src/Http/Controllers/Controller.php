<?php

namespace Cratespace\Sentinel\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Cratespace\Sentinel\Http\Controllers\Concerns\InteractsWithContainer;

abstract class Controller extends BaseController
{
    use InteractsWithContainer;
}
