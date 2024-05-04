<?php

/** @noinspection PhpDocSignatureInspection */

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace ELLa123\HyperfExceptionNotify\Listeners;

use ELLa123\HyperfExceptionNotify\ExceptionNotify;
use Hyperf\Command\Event\FailToHandle;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Event\Contract\ListenerInterface;

use function Hyperf\Collection\collect;
use function Hyperf\Config\config;

class CommandFailToHandleListener implements ListenerInterface
{
    #[Inject]
    protected ExceptionNotify $exceptionNotify;

    public function listen(): array
    {
        return [
            FailToHandle::class,
        ];
    }

    /**
     * @param FailToHandle $event
     */
    public function process(object $event): void
    {
        $channels = collect(config('exception_notify.channels'))->keys();

        if (empty($channels)) {
            return;
        }

        $this->exceptionNotify->onChannel(...$channels)->report($event->getThrowable());
    }
}
