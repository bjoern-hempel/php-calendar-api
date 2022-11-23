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

/**
 * Class NamingConventions
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 */
class NamingConventions
{
    /** @var array<int, string> $words */
    protected array $words;

    /**
     * NamingConventionsConverter constructor.
     *
     * @param string|array<int, string> $raw
     * @throws FunctionReplaceException
     */
    public function __construct(protected string|array $raw)
    {
        $this->words = $this->convertRawToWords($raw);
    }

    /**
     * Converts given raw input into words.
     *
     * @param string|array<int, string> $raw
     * @return array<int, string>
     * @throws FunctionReplaceException
     */
    protected function convertRawToWords(string|array $raw): array
    {
        /* Convert array to string */
        if (is_array($raw)) {
            $raw = implode('_', $raw);
        }

        /* Remove first _ or [spaces] */
        $replacePattern = '~(^[ _-]+)~';
        $raw = preg_replace($replacePattern, '', $raw);
        if ($raw === null) {
            throw new FunctionReplaceException($replacePattern);
        }

        /* Convert capitalized letters */
        $replacePattern = '~([A-Z]+)~';
        $raw = preg_replace($replacePattern, ' $1', $raw);
        if ($raw === null) {
            throw new FunctionReplaceException($replacePattern);
        }

        /* Convert all _ or [SPACE] to [SPACE] */
        $replacePattern = '~[ _-]+~';
        $raw = preg_replace($replacePattern, ' ', trim($raw));
        if ($raw === null) {
            throw new FunctionReplaceException($replacePattern);
        }

        /* Build single point of truth */
        return explode(' ', strtolower($raw));
    }

    /**
     * Gets given raw format.
     *
     * @return string|array<int, string>
     */
    public function getRaw(): string|array
    {
        return $this->raw;
    }

    /**
     * Gets converted words (single point of truth).
     *
     * @return array<int, string>
     */
    public function getWords(): array
    {
        return $this->words;
    }

    /**
     * Gets title of internal $this->words array.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return ucwords(implode(' ', $this->words));
    }

    /**
     * Gets PascalCase representation of internal $this->words array.
     *
     * @return string
     */
    public function getPascalCase(): string
    {
        return implode('', array_map(fn ($word) => ucfirst((string) $word), $this->words));
    }

    /**
     * Gets camelCase representation of internal $this->words array.
     *
     * @return string
     */
    public function getCamelCase(): string
    {
        return lcfirst($this->getPascalCase());
    }

    /**
     * Gets under_scored representation of internal $this->words array.
     *
     * @return string
     */
    public function getUnderscored(): string
    {
        return implode('_', $this->words);
    }

    /**
     * Gets config representation of internal $this->words array.
     *
     * @param string $format
     * @return string
     */
    public function getConfig(string $format = '%s'): string
    {
        return sprintf($format, implode('.', $this->words));
    }

    /**
     * Gets CONSTANT representation of internal $this->words array.
     *
     * @return string
     */
    public function getConstant(): string
    {
        return strtoupper($this->getUnderscored());
    }

    /**
     * Gets separated representation of internal $this->words array
     *
     * @param string $separator
     * @return string
     */
    public function getSeparated(string $separator = '-'): string
    {
        return implode($separator, $this->words);
    }
}
