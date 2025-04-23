<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter as FacadesRateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimiter
{
    /**
     * The rate limiter instance.
     *
     * @var \Illuminate\Cache\RateLimiter
     */
    protected $limiter;

    /**
     * Create a new rate limiter middleware.
     *
     * @param  \Illuminate\Cache\RateLimiter  $limiter
     * @return void
     */
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $maxAttempts
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $maxAttempts = 60)
    {
        // Determine the rate limit key based on the IP or the token if authenticated
        $key = $request->user() 
            ? 'api:' . $request->user()->id 
            : 'api:' . $request->ip();

        // Get the max attempts from config if not provided
        if (!$maxAttempts) {
            $maxAttempts = config('api.rate_limit_per_minute', 60);
        }

        // Check if the user has exceeded the rate limit
        if (FacadesRateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'success' => false,
                'message' => 'Too Many Requests',
                'data' => null,
                'retry_after' => FacadesRateLimiter::availableIn($key)
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        // Increment the rate limiter
        FacadesRateLimiter::hit($key);

        // Add rate limit headers to the response
        $response = $next($request);
        
        return $this->addHeaders(
            $response, 
            $maxAttempts,
            FacadesRateLimiter::attempts($key),
            FacadesRateLimiter::availableIn($key)
        );
    }

    /**
     * Add the limit header information to the given response.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @param  int  $maxAttempts
     * @param  int  $remainingAttempts
     * @param  int|null  $retryAfter
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function addHeaders(Response $response, $maxAttempts, $attempts, $retryAfter = null)
    {
        $remainingAttempts = $maxAttempts - $attempts;

        if ($response->headers) {
            $response->headers->add([
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => $remainingAttempts,
            ]);

            if ($retryAfter) {
                $response->headers->add([
                    'Retry-After' => $retryAfter,
                    'X-RateLimit-Reset' => $retryAfter,
                ]);
            }
        }

        return $response;
    }
}