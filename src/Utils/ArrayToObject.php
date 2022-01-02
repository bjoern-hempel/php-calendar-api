<?php declare(strict_types=1);

/*
 * MIT License
 *
 * Copyright (c) 2021 Björn Hempel <bjoern@hempel.li>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App\Utils;

use Exception;

/**
 * Class ArrayToObject
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-31)
 * @package App\Command
 *
 * @method float|null getAspectRatio()
 * @method int|null getHeight()
 * @method int|null getValign()
 */
class ArrayToObject
{
    /** @var array<string|int|float|bool> $data */
    protected array $data;

    /**
     * ArrayToObject constructor.
     *
     * @param array<string|int|float|bool> $data
     * @throws Exception
     */
    public function __construct(array $data)
    {
        $this->data = $data;
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
        if (!$this->has($key)) {
            throw new Exception(sprintf('Given key "%s" does not exist.', $key));
        }

        return $this->data[$key];
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
