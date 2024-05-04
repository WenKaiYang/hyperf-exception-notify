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

namespace ELLa123\HyperfExceptionNotify\Support;

use RuntimeException;

use function in_array;
use function strlen;

/**
 * This file is modified from guanguans/laravel-exception-notify.
 */
class JsonFixer
{
    /** @var array Current token stack indexed by position */
    protected array $stack = [];

    /** @var bool If current char is within a string */
    protected bool $inStr = false;

    /** @var bool Whether to throw Exception on failure */
    protected bool $silent = false;

    /** @var array The complementary pairs */
    protected array $pairs = [
        '{' => '}',
        '[' => ']',
        '"' => '"',
    ];

    /**
     * @var int The last seen object `{` type position
     */
    protected int $objectPos = -1;

    /** @var int The last seen array `[` type position */
    protected int $arrayPos = -1;

    /** @var string Missing value. (Options: true, false, null) */
    protected string $missingValue = 'null';

    /**
     * Set/unset silent mode.
     *
     * @return $this
     */
    public function silent(bool $silent = true): static
    {
        $this->silent = $silent;

        return $this;
    }

    /**
     * Set missing value.
     *
     * @return $this
     */
    public function missingValue(string $value): static
    {
        // if (null === $value) {
        //     $value = 'null';
        // } elseif (is_bool($value)) {
        //     $value = $value ? 'true' : 'false';
        // }

        $this->missingValue = $value;

        return $this;
    }

    /**
     * Fix the truncated JSON.
     *
     * @param string $json the JSON string to fix
     *
     * @return string Fixed JSON. If failed with silent then original JSON.
     *
     * @throws RuntimeException when fixing fails
     */
    public function fix(string $json): string
    {
        [$head, $json, $tail] = $this->trim($json);

        if (empty($json) || $this->isValid($json)) {
            return $json;
        }

        if (null !== $tmpJson = $this->quickFix($json)) {
            return $tmpJson;
        }

        $this->reset();

        return $head . $this->doFix($json) . $tail;
    }

    public function pad($tmpJson)
    {
        if (! $this->inStr) {
            $tmpJson = rtrim($tmpJson, ',');
            while ($this->lastToken() === ',') {
                $this->popToken();
            }
        }

        $tmpJson = $this->padLiteral($tmpJson);
        $tmpJson = $this->padObject($tmpJson);

        return $this->padStack($tmpJson);
    }

    // trait PadsJson

    protected function trim($json): array
    {
        preg_match('/^(\s*)([^\s]+)(\s*)$/', $json, $match);

        $match += ['', '', '', ''];
        $match[2] = trim($json);

        array_shift($match);

        return $match;
    }

    protected function isValid($json): bool
    {
        // @psalm-suppress UnusedFunctionCall
        json_decode($json);

        return json_last_error() === JSON_ERROR_NONE;
    }

    protected function quickFix($json): ?string
    {
        if (strlen($json) === 1 && isset($this->pairs[$json])) {
            return $json . $this->pairs[$json];
        }

        if ($json[0] !== '"') {
            return $this->maybeLiteral($json);
        }

        return $this->padString($json);
    }

    protected function maybeLiteral($json)
    {
        if (! in_array($json[0], ['t', 'f', 'n'], true)) {
            return;
        }

        foreach (['true', 'false', 'null'] as $literal) {
            if (strpos($literal, $json) === 0) {
                return $literal;
            }
        }

        // @codeCoverageIgnoreStart

        // @codeCoverageIgnoreEnd
    }

    protected function padString($string): string
    {
        $last = substr($string, -1);
        $last2 = substr($string, -2);

        if ($last2 === '\"' || $last !== '"') {
            return $string . '"';
        }

        return ''; // @codeCoverageIgnore
    }

    protected function reset(): void
    {
        $this->stack = [];
        $this->inStr = false;
        $this->objectPos = -1;
        $this->arrayPos = -1;
    }

