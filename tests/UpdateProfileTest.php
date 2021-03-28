<?php

namespace Cratespace\Sentinel\Tests;

use Mockery as m;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Cratespace\Sentinel\Jobs\DeleteUserJob;
use Illuminate\Contracts\Auth\Authenticatable;
use Cratespace\Sentinel\Tests\Traits\HasUserAttributes;
use Cratespace\Sentinel\Contracts\Actions\UpdatesUserProfiles;
use Cratespace\Sentinel\Contracts\Responses\UserProfileViewResponse;

class UpdateProfileTest extends TestCase
{
    use HasUserAttributes;

    public function setUp(): void
    {
        parent::setUp();

        Gate::policy(User::class, UserPolicy::class);
    }

    public function testTheUserProfileViewIsReturned()
    {
        $user = m::mock(Authenticatable::class);

        $this->mock(UserProfileViewResponse::class)
            ->shouldReceive('toResponse')
            ->andReturn(response('hello world'));

        $response = $this->actingAs($user)->get('/user/profile');

        $response->assertStatus(200);
        $response->assertSeeText('hello world');
    }

    public function testContactInformationCanBeUpdated()
    {
        $user = m::mock(User::class);

        $updater = $this->mock(UpdatesUserProfiles::class);

        $updater->shouldReceive('update')
            ->once()
            ->with($user, m::type('array'));

        $result = $updater->update($user, [
            'name' => 'James Silverman',
            'username' => 'SilverMonster',
            'email' => 'silver.james@monster.com',
        ]);

        $this->assertNull($result);
    }

    public function testUserAccountsCanBeDeleted()
    {
        $this->withoutExceptionHandling();

        $this->migrate();

        Queue::fake();

        $user = User::forceCreate($this->userDetails());

        $response = $this->actingAs($user)->delete('/user/profile', [
            'password' => 'cthuluEmployee',
        ]);

        Queue::assertPushed(fn (DeleteUserJob $job) => $job->getUser()->id === $user->id);

        $response->assertRedirect('/');
    }

    public function testCorrectPasswordMustBeProvidedBeforeAccountCanBeDeleted()
    {
        $this->migrate();

        Queue::fake();

        $user = User::forceCreate($this->userDetails());

        $response = $this->actingAs($user)
            ->delete('/user/profile', [
                'password' => 'wrong-password',
            ]);

        Queue::assertNotPushed(DeleteUserJob::class);

        $this->assertNotNull($user->fresh());
    }
}
