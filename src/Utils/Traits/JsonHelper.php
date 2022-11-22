<?php

declare(strict_types=1);

/*
 * This file is part of the bjoern-hempel/php-calendar-api project.
 *
 * (c) Björn Hempel <https://www.hempel.li/>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Utils\Traits;

use App\Constant\Constants;
use Exception;

/**
 * Trait JsonHelper
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.1 (2022-11-22)
 * @since 0.1.1 (2022-11-22) Add PHP Magic Number Detector (PHPMND).
 * @since 0.1.0 (2022-02-10) First version.
 * @package App\Utils\Traits
 */
trait JsonHelper
{
    /**
     * Checks if given JSON is a valid JSON format.
     *
     * @param string $json
     * @return bool
     */
    public static function isJson(string $json): bool
    {
        json_decode($json);

        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Returns a decoded JSON array (and null if error occurs).
     *
     * @param string $json
     * @return array<string|int|float|bool>|null
     */
    public static function jsonDecode(string $json): ?array
    {
        if (!self::isJson($json)) {
            return null;
        }

        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($data)) {
            return null;
        }

        return $data;
    }

    /**
     * Returns a decoded JSON array.
     *
     * @param string $json
     * @return array<string|int|float|bool>
     */
    public static function jsonDecodeArray(string $json): array
    {
        $data = self::jsonDecode($json);

        return $data ?? [];
    }

    /**
     * Returns an encoded JSON string.
     *
     * @param array<string|int|float|bool> $data
     * @param bool $beautify
     * @param int $indentation
     * @param int $lines
     * @param int $columns
     * @param string $indicant
     * @return string
     * @throws Exception
     */
    public static function jsonEncode(array $data, bool $beautify = false, int $indentation = 4, int $lines = -1, int $columns = -1, string $indicant = '...'): string
    {
        $encoded = json_encode($data, JSON_THROW_ON_ERROR);

        if (!is_string($encoded)) {
            throw new Exception(sprintf('Unable to encode given data to JSON (%s:%d).', __FILE__, __LINE__));
        }

        if (!$beautify) {
            return $encoded;
        }

        return self::beautifyJson($encoded, $indentation, $lines, $columns, $indicant);
    }

    /**
     * Beautifies given JSON string.
     *
     * @param string $json
     * @param int $indentation
     * @param int $lines
     * @param int $columns
     * @param string $indicant
     * @param string $lineBreak
     * @return string
     * @throws Exception
     */
    public static function beautifyJson(string $json, int $indentation = 4, int $lines = -1, int $columns = -1, string $indicant = '...', string $lineBreak = "\n"): string
    {
        /* Check parameter. */
        if (!in_array($indentation, [2, 4])) {
            throw new Exception(sprintf('Only an indentation of 2 or 4 is allowed. %d given (%s:%d).', $indentation, __FILE__, __LINE__));
        }
        if (empty($lineBreak)) {
            throw new Exception(sprintf('Given line break is empty (%s:%d', __FILE__, __LINE__));
        }

        $isJson = self::isJson($json);

        /* Check json. */
        if (!$isJson) {
            throw new Exception(sprintf('The given JSON format is not valid (%s:%d).', __FILE__, __LINE__));
        }

        $beautified = json_encode(self::jsonDecode($json), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($beautified === false) {
            throw new Exception(sprintf('Unable to encode JSON (%s:%d).', __FILE__, __LINE__));
        }

        /* Reduce indentation. */
        if ($indentation === Constants::INDENTION_2) {
            $beautified = preg_replace_callback('/^ +/m', fn($m) => str_repeat(' ', intval(strlen((string) $m[0]) / 2)), $beautified);
        }

        if (!is_string($beautified)) {
            throw new Exception(sprintf('Unable string format of JSON (%s:%d).', __FILE__, __LINE__));
        }

        if ($lines > Constants::LINES_MINUS_ONE) {
            $jsonLinesAll = explode($lineBreak, $beautified);

            $jsonLines = array_slice($jsonLinesAll, 0, $lines);

            if ($lines < count($jsonLinesAll)) {
                $jsonLines[] = $indicant;
            }

            $beautified = implode($lineBreak, $jsonLines);
        }

        if ($columns > Constants::LINES_MINUS_ONE) {
            $jsonLines = explode($lineBreak, $beautified);

            foreach ($jsonLines as &$jsonLine) {
                if (strlen($jsonLine) > $columns) {
                    $length = max($columns - 3, 0);
                    $jsonLine = substr($jsonLine, 0, $length).$indicant;
                }
            }

            $beautified = implode($lineBreak, $jsonLines);
        }

        return $beautified;
    }
}