    protected function doFix($json)
    {
        [$index, $char] = [-1, ''];

        while (isset($json[++$index])) {
            [$prev, $char] = [$char, $json[$index]];

            $next = $json[$index + 1] ?? '';

            if (! in_array($char, [' ', "\n", "\r"], true)) {
                $this->stack($prev, $char, $index, $next);
            }
        }

        return $this->fixOrFail($json);
    }

    protected function stack($prev, $char, $index, $next): void
    {
        if ($this->maybeStr($prev, $char, $index)) {
            return;
        }

        $last = $this->lastToken();

        if (in_array($last, [',', ':', '"'], true) && preg_match('/\"|\d|\{|\[|t|f|n/', $char)) {
            $this->popToken();
        }

        if (in_array($char, [',', ':', '[', '{'], true)) {
            $this->stack[$index] = $char;
        }

        $this->updatePos($char, $index);
    }

    protected function maybeStr($prev, $char, $index): bool
    {
        if ($prev !== '\\' && $char === '"') {
            $this->inStr = ! $this->inStr;
        }

        if ($this->inStr && $this->lastToken() !== '"') {
            $this->stack[$index] = '"';
        }

        return $this->inStr;
    }

    protected function lastToken()
    {
        return end($this->stack);
    }

    /**
     * @noinspection OffsetOperationsInspection
     *
     * @param null|mixed $token
     */
    protected function popToken($token = null)
    {
        // Last one
        if ($token === null) {
            return array_pop($this->stack);
        }

        $keys = array_reverse(array_keys($this->stack));
        foreach ($keys as $key) {
            if ($this->stack[$key] === $token) {
                unset($this->stack[$key]);

                break;
            }
        }
    }

    protected function updatePos($char, int $index): void
    {
        if ($char === '{') {
            $this->objectPos = $index;
        } elseif ($char === '}') {
            $this->popToken('{');
            $this->objectPos = -1;
        } elseif ($char === '[') {
            $this->arrayPos = $index;
        } elseif ($char === ']') {
            $this->popToken('[');
            $this->arrayPos = -1;
        }
    }

    protected function fixOrFail($json)
    {
        $length = strlen($json);
        $tmpJson = $this->pad($json);

        if ($this->isValid($tmpJson)) {
            return $tmpJson;
        }

        if ($this->silent) {
            return $json;
        }

        throw new RuntimeException(sprintf('Could not fix JSON (tried padding `%s`)', substr($tmpJson, $length)));
    }

    protected function padLiteral($tmpJson)
    {
        if ($this->inStr) {
            return $tmpJson;
        }

        $match = preg_match('/(tr?u?e?|fa?l?s?e?|nu?l?l?)$/', $tmpJson, $matches);

        if (! $match || null === $literal = $this->maybeLiteral($matches[1])) {
            return $tmpJson;
        }

        return substr($tmpJson, 0, -strlen($matches[1])) . $literal;
    }

    protected function padObject($tmpJson)
    {
        if (! $this->objectNeedsPadding($tmpJson)) {
            return $tmpJson;
        }

        $part = substr($tmpJson, $this->objectPos + 1);
        if (preg_match('/(\s*\"[^"]+\"\s*:\s*[^,]+,?)+$/', $part, $matches)) {
            return $tmpJson;
        }

        if ($this->inStr) {
            $tmpJson .= '"';
        }

        $tmpJson = $this->padIf($tmpJson, ':');
        $tmpJson .= $this->missingValue;

        if ($this->lastToken() === '"') {
            $this->popToken();
        }

        return $tmpJson;
    }

    protected function objectNeedsPadding($tmpJson): bool
    {
        $last = substr($tmpJson, -1);
        $empty = $last === '{' && ! $this->inStr;

        return ! $empty && $this->arrayPos < $this->objectPos;
    }

    protected function padIf($string, $substr)
    {
        if (substr($string, -strlen($substr)) !== $substr) {
            return $string . $substr;
        }

        return $string;
    }

    protected function padStack($tmpJson)
    {
        foreach (array_reverse($this->stack, true) as $token) {
            if (isset($this->pairs[$token])) {
                $tmpJson .= $this->pairs[$token];
            }
        }

        return $tmpJson;
    }
}
