<?php

namespace Cratespace\Sentinel\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Cratespace\Sentinel\Support\Concerns\InteractsWithContainer;

abstract class Controller extends BaseController
{
    use InteractsWithContainer;
}
