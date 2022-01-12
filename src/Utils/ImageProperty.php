<?php

declare(strict_types=1);

/*
 * MIT License
 *
 * Copyright (c) 2022 Björn Hempel <bjoern@hempel.li>
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

use App\Entity\Image;
use App\Entity\User;
use App\Service\CalendarBuilderService;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ImageProperty
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-01-12)
 * @package App\Utils
 */
class ImageProperty
{
    public const PATH_IMAGES = 'images';

    public const PATH_TESTS = 'tests';

    public const PATH_DATA = 'data';

    protected KernelInterface $kernel;

    protected string $pathRoot;

    protected string $pathData;

    protected int $width;

    protected int $height;

    protected int $bits;

    protected int $channels;

    protected string $mime;

    protected int $size;

    /**
     * ImageProperty constructor.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        /* Set kernel. */
        $this->kernel = $kernel;

        /* Set root path. */
        $this->pathRoot = $this->kernel->getProjectDir();

        /* Set data path. */
        $this->pathData = sprintf('%s/%s', $this->pathRoot, self::PATH_DATA);
    }

    /**
     * Returns the root path.
     *
     * @return string
     */
    public function getPathRoot(): string
    {
        return $this->pathRoot;
    }

    /**
     * Returns the data path.
     *
     * @return string
     */
    public function getPathData(): string
    {
        return $this->pathData;
    }

    /**
     * Returns the images path.
     *
     * @param bool $test
     * @return string
     */
    #[Pure]
    public function getPathImages(bool $test = false): string
    {
        if ($test) {
            return sprintf('%s/%s/%s', $this->getPathData(), self::PATH_TESTS, self::PATH_IMAGES);
        }

        return sprintf('%s/%s', $this->getPathData(), self::PATH_IMAGES);
    }

    /**
     * Returns the user path.
     *
     * @param User $user
     * @param bool $test
     * @return string
     */
    #[Pure]
    public function getPathUser(User $user, bool $test = false): string
    {
        return sprintf('%s/%s', $this->getPathImages($test), $user->getIdHash());
    }

    /**
     * Returns the image path.
     *
     * @param User $user
     * @param Image $image
     * @param bool $test
     * @return string
     */
    public function getPathImage(User $user, Image $image, bool $test = false): string
    {
        return sprintf('%s/%s', $this->getPathUser($user, $test), $image->getPath());
    }

    /**
     * Checks the given image path.
     *
     * @param string $pathImage
     * @throws Exception
     */
    public static function checkPathImage(string $pathImage): void
    {
        /* Check image path */
        if (!file_exists($pathImage)) {
            throw new Exception(sprintf('Image file not found "%s" (%s:%d)', $pathImage, __FILE__, __LINE__));
        }
    }

    /**
     * Get image dimensions.
     *
     * @param string $pathImage
     * @return array{width: int, height: int, bits: int, channels: int, mime: string}
     * @throws Exception
     */
    #[ArrayShape(['width' => 'int', 'height' => 'int', 'bits' => 'int', 'channels' => 'int', 'mime' => 'string'])]
    public static function getImageDimensions(string $pathImage): array
    {
        /* Check image path. */
        self::checkPathImage($pathImage);

        /* Get image size. */
        $imageSize = getimagesize($pathImage);

        /* Check image size. */
        if ($imageSize === false) {
            throw new Exception(sprintf('Unable to get image size from "%s" (%s:%d).', $pathImage, __FILE__, __LINE__));
        }

        return [
            'width' => intval($imageSize[0]),
            'height' => intval($imageSize[1]),
            'bits' => intval($imageSize['bits']),
            'channels' => intval($imageSize['channels']),
            'mime' => strval($imageSize['mime']),
        ];
    }

    /**
     * Get file size.
     *
     * @param string $pathImage
     * @return int
     * @throws Exception
     */
    public static function getFileSize(string $pathImage): int
    {
        /* Check image path. */
        self::checkPathImage($pathImage);

        /* Get file size. */
        $fileSize = filesize($pathImage);

        /* Check image size. */
        if ($fileSize === false) {
            throw new Exception(sprintf('Unable to get file size from "%s" (%s:%d).', $pathImage, __FILE__, __LINE__));
        }

        return $fileSize;
    }

    /**
     * Initialize the given Image resource.
     *
     * @param User $user
     * @param Image $image
     * @param bool $test
     * @throws Exception
     */
    public function init(User $user, Image $image, bool $test = false): void
    {
        /* Get image path. */
        $pathImage = $this->getPathImage($user, $image, $test);

        /* Check image path. */
        self::checkPathImage($pathImage);

        /* Get image dimensions. */
        list(
            'width' => $this->width,
            'height' => $this->height,
            'bits' => $this->bits,
            'channels' => $this->channels,
            'mime' => $this->mime
        ) = self::getImageDimensions($pathImage);

        /* Get file size. */
        $this->size = self::getFileSize($pathImage);

        /* Set dimensions and size */
        $image->setWidth($this->width);
        $image->setHeight($this->height);
        $image->setSize($this->size);
    }
}
