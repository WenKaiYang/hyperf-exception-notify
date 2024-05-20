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

namespace ELLa123\HyperfExceptionNotify\Jobs;

use ELLa123\HyperfExceptionNotify\Channels\AbstractChannel;
use ELLa123\HyperfExceptionNotify\Events\ReportedEvent;
use ELLa123\HyperfExceptionNotify\Events\ReportingEvent;
use Hyperf\Context\ApplicationContext;
use Hyperf\Pipeline\Pipeline;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function ELLa123\HyperfUtils\event;
use function Hyperf\Config\config;

class ReportExceptionJob
{
    protected AbstractChannel $channel;

    protected string $report;

    protected string $pipedReport = '';

    public function __construct(AbstractChannel $channel, string $report)
    {
        $this->channel = $channel;
        $this->report = $report;
        $this->pipedReport = $this->pipelineReport($report);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(): void
    {
        $this->fireReportingEvent($this->pipedReport);
        $result = $this->channel->report($this->pipedReport);
        $this->fireReportedEvent($result);
    }

    protected function pipelineReport(string $report): string
    {
        return (new Pipeline(ApplicationContext::getContainer()))
            ->send($report)
            ->through($this->getChannelPipeline())
            ->then(static fn ($report) => $report);
    }

    protected function getChannelPipeline(): array
    {
        return config(
            sprintf('exception_notify.channels.%s.sanitizers', $this->channel->getName()),
            []
        );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function fireReportingEvent(string $report): void
    {
        event(new ReportingEvent($this->channel, $report));
    }

    /**
     * @param mixed $result
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function fireReportedEvent($result): void
    {
        event(new ReportedEvent($this->channel, $result));
    }
}
