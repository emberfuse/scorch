<?php

namespace Cratespace\Citadel;

use Illuminate\Http\JsonResponse;
use Cratespace\Citadel\Citadel\View;
use Cratespace\Citadel\Tests\TestCase;
use Illuminate\Contracts\Support\Responsable;

class ViewTest extends TestCase
{
    public function testViewsCanBeCustomized()
    {
        View::login(fn () => 'foo');

        $response = $this->get('/login');

        $response->assertOk();
        $this->assertSame('foo', $response->content());
    }

    public function testCustomizedViewsCanReturnTheirOwnResponsable()
    {
        View::login(function () {
            return new class() implements Responsable {
                public function toResponse($request)
                {
                    return new JsonResponse(['foo' => 'bar']);
                }
            };
        });

        $response = $this->get('/login');

        $response->assertOk();
        $response->assertExactJson(['foo' => 'bar']);
    }
}
