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
use Guanguans\Notify\Messages\DingTalk\TextMessage;

use function Ella123\HyperfUtils\arrayFilterFilled;
use function Hyperf\Config\config;

class DingTalkChannel extends NotifyAbstractChannel
{
    protected function createMessage(string $report): MessageInterface
    {
        return TextMessage::create(arrayFilterFilled([
            'content' => $report,
            'atMobiles' => config('exception-notify.channels.dingTalk.atMobiles'),
            'atDingtalkIds' => config('exception-notify.channels.dingTalk.atDingtalkIds'),
            'isAtAll' => config('exception-notify.channels.dingTalk.isAtAll'),
        ]));
    }
}
