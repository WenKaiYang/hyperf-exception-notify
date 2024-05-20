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

use Exception;
use Throwable;

use function Hyperf\Support\make;
use function Hyperf\Support\value;

/**
 * @return null|string|void
 */
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

function exception_notify_report_if(mixed $condition, mixed $exception, null|array|string $channels = null): void
{
    value($condition) and exception_notify_report($exception, $channels);
}

function exception_notify_report(mixed $exception, null|array|string $channels = null): void
{
    $exception instanceof Throwable or $exception = new Exception($exception);

    make(ExceptionNotify::class)->onChannel($channels)->report($exception);
}
