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

class CollectorManager extends Fluent
{
    protected $time;

    /**
     * @throws \ELLa123\HyperfExceptionNotify\Exceptions\InvalidArgumentException
     *
     * @noinspection MagicMethodsValidityInspection
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     * @noinspection MissingParentCallInspection
     */
    public function __construct()
    {
        $collectors = \Hyperf\Collection\collect(\Hyperf\Config\config('exception_notify.collector'))
            ->map(function ($parameters, $class) {
                if (! is_array($parameters)) {
                    [$parameters, $class] = [[], $parameters];
                }
                return \Hyperf\Support\make($class, $parameters);
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
     * @param mixed $value
     *
     * @throws \ELLa123\HyperfExceptionNotify\Exceptions\InvalidArgumentException
     */
    public function offsetSet($offset, $value): void
    {
        if (! $value instanceof CollectorContract) {
            throw new InvalidArgumentException(sprintf('Collector must be instance of %s', CollectorContract::class));
        }

        $this->attributes[$offset] = $value;
    }

    public function toReport(Throwable $throwable): string
    {
        return \Hyperf\Collection\collect($this)
            ->mapWithKeys(static function (CollectorContract $collector) use ($throwable): array {
                $collector instanceof ExceptionAwareContract and $collector->setException($throwable);

                return [$collector->name() => $collector->collect()];
            })
            ->toJson(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
