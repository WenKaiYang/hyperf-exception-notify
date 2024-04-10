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
namespace ELLa123\HyperfExceptionNotify\Channels;

use Guanguans\Notify\Contracts\MessageInterface;
use Guanguans\Notify\Messages\DingTalk\MarkdownMessage;

class DingTalkChannel extends NotifyAbstractChannel
{
    protected function createMessage(string $report):MessageInterface
    {
        return new MarkdownMessage($report);
    }
}
