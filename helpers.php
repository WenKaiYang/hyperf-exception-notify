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

use ELLa123\HyperfExceptionNotify\ExceptionNotify;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use function Hyperf\Support\make;
use function Hyperf\Support\value;

if (!function_exists('blank')) {
    /**
     * Determine if the given value is "blank".
     *
     * @param  mixed  $value
     * @return bool
     */
    function blank(mixed $value): bool
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if ($value instanceof Countable) {
            return count($value) === 0;
        }

        return empty($value);
    }
}

if (!function_exists('array_filter_filled')) {
    function array_filter_filled(array $array): array
    {
        return array_filter($array, static fn($item) => !blank($item));
    }
}

if (!function_exists('var_output')) {
    /**
     * @param  mixed  $expression
     * @param  bool  $return
     * @return null|string|void
     */
    function var_output(mixed $expression, bool $return = false)
    {
        $patterns = [
            "/array \\(\n\\)/" => '[]',
            "/array \\(\n\\s+\\)/" => '[]',
            '/array \\(/' => '[',
            '/^([ ]*)\\)(,?)$/m' => '$1]$2',
            "/=>[ ]?\n[ ]+\\[/" => '=> [',
            "/([ ]*)(\\'[^\\']+\\') => ([\\[\\'])/" => '$1$2 => $3',
        ];

        $export = var_export($expression, true);
        $export = preg_replace(array_keys($patterns), array_values($patterns), $export);
        if ($return) {
            return $export;
        }

        echo $export;
    }
}

if (!function_exists('exception_notify_report_if')) {
    function exception_notify_report_if($condition, $exception, ...$channels): void
    {
        value($condition) and exception_notify_report($exception, ...$channels);
    }
}

if (!function_exists('exception_notify_report')) {
    function exception_notify_report($exception, ...$channels): void
    {
        $exception instanceof Throwable or $exception = new Exception($exception);

        ExceptionNotify::onChannel(...$channels)->report($exception);
    }
}

if (!function_exists('is_callable_with_at_sign')) {
    /**
     * Determine if the given string is in Class@method syntax.
     *
     * @param  mixed  $callback
     */
    function is_callable_with_at_sign($callback): bool
    {
        return is_string($callback) && str_contains($callback, '@');
    }
}

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
if (!function_exists('app')) {
    /**
     * 获取容器实例.
     *
     * @return \Psr\Container\ContainerInterface
     */
    function app(): Psr\Container\ContainerInterface
    {
        return Hyperf\Context\ApplicationContext::getContainer();
    }
}

if (!function_exists('logger')) {
    /**
     * 日志组件.
     *
     * @param  string  $group  日志配置
     *
     * @return LoggerInterface
     */
    function logger(string $group = 'default'): LoggerInterface
    {
        return make(LoggerFactory::class)
            ->get('default', $group);
    }
}

if (!function_exists('stdoutLogger')) {
    /**
     * StdoutLogger.
     *
     * @return StdoutLoggerInterface
     */
    function stdoutLogger(): StdoutLoggerInterface
    {
        return make(StdoutLoggerInterface::class);
    }
}

if (!function_exists('cache')) {
    /**
     * 获取缓存驱动.
     */
    function cache()
    {
        return make(\Psr\SimpleCache\CacheInterface::class);
    }
}

if (!function_exists('event')) {
    /**
     * 触发事件.
     */
    function event(object $event): void
    {
        make(EventDispatcherInterface::class)->dispatch($event);
    }
}

if (!function_exists('real_ip')) {
    /**
     * 获取真实ip.
     */
    function real_ip(mixed $request = null): mixed
    {
        $request = $request ?? make(\Hyperf\HttpServer\Contract\RequestInterface::class);

        $ip = $request->getHeader('x-forwarded-for');

        if (empty($ip)) {
            $ip = $request->getHeader('x-real-ip');
        }

        if (empty($ip)) {
            $ip = $request->getServerParams()['remote_addr'] ?? '127.0.0.1';
        }

        if (is_array($ip)) {
            $ip = \Hyperf\Collection\Arr::first($ip);
        }

        return \Hyperf\Collection\Arr::first(explode(',', $ip));
    }
}

if (!function_exists('asyncQueue')) {
    /**
     * 投递队列.
     *
     * @param  \Hyperf\AsyncQueue\Job  $job  异步Job
     * @param  int  $delay  延迟时间-秒
     * @param  string  $driver  消息队列驱动
     */
    function asyncQueue(Hyperf\AsyncQueue\Job $job, int $delay = 0, string $driver = 'default')
    {
        make(\Hyperf\AsyncQueue\Driver\DriverFactory::class)->get($driver)->push($job, $delay);
    }
}

