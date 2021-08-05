<?php

namespace Emberfuse\Scorch\Http\Middleware;

use Closure;
use Emberfuse\Scorch\Scorch\Config;
use Illuminate\Http\Request;
use Illuminate\Routing\Pipeline;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EnsureFrontendRequestsAreStateful
{
    /**
     * Handle the incoming requests.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $this->configureSecureCookieSessions();

        return (new Pipeline(app()))
            ->send($request)
            ->through(static::fromFrontend($request) ? [
                    function ($request, $next) {
                        $request->attributes->set('scorch', true);

                        return $next($request);
                    },
                    config(
                        'scorch.middleware.encrypt_cookies',
                        \Illuminate\Cookie\Middleware\EncryptCookies::class
                    ),
                    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                    \Illuminate\Session\Middleware\StartSession::class,
                    config(
                        'scorch.middleware.verify_csrf_token',
                        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class
                    ),
                ] : [])
            ->then(function (Request $request) use ($next) {
                return $next($request);
            });
    }

    /**
     * Configure secure cookie sessions.
     *
     * @return void
     */
    protected function configureSecureCookieSessions(): void
    {
        config([
            'session.http_only' => true,
            'session.same_site' => 'lax',
        ]);
    }

    /**
     * Determine if the given request is from the first-party application frontend.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public static function fromFrontend(Request $request): bool
    {
        $domain = $request->headers->get('referer') ?: $request->headers->get('origin'); // phpcs:ignore

        $domain = Str::replaceFirst('https://', '', $domain);
        $domain = Str::replaceFirst('http://', '', $domain);
        $domain = Str::endsWith($domain, '/') ? $domain : "{$domain}/";

        $stateful = array_filter(Config::stateful([[]]));

        return Str::is(Collection::make($stateful)->map(function ($uri) {
            return trim($uri) . '/*';
        })->all(), $domain);
    }
}
