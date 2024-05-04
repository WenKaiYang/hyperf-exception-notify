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

namespace ELLa123\HyperfExceptionNotify\Collectors;

use ELLa123\HyperfExceptionNotify\Contracts\ExceptionAwareContract;
use ELLa123\HyperfExceptionNotify\Traits\ExceptionAwareTrait;
use Hyperf\Utils\Str;

class ExceptionTraceCollector extends Collector implements ExceptionAwareContract
{
    use ExceptionAwareTrait;

    /**
     * @return string[]
     */
    public function collect(): array
    {
        return collect(explode("\n", $this->exception->getTraceAsString()))
            ->filter(static function ($trace) {
                return ! Str::contains($trace, 'vendor');
            })
            ->all();
    }
}
