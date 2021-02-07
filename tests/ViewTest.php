<?php

namespace Cratespace\Sentinel;

use Illuminate\Http\JsonResponse;
use Cratespace\Sentinel\Sentinel\View;
use Cratespace\Sentinel\Tests\TestCase;
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
