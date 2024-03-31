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

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Router\Dispatched;

class RequestMiddlewareCollector extends Collector
{
    public function __construct(protected RequestInterface $request)
    {
    }

    public function collect(): array
    {
        $dispatched = $this->request->getAttribute(Dispatched::class);
        return is_null($dispatched->handler) ? [] : $dispatched->handler->options['middleware'];
    }
}
