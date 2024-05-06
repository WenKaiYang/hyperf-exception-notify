<?php

/** @noinspection PhpUnused */

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace ELLa123\HyperfExceptionNotify;

use ELLa123\HyperfExceptionNotify\Channels\DingTalkChannel;
use ELLa123\HyperfExceptionNotify\Channels\FeiShuChannel;
use ELLa123\HyperfExceptionNotify\Channels\LogAbstractChannel;
use ELLa123\HyperfExceptionNotify\Channels\WeWorkChannel;
use ELLa123\HyperfExceptionNotify\Jobs\ReportExceptionJob;
use ELLa123\HyperfExceptionNotify\Support\Manager;
use ELLa123\HyperfExceptionNotify\Support\RateLimiter;
use Guanguans\Notify\Factory;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\Arr;
use Hyperf\Utils\Str;
use RedisException;
use Throwable;

class ExceptionNotify extends Manager
{
    public function __construct(
        protected CollectorManager $collectorManager,
        protected ConfigInterface $config,
        protected RateLimiter $rateLimiter
    ) {
    }

    public function reportIf($condition, Throwable $throwable): void
    {
        value($condition) and $this->report($throwable);
    }

    public function report(Throwable $throwable): void
    {
        try {
            if ($this->shouldntReport($throwable)) {
                return;
            }
            $this->dispatchReportExceptionJob($throwable);
        } catch (Throwable $throwable) {
            stdoutLogger()->error($throwable->getMessage(), ['exception' => $throwable]);
        }
    }

    /**
     * @throws RedisException
     */
    public function shouldntReport(Throwable $throwable): bool
    {
        if (!$this->config->get('exception_notify.enabled')) {
            return true;
        }

        if (!Str::is($this->config->get('exception_notify.env'), (string) env('APP_ENV'))) {
            return true;
        }

        foreach ($this->config->get('exception_notify.dont_report') as $type) {
            if ($throwable instanceof $type) {
                return true;
            }
        }

        return !$this->rateLimiter->attempt(
            md5($throwable->getFile().$throwable->getLine().$throwable->getCode().$throwable->getMessage().$throwable->getTraceAsString()),
            config('exception_notify.rate_limiter.max_attempts'),
            static fn(): bool => true,
            config('exception_notify.rate_limiter.decay_seconds')
        );
    }

    protected function dispatchReportExceptionJob(Throwable $throwable): void
    {
        $report = $this->collectorManager->toReport($throwable);

        $drivers = $this->getDrivers() ?: Arr::wrap($this->driver());

        foreach ($drivers as $driver) {
            (new ReportExceptionJob($driver, $report))->handle();
        }
    }

    /**
     * @throws RedisException
     */
    public function shouldReport(Throwable $throwable): bool
    {
        return !$this->shouldntReport($throwable);
    }

    public function getDefaultDriver(): string
    {
        return config('exception_notify.default');
    }

    public function onChannel(array|string|null $channels = null): self
    {
        if ($channels){
            is_string($channels) && $channels = explode(',', $channels);
            foreach ((array) $channels as $channel) {
                $this->driver($channel);
            }
        }else{
            $this->driver();
        }
        return $this;
    }

    protected function createLogDriver(): LogAbstractChannel
    {
        return new LogAbstractChannel(
            config('exception_notify.channels.log.channel'),
            config('exception_notify.channels.log.level'),
        );
    }

    protected function createFeiShuDriver(): FeiShuChannel
    {
        return new FeiShuChannel(
            Factory::feiShu(array_filter_filled([
                'token' => config('exception_notify.channels.feiShu.token'),
                'secret' => config('exception_notify.channels.feiShu.secret'),
            ]))
        );
    }

    protected function createDingTalkDriver(): DingTalkChannel
    {
        return new DingTalkChannel(
            Factory::DingTalk(array_filter_filled([
                'token' => config('exception_notify.channels.dingTalk.token'),
                'secret' => config('exception_notify.channels.dingTalk.secret'),
            ]))
        );
    }

    protected function createWeWorkChannel(): WeWorkChannel
    {
        return new WeWorkChannel(Factory::weWork(array_filter_filled([
            'token' => config('exception_notify.channels.weWork.token'),
            'secret' => config('exception_notify.channels.weWork.secret'),
        ])));
    }
}
