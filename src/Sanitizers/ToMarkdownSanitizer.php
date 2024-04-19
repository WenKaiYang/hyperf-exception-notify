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

class ToMarkdownSanitizer
{
    public function handle(
        string $report,
        Closure $next,
        string $mark = <<<'md'
            ```
            %s
            ```
            md
    ): string {
        return $next(sprintf($mark, $report));
    }
}
