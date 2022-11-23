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

namespace App\Container;

use App\Container\Base\BaseContainer;
use App\Exception\ClassNotFoundException;
use App\Exception\TypeInvalidException;
use CurlHandle;
use Stringable;

/**
 * Class Curl
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 0.1.0 (2022-11-23)
 * @since 0.1.0 (2022-11-23) First version.
 */
class Curl extends BaseContainer implements Stringable
{
    /**
     * Curl constructor.
     *
     * @param string $url
     */
    public function __construct(protected string $url)
    {
    }

    /**
     * Returns the url of this container.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->url;
    }

    /**
     * Returns the url of this container.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Sets the url of this container.
     *
     * @param string $url
     * @return self
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Returns the file content as text.
     *
     * @return string
     * @throws ClassNotFoundException
     * @throws TypeInvalidException
     */
    public function getContentAsText(): string
    {
        $curlHandle = curl_init($this->url);

        if (!$curlHandle instanceof CurlHandle) {
            throw new ClassNotFoundException(CurlHandle::class);
        }

        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);

        $data = curl_exec($curlHandle);

        if (!is_string($data)) {
            throw new TypeInvalidException('string', gettype($data));
        }

        curl_close($curlHandle);

        return $data;
    }
}
