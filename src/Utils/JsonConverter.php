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

use App\Utils\Traits\JsonHelper;
use Exception;

/**
 * Class JsonConverter
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-10)
 * @package App\Utils
 */
class JsonConverter
{
    use JsonHelper;

    public const LINE_BREAK = "\n";

    protected string $json;

    protected bool $isJson;

    /**
     * JsonConverter constructor.
     *
     * @param array<string|int|float|bool> $data
     * @throws Exception
     */
    public function __construct(string|array $data)
    {
        if (is_array($data)) {
            $data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if (!is_string($data)) {
            throw new Exception(sprintf('Type string expected (%s:%d)', __FILE__, __LINE__));
        }

        $this->json = $data;

        $this->isJson = self::isJson($data);
    }

    /**
     * Returns the given raw JSON format.
     *
     * @return string
     * @throws Exception
     */
    public function getRaw(): string
    {
        if (!$this->isJson) {
            throw new Exception(sprintf('The given JSON format is not valid (%s:%d).', __FILE__, __LINE__));
        }

        return $this->json;
    }

    /**
     * Returns beautified JSON string.
     *
     * @param int $indentation
     * @param int $lines
     * @param int $columns
     * @param string $indicant
     * @return string
     *
     * @throws Exception
     */
    public function getBeautified(int $indentation = 4, int $lines = -1, int $columns = -1, string $indicant = '...'): string
    {
        return self::beautifyJson($this->json, $indentation, $lines, $columns, $indicant);
    }
}
