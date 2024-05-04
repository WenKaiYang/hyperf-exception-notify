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

use Hyperf\Stringable\Str;
use InvalidArgumentException;

abstract class Manager
{
    /**
     * The array of created "drivers".
     */
    protected array $drivers = [];

    /**
     * Dynamically call the default driver instance.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->driver()->{$method}(...$parameters);
    }

    /**
     * Get a driver instance.
     *
     * @throws InvalidArgumentException
     */
    public function driver(?string $driver = null): mixed
    {
        $driver = $driver ?: $this->getDefaultDriver();

        if (is_null($driver)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to resolve NULL driver for [%s].',
                static::class
            ));
        }

        // If the given driver has not been created before, we will create the instances
        // here and cache it so we can return it next time very quickly. If there is
        // already a driver created by this name, we'll just return that instance.
        if (! isset($this->drivers[$driver])) {
            $this->drivers[$driver] = $this->createDriver($driver);
        }

        return $this->drivers[$driver];
    }

    /**
     * Get the default driver name.
     */
    abstract public function getDefaultDriver(): string;

    /**
     * Get all of the created "drivers".
     */
    public function getDrivers(): array
    {
        return $this->drivers;
    }

    /**
     * Forget all of the resolved driver instances.
     *
     * @return $this
     */
    public function forgetDrivers(): static
    {
        $this->drivers = [];

        return $this;
    }

    /**
     * Create a new driver instance.
     *
     * @throws InvalidArgumentException
     */
    protected function createDriver(string $driver): mixed
    {
        $method = 'create' . Str::studly($driver) . 'Driver';

        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        throw new InvalidArgumentException("Driver [{$driver}] not supported.");
    }
}