if (!function_exists('redirect')) {
    /**
     * 页面重定向.
     *
     * @param  string  $url  跳转URL
     * @param  int  $status  HTTP状态码
     * @param  string  $schema  协议
     * @return \Psr\Http\Message\ResponseInterface
     */
    function redirect(string $url, int $status = 302, string $schema = 'http'): Psr\Http\Message\ResponseInterface
    {
        return make(\Hyperf\HttpServer\Contract\ResponseInterface::class)
            ->redirect($url, $status, $schema);
    }
}

if (!function_exists('remember')) {
    /**
     * 数据缓存.
     *
     * @param  string  $key  缓存KEY
     * @param  null|DateInterval|int  $ttl  缓存时间
     * @param  Closure  $closure
     * @return mixed
     */
    function remember(string $key, null|int|DateInterval $ttl, Closure $closure): mixed
    {
        if (!empty($value = cache()->get($key))) {
            return $value;
        }

        $value = $closure();

        cache()->set($key, $value, $ttl);

        return $value;
    }
}

if (!function_exists('config_set')) {
    /**
     * 修改配置项.
     *
     * @param  string  $key  identifier of the entry to set
     * @param  mixed  $value  the value that save to container
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    function config_set(string $key, mixed $value): mixed
    {
        return app()->get(\Hyperf\Contract\ConfigInterface::class)->set($key, $value);
    }
}

if (!function_exists('throw_if')) {
    /**
     * Throw the given exception if the given condition is true.
     *
     * @param  mixed  $condition  判断条件
     * @param  string|\Throwable  $exception  指定异常信息(RuntimeException)|抛出异常
     * @param  mixed  ...$parameters  异常自定义参数
     *
     * @return mixed 返回条件数据
     * @throws \Throwable
     */
    function throw_if(mixed $condition, Throwable|string $exception = 'RuntimeException', ...$parameters): mixed
    {
        if ($condition) {
            if (is_string($exception) && class_exists($exception)) {
                $exception = new $exception(...$parameters);
            }

            throw is_string($exception) ? new RuntimeException($exception) : $exception;
        }

        return $condition;
    }
}

if (!function_exists('throw_unless')) {
    /**
     * Throw the given exception unless the given condition is true.
     *
     * @param  mixed  $condition  判断条件
     * @param  string|\Throwable  $exception  指定异常信息(RuntimeException)|抛出异常
     * @param  mixed  ...$parameters  异常自定义参数
     *
     * @return mixed 返回条件数据
     * @throws \Throwable
     */
    function throw_unless(mixed $condition, Throwable|string $exception = 'RuntimeException', ...$parameters): mixed
    {
        throw_if(!$condition, $exception, ...$parameters);

        return $condition;
    }
}

if (!function_exists('redis')) {
    /**
     * redis用例.
     *
     * @param  string  $driver  redis实例
     *
     * @return \Hyperf\Redis\RedisProxy
     */
    function redis(string $driver = 'default'): Hyperf\Redis\RedisProxy
    {
        return make(\Hyperf\Redis\RedisFactory::class)->get($driver);
    }
}

if (!function_exists('annotation_collector')) {
    /**
     * 获取指定annotation.
     *
     * @param  string  $class  查询类
     * @param  string  $method  查询方法
     * @param  string  $annotationTarget  指定注解类
     *
     * @return \Hyperf\Di\Annotation\AbstractAnnotation
     * @throws \Hyperf\Di\Exception\AnnotationException
     */
    function annotation_collector(
        string $class,
        string $method,
        string $annotationTarget
    ): Hyperf\Di\Annotation\AbstractAnnotation {
        $methodAnnotation = \Hyperf\Di\Annotation\AnnotationCollector::getClassMethodAnnotation($class,
            $method)[$annotationTarget] ?? null;

        if ($methodAnnotation instanceof $annotationTarget) {
            return $methodAnnotation;
        }

        $classAnnotation = \Hyperf\Di\Annotation\AnnotationCollector::getClassAnnotations($class)[$annotationTarget] ?? null;
        if (!$classAnnotation instanceof $annotationTarget) {
            throw new \Hyperf\Di\Exception\AnnotationException("Annotation {$annotationTarget} couldn't be collected successfully.");
        }
        return $classAnnotation;
    }
}