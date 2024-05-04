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

use Countable;
use Exception;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\Arr;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\SimpleCache\CacheInterface;
use Throwable;

function blank(mixed $value): bool
{
    if (is_null($value)) {
        return true;
    }

    if (is_string($value)) {
        return trim($value) === '';
    }

    if (is_numeric($value) || is_bool($value)) {
        return false;
    }

    if ($value instanceof Countable) {
        return count($value) === 0;
    }

    return empty($value);
}

function array_filter_filled(array $array): array
{
    return array_filter($array, static function ($item) {
        return ! blank($item);
    });
}

function var_output(mixed $expression, bool $return = false)
{
    $patterns = [
        "/array \\(\n\\)/" => '[]',
        "/array \\(\n\\s+\\)/" => '[]',
        '/array \\(/' => '[',
        '/^([ ]*)\\)(,?)$/m' => '$1]$2',
        "/=>[ ]?\n[ ]+\\[/" => '=> [',
        "/([ ]*)(\\'[^\\']+\\') => ([\\[\\'])/" => '$1$2 => $3',
    ];

    $export = var_export($expression, true);
    $export = preg_replace(array_keys($patterns), array_values($patterns), $export);
    if ($return) {
        return $export;
    }

    echo $export;
}

function exception_notify_report_if(mixed $condition, mixed $exception, array|string $channels): void
{
    value($condition) and exception_notify_report($exception, $channels);
}

function exception_notify_report(mixed $exception, array|string $channels): void
{
    $exception instanceof Throwable or $exception = new Exception($exception);

    make(ExceptionNotify::class)->onChannel($channels)->report($exception);
}

function stdoutLogger(): StdoutLoggerInterface
{
    return make(StdoutLoggerInterface::class);
}

function cache()
{
    return make(CacheInterface::class);
}

function event(object $event): void
{
    make(EventDispatcherInterface::class)->dispatch($event);
}

function real_ip(mixed $request = null): mixed
{
    $request = $request ?? make(RequestInterface::class);

    $ip = $request->getHeader('x-forwarded-for');

    if (empty($ip)) {
        $ip = $request->getHeader('x-real-ip');
    }

    if (empty($ip)) {
        $ip = $request->getServerParams()['remote_addr'] ?? '127.0.0.1';
    }

    if (is_array($ip)) {
        $ip = Arr::first($ip);
    }

    return Arr::first(explode(',', $ip));
}
