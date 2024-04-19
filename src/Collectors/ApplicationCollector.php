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

class ApplicationCollector extends Collector
{
    /**
     * @return array<string, mixed>
     */
    public function collect(): array
    {
        return [
            'name' => \Hyperf\Support\env('APP_NAME'),
            'environment' => \Hyperf\Support\env('APP_ENV'),
        ];
    }
}
