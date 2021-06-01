<?php

namespace Emberfuse\Scorch\Http\Requests;

use Illuminate\Support\Traits\Tappable;
use Illuminate\Foundation\Http\FormRequest;
use Emberfuse\Scorch\Http\Requests\Traits\HasCustomValidator;
use Emberfuse\Scorch\Support\Concerns\InteractsWithContainer;
use Emberfuse\Scorch\Http\Requests\Concerns\AuthorizesRequests;
use Emberfuse\Scorch\Http\Requests\Traits\InputValidationRules;

abstract class Request extends FormRequest
{
    use AuthorizesRequests;
    use InputValidationRules;
    use HasCustomValidator;
    use InteractsWithContainer;
    use Tappable;
}
