<?php

namespace Emberfuse\Scorch\Tests;

use stdClass;
use Mockery as m;
use DateTimeInterface;
use Illuminate\Http\Request;
use Emberfuse\Scorch\Auth\Guard;
use Illuminate\Auth\EloquentUserProvider;
use Emberfuse\Scorch\Models\PersonalAccessToken;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Emberfuse\Scorch\Tests\Traits\HasUserAttributes;
use Emberfuse\Scorch\Tests\Fixtures\TestAuthenticationUser;

class GuardTest extends TestCase
{
    use HasUserAttributes;

    public function testAuthenticationIsAttemptedWithWebMiddleware()
    {
        $factory = m::mock(AuthFactory::class);
        $guard = new Guard($factory, null, 'users');
        $webGuard = m::mock(stdClass::class);

        $factory->shouldReceive('guard')
            ->with('web')
            ->andReturn($webGuard);

        $webGuard->shouldReceive('user')->once()->andReturn(
            $fakeUser = new TestAuthenticationUser()
        );

        $user = $guard->__invoke(Request::create('/', 'GET'));

        $this->assertSame($user, $fakeUser);
        $this->assertTrue($user->tokenCan('foo'));
    }

    public function testAuthenticationIsAttemptedWithTokenIfNoSessionPresent()
    {
        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        $factory = m::mock(AuthFactory::class);

        $guard = new Guard($factory, null, 'users');

        $webGuard = m::mock(stdClass::class);

        $factory->shouldReceive('guard')
            ->with('web')
            ->andReturn($webGuard);

        $webGuard->shouldReceive('user')->once()->andReturn(null);

        $request = Request::create('/', 'GET');
        $request->headers->set('Authorization', 'Bearer test');

        $user = $guard->__invoke($request);

        $this->assertNull($user);
    }

    public function testAuthenticationWithTokenFailsIfExpired()
    {
        $this->migrate();

        $factory = m::mock(AuthFactory::class);

        $guard = new Guard($factory, 1, 'users');

        $webGuard = m::mock(stdClass::class);

        $factory->shouldReceive('guard')
            ->with('web')
            ->andReturn($webGuard);

        $webGuard->shouldReceive('user')->once()->andReturn(null);

        $request = Request::create('/', 'GET');
        $request->headers->set('Authorization', 'Bearer test');

        $user = TestAuthenticationUser::forceCreate($this->userDetails());

        $token = PersonalAccessToken::forceCreate([
            'tokenable_id' => $user->id,
            'tokenable_type' => get_class($user),
            'name' => 'Test',
            'token' => hash('sha256', 'test'),
            'created_at' => now()->subMinutes(60),
        ]);

        $user = $guard->__invoke($request);

        $this->assertNull($user);
    }

    public function testAuthenticationIsSuccessfulWithTokenIfNoSessionPresent()
    {
        $this->migrate();

        $factory = m::mock(AuthFactory::class);

        $guard = new Guard($factory, null);

        $webGuard = m::mock(stdClass::class);

        $factory->shouldReceive('guard')
            ->with('web')
            ->andReturn($webGuard);

        $webGuard->shouldReceive('user')->once()->andReturn(null);

        $request = Request::create('/', 'GET');
        $request->headers->set('Authorization', 'Bearer test');

        $user = TestAuthenticationUser::forceCreate($this->userDetails());

        $token = PersonalAccessToken::forceCreate([
            'tokenable_id' => $user->id,
            'tokenable_type' => get_class($user),
            'name' => 'Test',
            'token' => hash('sha256', 'test'),
        ]);

        $returnedUser = $guard->__invoke($request);

        $this->assertEquals($user->id, $returnedUser->id);
        $this->assertEquals($token->id, $returnedUser->currentAccessToken()->id);
        $this->assertInstanceOf(DateTimeInterface::class, $returnedUser->currentAccessToken()->last_used_at);
    }

    public function testAuthenticationWithTokenFailsIfUserProviderIsInvalid()
    {
        $this->migrate();

        config(['auth.guards.scorch.provider' => 'users']);
        config(['auth.providers.users.model' => 'App\Models\User']);

        $factory = $this->app->make(AuthFactory::class);
        $requestGuard = $factory->guard('scorch');

        $request = Request::create('/', 'GET');
        $request->headers->set('Authorization', 'Bearer test');

        $user = TestAuthenticationUser::forceCreate($this->userDetails());

        $token = PersonalAccessToken::forceCreate([
            'tokenable_id' => $user->id,
            'tokenable_type' => get_class($user),
            'name' => 'Test',
            'token' => hash('sha256', 'test'),
        ]);

        $returnedUser = $requestGuard->setRequest($request)->user();

        $this->assertNull($returnedUser);
        $this->assertInstanceOf(EloquentUserProvider::class, $requestGuard->getProvider());
    }

    public function testAuthenticationIsSuccessfulWithTokenIfUserProviderIsValid()
    {
        $this->migrate();

        config(['auth.guards.scorch.provider' => 'users']);
        config(['auth.providers.users.model' => TestAuthenticationUser::class]);

        $factory = $this->app->make(AuthFactory::class);
        $requestGuard = $factory->guard('scorch');

        $request = Request::create('/', 'GET');
        $request->headers->set('Authorization', 'Bearer test');

        $user = TestAuthenticationUser::forceCreate($this->userDetails());

        $token = PersonalAccessToken::forceCreate([
            'tokenable_id' => $user->id,
            'tokenable_type' => get_class($user),
            'name' => 'Test',
            'token' => hash('sha256', 'test'),
        ]);

        $returnedUser = $requestGuard->setRequest($request)->user();

        $this->assertEquals($user->id, $returnedUser->id);
        $this->assertInstanceOf(EloquentUserProvider::class, $requestGuard->getProvider());
    }
}
