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
use ELLa123\HyperfExceptionNotify\Collectors\ApplicationCollector;
use ELLa123\HyperfExceptionNotify\Collectors\ChoreCollector;
use ELLa123\HyperfExceptionNotify\Collectors\ExceptionBasicCollector;
use ELLa123\HyperfExceptionNotify\Collectors\ExceptionTraceCollector;
use ELLa123\HyperfExceptionNotify\Collectors\PhpInfoCollector;
use ELLa123\HyperfExceptionNotify\Collectors\RequestBasicCollector;
use ELLa123\HyperfExceptionNotify\Collectors\RequestCookieCollector;
use ELLa123\HyperfExceptionNotify\Collectors\RequestFileCollector;
use ELLa123\HyperfExceptionNotify\Collectors\RequestHeaderCollector;
use ELLa123\HyperfExceptionNotify\Collectors\RequestMiddlewareCollector;
use ELLa123\HyperfExceptionNotify\Collectors\RequestPostCollector;
use ELLa123\HyperfExceptionNotify\Collectors\RequestQueryCollector;
use ELLa123\HyperfExceptionNotify\Collectors\RequestServerCollector;
use ELLa123\HyperfExceptionNotify\Collectors\RequestSessionCollector;
use ELLa123\HyperfExceptionNotify\Sanitizers\AppendContentSanitizer;
use ELLa123\HyperfExceptionNotify\Sanitizers\FixPrettyJsonSanitizer;
use ELLa123\HyperfExceptionNotify\Sanitizers\LengthLimitSanitizer;

use function Hyperf\Support\env;

return [
    /*
    |--------------------------------------------------------------------------
    | Enable exception notification report switch.
    |--------------------------------------------------------------------------
    |
    | If set to false, the exception notification report will not be enabled.
    |
    */
    'enabled' => (bool) env('EXCEPTION_NOTIFY_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Enable command exception notification report switch.
    |--------------------------------------------------------------------------
    |
    | If set to false or enabled set to false, the exception notification report will not be enabled.
    |
    */
    'enabled_cli' => (bool) env('EXCEPTION_NOTIFY_ENABLED_CLI', true),

    /*
    |--------------------------------------------------------------------------
    | A list of the application environments that are reported.
    |--------------------------------------------------------------------------
    |
    | Here you may specify a list of the application environments that should
    | be reported.
    |
    | ```
    | [production, local]
    | ```
    */
    'env' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | A list of the exception types that are not reported.
    |--------------------------------------------------------------------------
    |
    | Here you may specify a list of the exception types that should not be
    | reported.
    |
    | ```
    | [
    |     HttpResponseException::class,
    |     HttpException::class,
    | ]
    | ```
    */
    'dont_report' => [],

    /*
    |--------------------------------------------------------------------------
    | List of collectors.
    |--------------------------------------------------------------------------
    |
    | Responsible for collecting the exception data.
    |
    */
    'collector' => [
        ExceptionTraceCollector::class,
        ExceptionBasicCollector::class,
        ChoreCollector::class,
        ApplicationCollector::class,
        PhpInfoCollector::class,
        RequestBasicCollector::class,
        RequestSessionCollector::class,
        RequestCookieCollector::class,
        RequestFileCollector::class,
        RequestHeaderCollector::class,
        RequestMiddlewareCollector::class,
        RequestPostCollector::class,
        RequestQueryCollector::class,
        RequestServerCollector::class,
    ],

    /*
     |--------------------------------------------------------------------------
     | Exception notification rate limiter.
     |--------------------------------------------------------------------------
     |
     | The exception notification rate limiter is used to prevent sending
     | exception notification to the same channel too frequently.
     |
     */
    'rate_limiter' => [
        'max_attempts' => (int) env('EXCEPTION_NOTIFY_LIMIT', env('APP_ENV') === 'prod' ? 1 : 50),
        'decay_seconds' => 300,
    ],

    /*
    |--------------------------------------------------------------------------
    | Report title.
    |--------------------------------------------------------------------------
    |
    | The title of the exception notification report.
    |
    */
    'title' => env('EXCEPTION_NOTIFY_REPORT_TITLE', sprintf('%s application exception report', env('APP_ENV'))),

    /*
    |--------------------------------------------------------------------------
    | default channel.
    |--------------------------------------------------------------------------
    |
    | The default channel of the exception notification report.
    |
    */
    'default' => env('EXCEPTION_NOTIFY_DEFAULT_CHANNEL', 'log'),

    /*
     |--------------------------------------------------------------------------
     | Supported channels.
     |--------------------------------------------------------------------------
     |
     | Here you may specify a list of the supported channels.
     |
     */
    'channels' => [
        // Log
        'log' => [
            'driver' => 'log',
            'channel' => env('EXCEPTION_NOTIFY_LOG_CHANNEL', 'default'),
            'level' => env('EXCEPTION_NOTIFY_LOG_LEVEL', 'error'),
            'sanitizers' => [
            ],
        ],

        /**
         * 飞书群机器人.
         * @see https://www.feishu.cn/hc/zh-CN/articles/360024984973
         */
        'feiShu' => [
            'driver' => 'feiShu',
            'token' => env('EXCEPTION_NOTIFY_FEISHU_TOKEN'),
            'secret' => env('EXCEPTION_NOTIFY_FEISHU_SECRET'),
            'keyword' => env('EXCEPTION_NOTIFY_FEISHU_KEYWORD'),
            'sanitizers' => [
                sprintf('%s:%s', LengthLimitSanitizer::class, 30720),
                FixPrettyJsonSanitizer::class,
                sprintf('%s:%s', AppendContentSanitizer::class, env('EXCEPTION_NOTIFY_FEISHU_KEYWORD')),
            ],
        ],

        /**
         * 钉钉群机器人.
         * @see https://developers.dingtalk.com/document/app/custom-robot-access
         */
        'dingTalk' => [
            'driver' => 'dingTalk',
            'token' => env('EXCEPTION_NOTIFY_DINGTALK_TOKEN'),
            'secret' => env('EXCEPTION_NOTIFY_DINGTALK_SECRET'),
            'keyword' => env('EXCEPTION_NOTIFY_DINGTALK_KEYWORD'),
            'pipes' => [
                sprintf('%s:%s', AppendContentSanitizer::class, env('EXCEPTION_NOTIFY_DINGTALK_KEYWORD')),
                FixPrettyJsonSanitizer::class,
                sprintf('%s:%s', LengthLimitSanitizer::class, 30720),
            ],
        ],

        /**
         * 企业微信群机器人.
         * @see https://open.work.weixin.qq.com/api/doc/90000/90136/91770
         */
        'weWork' => [
            'driver' => 'weWork',
            'token' => env('EXCEPTION_NOTIFY_WEWORK_TOKEN'),
            'pipes' => [
                FixPrettyJsonSanitizer::class,
                sprintf('%s:%s', LengthLimitSanitizer::class, 5120),
            ],
        ],
    ],
];
