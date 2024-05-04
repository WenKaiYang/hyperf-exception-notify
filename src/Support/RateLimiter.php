<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace ELLa123\HyperfExceptionNotify\Support;

use Closure;
use Hyperf\Redis\Redis;
use Hyperf\Utils\InteractsWithTime;

class RateLimiter
{
    use InteractsWithTime;

    /**
     * The configured limit object resolvers.
     */
    protected array $limiters = [];

    /**
     * Create a new rate limiter instance.
     */
    public function __construct(protected Redis $redis) {}

    /**
     * Register a named limiter configuration.
     *
     * @return $this
     */
    public function for(string $name, Closure $callback): static
    {
        $this->limiters[$name] = $callback;

        return $this;
    }

    /**
     * Get the given named rate limiter.
     */
    public function limiter(string $name): ?Closure
    {
        return $this->limiters[$name] ?? null;
    }

    /**
     * Attempts to execute a callback if it's not limited.
     * @throws \RedisException
     */
    public function attempt(string $key, int $maxAttempts, Closure $callback, int $decaySeconds = 60): mixed
    {
        if ($this->tooManyAttempts($key, $maxAttempts)) {
            return false;
        }

        return tap($callback() ?: true, function () use ($key, $decaySeconds) {
            $this->hit($key, $decaySeconds);
        });
    }

    /**
     * Determine if the given key has been "accessed" too many times.
     * @throws \RedisException
     */
    public function tooManyAttempts(string $key, int $maxAttempts): bool
    {
        if ($this->attempts($key) >= $maxAttempts) {
            if ($this->redis->get($this->cleanRateLimiterKey($key) . ':timer')) {
                return true;
            }

            $this->resetAttempts($key);
        }

        return false;
    }

    /**
     * Increment the counter for a given key for a given decay time.
     * @throws \RedisException
     */
    public function hit(string $key, int $decaySeconds = 60): int
    {
        $key = $this->cleanRateLimiterKey($key);

        $this->redis->set(
            $key . ':timer',
            $this->availableAt($decaySeconds),
            $decaySeconds
        );

        $hits = $this->redis->incr($key);
        $hits === 1 and $this->redis->expire($key, $decaySeconds);

        return $hits;
    }

    /**
     * Get the number of attempts for the given key.
     * @throws \RedisException
     */
    public function attempts(string $key): int
    {
        $key = $this->cleanRateLimiterKey($key);

        return (int) $this->redis->get($key);
    }

    /**
     * Reset the number of attempts for the given key.
     * @throws \RedisException
     */
    public function resetAttempts(string $key): int
    {
        $key = $this->cleanRateLimiterKey($key);

        return $this->redis->del($key);
    }

    /**
     * Get the number of retries left for the given key.
     */
    public function remaining(string $key, int $maxAttempts): int
    {
        $key = $this->cleanRateLimiterKey($key);

        $attempts = $this->attempts($key);

        return $maxAttempts - $attempts;
    }

    /**
     * Get the number of retries left for the given key.
     */
    public function retriesLeft(string $key, int $maxAttempts): int
    {
        return $this->remaining($key, $maxAttempts);
    }

    /**
     * Clear the hits and lockout timer for the given key.
     * @throws \RedisException
     */
    public function clear(string $key): void
    {
        $key = $this->cleanRateLimiterKey($key);

        $this->resetAttempts($key);

        $this->redis->del($key . ':timer');
    }

    /**
     * Get the number of seconds until the "key" is accessible again.
     */
    public function availableIn(string $key): int
    {
        $key = $this->cleanRateLimiterKey($key);

        return max(0, $this->redis->get($key . ':timer') - $this->currentTime());
    }

    /**
     * Clean the rate limiter key from unicode characters.
     */
    public function cleanRateLimiterKey(string $key): string
    {
        return preg_replace('/&([a-z])[a-z]+;/i', '$1', htmlentities($key));
    }
}
