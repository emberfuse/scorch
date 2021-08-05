<?php

namespace Emberfuse\Scorch\Http\Requests;

use Emberfuse\Scorch\Http\Requests\Concerns\AuthorizesRequests;
use Emberfuse\Scorch\Http\Requests\Traits\HasCustomValidator;
use Emberfuse\Scorch\Http\Requests\Traits\InputValidationRules;
use Emberfuse\Scorch\Support\Concerns\InteractsWithContainer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Traits\Tappable;

abstract class Request extends FormRequest
{
    use AuthorizesRequests;
    use InputValidationRules;
    use HasCustomValidator;
    use InteractsWithContainer;
    use Tappable;
}
