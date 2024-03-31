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

class ChoreCollector extends Collector
{
    /**
     * @return array{time: string, memory: string}
     */
    public function collect(): array
    {
        return [
            'time' => date('Y-m-d H:i:s'),
            'memory' => round(memory_get_peak_usage(true) / 1024 / 1024, 1) . 'M',
        ];
    }
}
