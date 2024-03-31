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
if (! function_exists('blank')) {
    /**
     * Determine if the given value is "blank".
     *
     * @param mixed $value
     * @return bool
     */
    function blank($value)
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

if (! function_exists('array_filter_filled')) {
    function array_filter_filled(array $array): array
    {
        return array_filter($array, static fn ($item) => ! blank($item));
    }
}

if (! function_exists('var_output')) {
    /**
     * @noinspection DebugFunctionUsageInspection
     *
     * @param mixed $expression
     *
     * @return null|string|void
     */
    function var_output($expression, bool $return = false)
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

if (! function_exists('exception_notify_report_if')) {
    function exception_notify_report_if($condition, $exception, ...$channels): void
    {
        value($condition) and exception_notify_report($exception, ...$channels);
    }
}

if (! function_exists('exception_notify_report')) {
    function exception_notify_report($exception, ...$channels): void
    {
        $exception instanceof Throwable or $exception = new Exception($exception);

        ExceptionNotify::onChannel(...$channels)->report($exception);
    }
}

if (! function_exists('is_callable_with_at_sign')) {
    /**
     * Determine if the given string is in Class@method syntax.
     *
     * @param mixed $callback
     */
    function is_callable_with_at_sign($callback): bool
    {
        return is_string($callback) && str_contains($callback, '@');
    }
}
