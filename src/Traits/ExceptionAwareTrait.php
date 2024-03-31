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
namespace ELLa123\HyperfExceptionNotify\Traits;

use Throwable;

trait ExceptionAwareTrait
{
    /**
     * @var Throwable
     */
    protected $exception;

    public function setException(Throwable $throwable): void
    {
        $this->exception = $throwable;
    }
}
