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
use Throwable;

class RequestHeaderCollector extends Collector
{
    public function __construct(protected RequestInterface $request) {}

    public function collect(): array
    {
        try {
            return $this->request->getHeaders();
        } catch (Throwable $throwable) {
            return [];
        }
    }
}
