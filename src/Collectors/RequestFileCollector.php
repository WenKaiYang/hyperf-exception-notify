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

class RequestFileCollector extends Collector
{
    public function __construct(protected RequestInterface $request) {}

    /**
     * @return array
     */
    public function collect(): array
    {
        $files = $this->request->getUploadedFiles();
        array_walk_recursive($files, static function (&$file): void {
            $file = [
                'name' => $file->getClientOriginalName(),
                'size' => $file->isFile() ? ($file->getSize() / 1000) . 'KB' : '0',
            ];
        });

        return $files;
    }
}
