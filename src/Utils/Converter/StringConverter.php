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

namespace App\Utils\Converter;

use App\Exception\FunctionReplaceException;
use App\Tests\Unit\Utils\Converter\StringConverterTest;

/**
 * Class SizeByte
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 * @link StringConverterTest
 */
class StringConverter
{
    /**
     * StringConverter constructor.
     *
     * @param string $value
     */
    public function __construct(protected string $value)
    {
    }

    /**
     * Returns the value as a variablized representation.
     *
     * "Test {test}, {foo} and {bar}." -> "Test `{test}`, `{foo}` and `{bar}`."
     *
     * @return string
     * @throws FunctionReplaceException
     */
    public function getVariablized(): string
    {
        $replacementPattern = '~{([A-Za-z][A-Za-z0-9_-]*)}~';

        $valueReplaced = preg_replace($replacementPattern, '`{\1}`', $this->value);

        if (!is_string($valueReplaced)) {
            throw new FunctionReplaceException($replacementPattern);
        }

        return $valueReplaced;
    }
}
