<?php

/** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

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
use Throwable;

use function ELLa123\HyperfExceptionNotify\real_ip;
use function ELLa123\HyperfExceptionNotify\stdoutLogger;

class RequestBasicCollector extends Collector
{
    public function __construct(protected RequestInterface $request) {}

    /**
     * @psalm-suppress InvalidScalarArgument
     * @psalm-suppress InvalidArgument
     *
     * @return array{url: string, ip: null|string, method: string, action: mixed, duration: string}
     */
    public function collect(): array
    {
        $dispatched = $this->request->getAttribute(Dispatched::class);

        $request = $this->request;

        $data = [
            'url' => $this->request->fullUrl(),
            'ip' => real_ip(),
            'method' => $this->request->getMethod(),
            'route' => '',
            'action' => $this->request->getRequestTarget(),
            'class' => '',
            'function' => '',
            'duration' => value(function () use ($request) {
                $startTime = $request->server('request_time_float');
                return floor((microtime(true) - $startTime) * 1000) . 'ms';
            }),
        ];

        try {
            if (! is_null($dispatched->handler)) {
                $data['route'] = $dispatched->handler->route;
                $data['class'] = $dispatched->handler->callback[0];
                $data['function'] = $dispatched->handler->callback[1];
            }
        } catch (Throwable $throwable) {
            stdoutLogger()->error('采集器异常: ' . $throwable->getMessage());
        }

        return $data;
    }
}
