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

class UrlEncodeSanitizer
{
    public function handle(string $report, Closure $next): string
    {
        return $next(urlencode($report));
    }
}
