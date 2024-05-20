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

namespace ELLa123\HyperfExceptionNotify;

use ELLa123\HyperfExceptionNotify\Contracts\CollectorContract;
use ELLa123\HyperfExceptionNotify\Contracts\ExceptionAwareContract;
use ELLa123\HyperfExceptionNotify\Exceptions\InvalidArgumentException;
use Hyperf\Support\Fluent;
use Throwable;

use function Hyperf\Collection\collect;
use function Hyperf\Config\config;
use function Hyperf\Support\make;

class CollectorManager extends Fluent
{
    protected int $time;

    /**
     * @throws InvalidArgumentException
     *
     * @noinspection MagicMethodsValidityInspection
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     * @noinspection MissingParentCallInspection
     */
    public function __construct()
    {
        $collectors = collect(config('exception_notify.collector'))
            ->map(function ($parameters, $class) {
                if (! is_array($parameters)) {
                    [$parameters, $class] = [[], $parameters];
                }
                return make($class, $parameters);
            })
            ->values()
            ->all();

        foreach ($collectors as $index => $collector) {
            $this->offsetSet($index, $collector);
        }
    }

    /**
     * @noinspection PhpMissingParentCallCommonInspection
     * @noinspection MissingParentCallInspection
     *
     * @param array-key $offset
     *
     * @throws InvalidArgumentException
     */
    public function offsetSet($offset, mixed $value): void
    {
        if (! $value instanceof CollectorContract) {
            throw new InvalidArgumentException(sprintf(
                'Collector must be instance of %s',
                CollectorContract::class
            ));
        }

        $this->attributes[$offset] = $value;
    }

    public function toReport(Throwable $throwable): string
    {
        return collect($this)
            ->mapWithKeys(static function (CollectorContract $collector) use ($throwable): array {
                $collector instanceof ExceptionAwareContract and $collector->setException($throwable);

                return [$collector->name() => $collector->collect()];
            })
            ->toJson(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
