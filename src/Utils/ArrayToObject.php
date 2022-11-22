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

namespace App\Utils;

use Exception;

/**
 * Class ArrayToObject
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-31)
 * @package App\Utils
 *
 * @method float|null getAspectRatio()
 * @method int|null getHeight()
 * @method int|null getValign()
 */
class ArrayToObject
{
    protected const KEY_WIDTH = 'width';

    protected const KEY_HEIGHT = 'height';

    protected const KEY_ASPECT_RATIO = 'aspectRatio';

    /**
     * ArrayToObject constructor.
     *
     * @param array<string|int|float|bool> $data
     * @throws Exception
     */
    public function __construct(protected array $data)
    {
    }

    /**
     * Returns if given key exists.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Returns the value from given key.
     *
     * @param string $key
     * @return string|int|float|bool
     * @throws Exception
     */
    public function get(string $key): string|int|float|bool
    {
        $this->translate($key);

        if (!$this->has($key)) {
            throw new Exception(sprintf('Given key "%s" does not exist (%s:%d).', $key, __FILE__, __LINE__));
        }

        return $this->data[$key];
    }

    /**
     * Sets value for given key.
     *
     * @param string $key
     * @param bool|int|float|string $value
     * @return bool|int|float|string
     */
    public function set(string $key, bool|int|float|string $value): bool|int|float|string
    {
        $this->data[$key] = $value;

        return $value;
    }

    /**
     * Returns the value from given key as int.
     *
     * @param string $key
     * @return int
     * @throws Exception
     */
    public function getInt(string $key): int
    {
        return intval($this->get($key));
    }

    /**
     * Returns the value from given key as float.
     *
     * @param string $key
     * @return float
     * @throws Exception
     */
    public function getFloat(string $key): float
    {
        return floatval($this->get($key));
    }

    /**
     * Translate given key.
     *
     * @param string $key
     * @throws Exception
     */
    protected function translate(string $key): void
    {
        if ($this->has($key)) {
            return;
        }

        switch (true) {
            case $key === self::KEY_WIDTH && $this->has(self::KEY_HEIGHT) && $this->has(self::KEY_ASPECT_RATIO):
                $this->set(self::KEY_WIDTH, floor($this->getInt(self::KEY_HEIGHT) * $this->getFloat(self::KEY_ASPECT_RATIO)));
                break;
        }
    }

    /**
     * Returns data values.
     *
     * @param string $name
     * @param mixed[] $arguments
     * @return string|int|float|bool|null
     * @throws Exception
     */
    public function __call(string $name, array $arguments): string|int|float|bool|null
    {
        if (!preg_match('~^get[A-Z][a-z0-9]*~', $name)) {
            throw new Exception(sprintf('Unsupported method name "%s".', $name));
        }

        $key = preg_replace('~^get~', '', $name);
        if ($key === null) {
            throw new Exception(sprintf('Unable to convert name to key (%s:%d).', __FILE__, __LINE__));
        }

        $namingConventionsConverter = new NamingConventionsConverter($key);

        /* Try to find camelcase key. */
        $camelCaseKey = $namingConventionsConverter->getCamelCase();
        if ($this->has($camelCaseKey)) {
            return $this->get($camelCaseKey);
        }

        /* Try to find minus separated key. */
        $separatedKey = $namingConventionsConverter->getSeparated();
        if ($this->has($separatedKey)) {
            return $this->get($separatedKey);
        }

        return null;
    }
}
