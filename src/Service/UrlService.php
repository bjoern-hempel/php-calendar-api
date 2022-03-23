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

use App\Controller\Base\BaseController;
use App\Entity\Calendar;
use App\Entity\CalendarImage;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
    protected RequestStack $requestStack;

    protected UrlGeneratorInterface $router;

    public const SEPARATOR = '/';

    public const REPLACE_ENCODE = '+/=';

    public const REPLACE_DECODE = '._-';

    /**
     * UrlService constructor.
     *
     * @param RequestStack $requestStack
     * @param UrlGeneratorInterface $router
     */
    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $router)
    {
        $this->requestStack = $requestStack;

        $this->router = $router;
    }

    /**
     * Returns the full and automatically generated URL from given calendar image.
     *
     * @param CalendarImage $calendarImage
     * @param string $defaultHost
     * @return string
     * @throws Exception
     */
    public function getUrl(CalendarImage $calendarImage, string $defaultHost = 'twelvepics.com'): string
    {
        $calendar = $calendarImage->getCalendar();

        if (!$calendar instanceof Calendar) {
            throw new Exception(sprintf('Unable to get calendar (%s:%d).', __FILE__, __LINE__));
        }

        $currentRequest = $this->requestStack->getCurrentRequest();

        if ($currentRequest === null) {
            throw new Exception(sprintf('Unable to get current request (%s:%d).', __FILE__, __LINE__));
        }

        $host = in_array($currentRequest->getHost(), ['localhost', '127.0.0.1']) ? $defaultHost : $currentRequest->getHost();

        $encoded = $this->getEncoded($calendarImage, true);

        $path = match ($calendarImage->getMonth()) {
            0 => $this->router->generate(BaseController::ROUTE_NAME_APP_CALENDAR_INDEX_ENCODED_SHORT, [
                'encoded' => $encoded,
            ]),
            default => $this->router->generate(BaseController::ROUTE_NAME_APP_CALENDAR_DETAIL_ENCODED_SHORT, [
                'encoded' => $encoded,
            ]),
        };

        return sprintf('https://%s%s', $host, $path);
    }

    /**
     * Gets encoded string from given calendar image.
     *
     * @param CalendarImage $calendarImage
     * @param bool $short
     * @return string
     * @throws Exception
     */
    protected function getEncoded(CalendarImage $calendarImage, bool $short = false): string
    {
        $calendar = $calendarImage->getCalendar();

        if (!$calendar instanceof Calendar) {
            throw new Exception(sprintf('Unable to get calendar (%s:%d).', __FILE__, __LINE__));
        }

        return match ($calendarImage->getMonth()) {
            0 => UrlService::encode(BaseController::CONFIG_APP_CALENDAR_INDEX, [
                'hash' => $short ? $calendar->getUser()->getIdHashShort() : $calendar->getUser()->getIdHash(),
                'userId' => intval($calendar->getUser()->getId()),
                'calendarId' => $calendar->getId(),
            ], false, true),
            default => UrlService::encode(BaseController::CONFIG_APP_CALENDAR_DETAIL, [
                'hash' => $short ? $calendar->getUser()->getIdHashShort() : $calendar->getUser()->getIdHash(),
                'userId' => intval($calendar->getUser()->getId()),
                'calendarImageId' => $calendarImage->getId(),
            ], false, true),
        };
    }

    /**
     * Builds url from given values.
     *
     * @param array<string, string|array<string, string>> $config
     * @param array<string, string|int> $values
     * @param bool $withPath
     * @param bool $short
     * @param bool $encode
     * @return string
     * @throws Exception
     */
    public static function build(array $config, array $values, bool $withPath = false, bool $short = false, bool $encode = false): string
    {
        $configParameter = $config['parameter'];
        $configPath = strval($config[$short ? 'pathShort' : 'path']);

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
     * @param bool $short
     * @return string
     * @throws Exception
     */
    public static function encode(array $config, array $values, bool $withPath = false, bool $short = false): string
    {
        return self::build($config, $values, $withPath, $short, true);
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
        $short = !str_contains($path, strval($config['path']));

        $configParameter = $config['parameter'];
        $configPath = strval($config[$short ? 'pathShort' : 'path']);

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
