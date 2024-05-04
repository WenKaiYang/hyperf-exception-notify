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
use Hyperf\Utils\Str;

class RequestPostCollector extends Collector
{
    public function __construct(protected RequestInterface $request) {}

    public function collect(): array
    {
        return collect($this->request->post())
            ->transform(static function ($val, $key) {
                Str::is([
                    'password',
                    '*password',
                    'password*',
                ], $key) and $val = '******';

                return $val;
            })
            ->all();
    }
}
