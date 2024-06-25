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
            'name' => config('app_name', env('APP_NAME')),
            'version' => config('app_version', env('APP_VERSION')),
            'environment' => config('app_env', env('APP_ENV')),
            'scan_cacheable' => env('SCAN_CACHEABLE'),
        ];
    }
}
