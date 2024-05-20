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

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function ELLa123\HyperfUtils\stdoutLogger;

class LogAbstractChannel extends AbstractChannel
{
    protected string $level;

    protected string $channel;

    public function __construct(string $channel, string $level)
    {
        $this->channel = $channel;
        $this->level = $level;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function report(string $report): void
    {
        stdoutLogger()->{$this->level}($report);
    }
}
