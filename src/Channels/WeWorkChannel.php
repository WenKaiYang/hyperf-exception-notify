<?php

namespace ELLa123\HyperfExceptionNotify\Channels;

use Guanguans\Notify\Contracts\MessageInterface;
use Guanguans\Notify\Messages\WeWork\TextMessage;
use function ELLa123\HyperfExceptionNotify\array_filter_filled;

use function Hyperf\Config\config;
class WeWorkChannel extends NotifyAbstractChannel
{

    protected function createMessage(string $report): MessageInterface
    {
        return TextMessage::create(array_filter_filled([
            'content' => $report,
            'mentioned_list' => config('exception-notify.channels.weWork.mentioned_list'),
            'mentioned_mobile_list' => config('exception-notify.channels.weWork.mentioned_mobile_list'),
        ]));
    }
}