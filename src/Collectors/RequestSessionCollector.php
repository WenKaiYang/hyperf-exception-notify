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

use Hyperf\Contract\SessionInterface;
use Hyperf\Di\Exception\InvalidDefinitionException;

class RequestSessionCollector extends Collector
{
    public function collect(): array
    {
        try {
            return app()->get(SessionInterface::class)->all();
        } catch (InvalidDefinitionException $throwable) {
            return [];
        }
    }
}
