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
namespace ELLa123\HyperfExceptionNotify\Sanitizers;

use Closure;

class LengthLimitSanitizer
{
    public function handle(string $report, Closure $next, $length = -1): string
    {
        $length > 0 and $report = substr($report, 0, (int) ($length * 90 / 100));
        return $next($report);
    }
}
