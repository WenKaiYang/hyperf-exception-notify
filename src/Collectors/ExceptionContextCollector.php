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
use ELLa123\HyperfExceptionNotify\Support\ExceptionContext;
use ELLa123\HyperfExceptionNotify\Traits\ExceptionAwareTrait;

class ExceptionContextCollector extends Collector implements ExceptionAwareContract
{
    use ExceptionAwareTrait;

    public function collect(): array
    {
        return ExceptionContext::getFormattedContext($this->exception);
    }
}
