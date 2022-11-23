<?php

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
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/**
 * Class CamelCaseToSeparatedConverter
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 */
class CamelCaseToSeparatedConverter implements NameConverterInterface
{
    /**
     * @param array<int, string>|null $attributes
     * @param bool $lowerCamelCase
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function __construct(private readonly ?array $attributes = null, private readonly bool $lowerCamelCase = true)
    {
    }

    /**
     * @param string $propertyName
     * @return string
     * @throws FunctionReplaceException
     */
    public function normalize(string $propertyName): string
    {
        if (null === $this->attributes || in_array($propertyName, $this->attributes)) {
            return (new NamingConventions($propertyName))->getSeparated();
        }

        return $propertyName;


    }

    /**
     * @param string $propertyName
     * @return string
     * @throws FunctionReplaceException
     */
    public function denormalize(string $propertyName): string
    {
        $namingConverter = new NamingConventions($propertyName);

        $camelCasedName = $this->lowerCamelCase ? $namingConverter->getCamelCase() : $namingConverter->getPascalCase();

        if (null === $this->attributes || in_array($camelCasedName, $this->attributes)) {
            return $camelCasedName;
        }

        return $propertyName;
    }
}
