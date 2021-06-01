<?php

namespace Emberfuse\Scorch\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Emberfuse\Scorch\Support\Concerns\InteractsWithContainer;

abstract class Controller extends BaseController
{
    use InteractsWithContainer;
}
