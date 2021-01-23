<?php

namespace Citadel\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Citadel\Http\Controllers\Concerns\InteractsWithContainer;

abstract class Controller extends BaseController
{
    use InteractsWithContainer;
}
