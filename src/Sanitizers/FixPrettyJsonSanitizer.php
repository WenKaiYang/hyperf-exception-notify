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
use ELLa123\HyperfExceptionNotify\Support\JsonFixer;
use Throwable;

class FixPrettyJsonSanitizer
{
    public function __construct(protected JsonFixer $jsonFixer) {}

    public function handle(string $report, Closure $next, string $missingValue = '"..."'): string
    {
        try {
            $fixedJson = $this->jsonFixer->silent(false)->missingValue($missingValue)->fix($report);

            return $next(json_encode(
                json_decode($fixedJson, true),
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
            ));
        } catch (Throwable $throwable) {
            return $next($report);
        }
    }
}
