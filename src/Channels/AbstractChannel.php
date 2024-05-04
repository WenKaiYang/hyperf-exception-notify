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

namespace ELLa123\HyperfExceptionNotify\Channels;

use ELLa123\HyperfExceptionNotify\Contracts\ChannelContract;

use Hyperf\Utils\Str;

abstract class AbstractChannel implements ChannelContract
{
    public function getName(): string
    {
        return Str::lower(Str::beforeLast(class_basename($this), 'AbstractChannel'));
    }

    abstract public function report(string $report);
}
