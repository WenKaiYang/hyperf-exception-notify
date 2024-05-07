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

use function Hyperf\Config\config;

class ApplicationCollector extends Collector
{
    /**
     * @return array<string, mixed>
     */
    public function collect(): array
    {
        return [
            'name' => config('app_name'),
            'version' => config('app_version'),
            'environment' => config('app_env'),
            'scan_cacheable' => config('scan_cacheable'),
        ];
    }
}
