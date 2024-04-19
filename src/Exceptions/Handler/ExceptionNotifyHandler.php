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

namespace ELLa123\HyperfExceptionNotify\Exceptions\Handler;

use ELLa123\HyperfExceptionNotify\ExceptionNotify;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ExceptionNotifyHandler extends ExceptionHandler
{
    #[Inject]
    protected ExceptionNotify $exceptionNotify;

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $channels = \Hyperf\Collection\collect(\Hyperf\Config\config('exception_notify.channels'))->keys();

        if (empty($channels)) {
            return $response;
        }

        $this->exceptionNotify->onChannel(...$channels)->report($throwable);

        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
