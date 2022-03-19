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

namespace App\Service;

use Exception;

/**
 * Class UrlService
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-03-19)
 * @package App\Utils
 *
 * @method float|null getAspectRatio()
 * @method int|null getValign()
 */
class UrlService
{
    public const SEPARATOR = '/';

    public const REPLACE_ENCODE = '+/=';

    public const REPLACE_DECODE = '._-';

    /**
     * Builds url from given values.
     *
     * @param array<string, string|array<string, string>> $config
     * @param array<string, string|int> $values
     * @param bool $withPath
     * @param bool $encode
     * @return string
     * @throws Exception
     */
    public static function build(array $config, array $values, bool $withPath = false, bool $encode = false): string
    {
        $configParameter = $config['parameter'];
        $configPath = strval($config['path']);

        if (!is_array($configParameter)) {
            throw new Exception(sprintf('Unexpected data format (%s:%d).', __FILE__, __LINE__));
        }

        $parameterKeys = array_keys($configParameter);
        $valueKeys = array_keys($values);

        $missingKeys = array_diff($parameterKeys, $valueKeys);
        $unknownKeys = array_diff($valueKeys, $parameterKeys);

        if (count($missingKeys) > 0) {
            throw new Exception(sprintf('The following keys are missing: %s (%s:%d).', implode(', ', $missingKeys), __FILE__, __LINE__));
        }

        if (count($unknownKeys) > 0) {
            throw new Exception(sprintf('The following keys are unknown: %s (%s:%d).', implode(', ', $unknownKeys), __FILE__, __LINE__));
        }

        foreach ($configParameter as $parameterKey => $parameterType) {
            $value = $values[$parameterKey];
            $valueType = gettype($value);

            if ($valueType !== $parameterType) {
                throw new Exception(sprintf('The type "%s" from key "%s" does not match with given type "%s" (%s:%d).', $valueType, $parameterKey, $parameterType, __FILE__, __LINE__));
            }
        }

        $path = implode(self::SEPARATOR, $values);

        if ($encode) {
            $path = self::base64UrlEncode($path);
        }

        if (!$withPath) {
            return $path;
        }

        return sprintf('%s/%s', $configPath, $path);
    }

    /**
     * Encodes url from given values.
     *
     * @param array<string, string|array<string, string>> $config
     * @param array<string, string|int> $values
     * @param bool $withPath
     * @return string
     * @throws Exception
     */
    public static function encode(array $config, array $values, bool $withPath = false): string
    {
        return self::build($config, $values, $withPath, true);
    }

    /**
     * Parses the given path.
     *
     * @param array<string, string|array<string, string>> $config
     * @param string $path
     * @param bool $decode
     * @return array<string, string|int>
     * @throws Exception
     */
    public static function parse(array $config, string $path, bool $decode = false): array
    {
        $configParameter = $config['parameter'];
        $configPath = strval($config['path']);

        if (!is_array($configParameter)) {
            throw new Exception(sprintf('Unexpected data format (%s:%d).', __FILE__, __LINE__));
        }

        $parameterKeys = array_keys($configParameter);

        $path = preg_replace(sprintf('~^((http[s]://[^%s]+[%s])|[%s])?%s%s~', self::SEPARATOR, self::SEPARATOR, self::SEPARATOR, $configPath, self::SEPARATOR), '', $path);

        if (!is_string($path)) {
            throw new Exception(sprintf('Unable to replace string (%s:%d).', __FILE__, __LINE__));
        }

        if ($decode) {
            $path = self::base64UrlDecode($path);
        }

        $parts = explode(self::SEPARATOR, $path);

        if (!is_array($parts)) {
            throw new Exception(sprintf('Unable to explode given string (%s:%d).', __FILE__, __LINE__));
        }

        if (count($parts) > count($parameterKeys)) {
            throw new Exception(sprintf('Too much parameter given. Expected: %d. Given: %d. (%s:%d)', count($parameterKeys), count($parts), __FILE__, __LINE__));
        }

        if (count($parts) < count($parameterKeys)) {
            throw new Exception(sprintf('Too few parameter given. Expected: %d. Given: %d. (%s:%d)', count($parameterKeys), count($parts), __FILE__, __LINE__));
        }

        $parameters = array_combine($parameterKeys, $parts);

        foreach ($configParameter as $parameterKey => $parameterType) {
            $parameters[$parameterKey] = match ($parameterType) {
                'bool' => boolval($parameters[$parameterKey]),
                'integer' => intval($parameters[$parameterKey]),
                'string' => strval($parameters[$parameterKey]),
                default => throw new Exception(sprintf('Unsupported type (%s:%d).', __FILE__, __LINE__)),
            };
        }

        return $parameters;
    }

    /**
     * Decodes url from given values.
     *
     * @param array<string, string|array<string, string>> $config
     * @param string $path
     * @return array<string, string|int>
     * @throws Exception
     */
    public static function decode(array $config, string $path): array
    {
        return self::parse($config, $path, true);
    }

    /**
     * Url safe base64_encode.
     *
     * @param string $input
     * @return string
     */
    public static function base64UrlEncode(string $input): string
    {
        return strtr(base64_encode($input), self::REPLACE_ENCODE, self::REPLACE_DECODE);
    }

    /**
     * Url safe base64_decode.
     *
     * @param string $input
     * @return string
     */
    public static function base64UrlDecode(string $input): string
    {
        return base64_decode(strtr($input, self::REPLACE_DECODE, self::REPLACE_ENCODE));
    }
}
