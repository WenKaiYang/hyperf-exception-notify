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

use Guanguans\Notify\Clients\Client;
use Guanguans\Notify\Contracts\MessageInterface;

abstract class NotifyAbstractChannel extends AbstractChannel
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function report(string $report)
    {
        return $this->client->send($this->createMessage($report));
    }

    abstract protected function createMessage(string $report): MessageInterface;
}
