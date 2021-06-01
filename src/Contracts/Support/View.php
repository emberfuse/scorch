<?php

namespace Emberfuse\Scorch\Contracts\Support;

interface View
{
    /**
     * Register given view response.
     *
     * @param string          $viewResponse
     * @param \Closure|string $view
     *
     * @return void
     */
    public static function registerView(string $viewResponse, $view): void;
}
