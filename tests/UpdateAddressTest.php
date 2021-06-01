<?php

namespace Emberfuse\Scorch\Tests;

use Mockery as m;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use App\Actions\Auth\UpdateUserAddress;
use Emberfuse\Scorch\Tests\Traits\HasUserAttributes;

class UpdateAddressTest extends TestCase
{
    use HasUserAttributes;

    public function setUp(): void
    {
        parent::setUp();

        Gate::policy(User::class, UserPolicy::class);
    }

    public function testAddressInformationCanBeUpdated()
    {
        $user = m::mock(User::class);

        $updater = $this->mock(UpdateUserAddress::class);

        $updater->shouldReceive('update')
            ->once()
            ->with($user, m::type('array'));

        $result = $updater->update($user, [
            'line1' => '4431 Birch Street',
            'line2' => $data['line2'] ?? null,
            'city' => 'Greenwood',
            'state' => 'Indiana',
            'country' => 'United States',
            'postal_code' => '46142',
        ]);

        $this->assertNull($result);
    }
}
