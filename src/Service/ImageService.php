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

use App\Entity\Image;
use Exception;
use GdImage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ImageService
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-26)
 * @package App\Service
 */
class ImageService
{
    protected KernelInterface $appKernel;

    protected UserLoaderService $userLoaderService;

    protected RequestStack $requestStack;

    protected TranslatorInterface $translator;

    public const TEXT_NOT_GENERATED = 'admin.image.notGenerated';

    public const PATH_FONT = 'data/font/OpenSansCondensed-Bold.ttf';

    /**
     * ImageService constructor
     *
     */
    public function __construct(KernelInterface $appKernel, UserLoaderService $userLoaderService, RequestStack $requestStack, TranslatorInterface $translator)
    {
        $this->appKernel = $appKernel;

        $this->userLoaderService = $userLoaderService;

        $this->requestStack = $requestStack;

        $this->translator = $translator;
    }

    /**
     * Returns current request.
     *
     * @return Request
     * @throws Exception
     */
    protected function getCurrentRequest(): Request
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            throw new Exception(sprintf('Unable to get current request (%s:%d).', __FILE__, __LINE__));
        }

        return $request;
    }

    /**
     * Get entity fqcn from POST request or entity dto.
     *
     * @return bool
     * @throws Exception
     */
    protected function isImagePosted(): bool
    {
        $request = $this->getCurrentRequest();

        if ($request->getMethod() !== Request::METHOD_POST) {
            return false;
        }

        if ($request->get('Image') === null) {
            return false;
        }

        return true;
    }

    /**
     * Creates the source and target directory from user id hash.
     *
     * @param string $idHash
     * @throws Exception
     */
    protected function createSourceAndTargetFromIdHash(string $idHash): void
    {
        $imageDirectory = sprintf('%s/%s/%s/%s', $this->appKernel->getProjectDir(), Image::PATH_DATA, Image::PATH_IMAGES, $idHash);

        $imageDirectorySource = sprintf('%s/%s', $imageDirectory, Image::PATH_TYPE_SOURCE);
        $imageDirectoryTarget = sprintf('%s/%s', $imageDirectory, Image::PATH_TYPE_TARGET);

        if (file_exists($imageDirectorySource) && !is_dir($imageDirectorySource)) {
            throw new Exception(sprintf('Path "%s" does exists but is not a directory (%s:%d).', $imageDirectorySource, __FILE__, __LINE__));
        }

        if (file_exists($imageDirectoryTarget) && !is_dir($imageDirectoryTarget)) {
            throw new Exception(sprintf('Path "%s" does exists but is not a directory (%s:%d).', $imageDirectorySource, __FILE__, __LINE__));
        }

        if (!file_exists($imageDirectorySource)) {
            mkdir($imageDirectorySource, 0775, true);
        }

        if (!file_exists($imageDirectoryTarget)) {
            mkdir($imageDirectoryTarget, 0775, true);
        }

        if (!file_exists($imageDirectorySource)) {
            throw new Exception(sprintf('Unable to create directory "%s" (%s:%d).', $imageDirectorySource, __FILE__, __LINE__));
        }

        if (!file_exists($imageDirectoryTarget)) {
            throw new Exception(sprintf('Unable to create directory "%s" (%s:%d).', $imageDirectoryTarget, __FILE__, __LINE__));
        }
    }

    /**
     * Gets image info.
     *
     * @param string $path
     * @return string[]|int[]
     * @throws Exception
     */
    protected function getImageInfo(string $path): array
    {
        /* Get information about image. */
        $imageInfo = getimagesize($path);

        if ($imageInfo === false) {
            throw new Exception(sprintf('Unable to get image information from "%s" (%s:%d).', $path, __FILE__, __LINE__));
        }

        return $imageInfo;
    }

    /**
     * Creates image from given path.
     *
     * @param string $path
     * @return GdImage
     * @throws Exception
     */
    protected function createGdImageFromGivenPath(string $path): GdImage
    {
        $imageInfo = $this->getImageInfo($path);

        /* Create image. */
        $gdImage = match ($imageInfo[2]) {
            IMAGETYPE_GIF => imagecreatefromgif($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            default => throw new Exception(sprintf('Unsupported image type %d - %s (%s:%d)', $imageInfo[2], $imageInfo['mime'], __FILE__, __LINE__)),
        };

        if ($gdImage === false) {
            throw new Exception(sprintf('Unable to load image (%s:%d).', __FILE__, __LINE__));
        }

        return $gdImage;
    }

    /**
     * Creates an empty image from given width and height.
     *
     * @param int $width
     * @param int $height
     * @return GdImage
     * @throws Exception
     */
    protected function createEmptyGdImage(int $width, int $height): GdImage
    {
        /* Create resized image. */
        $gdImage = imagecreatetruecolor($width, $height);

        if ($gdImage === false) {
            throw new Exception(sprintf('Unable to create image (%s:%d).', __FILE__, __LINE__));
        }

        return $gdImage;
    }

    /**
     * Saves given gd image to path.
     *
     * @param GdImage $image
     * @param string $path
     * @param int $imageType
     * @param string $mimeType
     * @return bool
     * @throws Exception
     */
    protected function saveImage(GdImage $image, string $path, int $imageType = IMAGETYPE_JPEG, string $mimeType = 'image/jpeg'): bool
    {
        /* Create resized image. */
        $status = match ($imageType) {
            IMAGETYPE_GIF => imagegif($image, $path),
            IMAGETYPE_PNG => imagepng($image, $path),
            IMAGETYPE_JPEG => imagejpeg($image, $path),
            default => throw new Exception(sprintf('Unsupported image type %d - %s (%s:%d)', $imageType, $mimeType, __FILE__, __LINE__)),
        };

        /* Check image */
        if ($status === false || !file_exists($path)) {
            throw new Exception(sprintf('Unable to generate picture (%s:%d).', __FILE__, __LINE__));
        }

        return true;
    }

    /**
     * Resizes the given image with given width.
     *
     * @param Image $image
     * @param int $widthResize
     * @param string $type
     * @return string
     * @throws Exception
     */
    protected function resizeImageWidth(Image $image, int $widthResize, string $type = Image::PATH_TYPE_SOURCE): string
    {
        $pathFull = $image->getPath($type, false, false, true, $this->appKernel->getProjectDir());
        $pathResizedFull = $image->getPath($type, false, false, true, $this->appKernel->getProjectDir(), $widthResize);

        /* Get information about image. */
        $imageInfo = $this->getImageInfo($pathFull);

        /* Create image. */
        $imageSource = $this->createGdImageFromGivenPath($pathFull);

        /* Get width and height */
        $width = intval($imageInfo[0]);
        $height = intval($imageInfo[1]);
        $imageType = intval($imageInfo[2]);
        $mimeType = strval($imageInfo['mime']);

        /* Calculate resized image. */
        $heightResize = intval(round($widthResize * $height / $width));

        /* Create resized image. */
        $imageResized = $this->createEmptyGdImage($widthResize, $heightResize);

        /* Copy resized image. */
        imagecopyresampled($imageResized, $imageSource, 0, 0, 0, 0, $widthResize, $heightResize, $width, $height);

        /* Create resized image. */
        $this->saveImage($imageResized, $pathResizedFull, $imageType, $mimeType);

        /* Return relative path */
        return $image->getPath($type, false, false, false, $this->appKernel->getProjectDir(), $widthResize);
    }

    /**
     * Creates a temporary target image.
     *
     * @param Image $image
     * @return string
     * @throws Exception
     */
    protected function createTmpTargetImage(Image $image): string
    {
        $sourcePathFull = $image->getPathSource(true, false, $this->appKernel->getProjectDir());
        $targetPathFullTmp = $image->getPathTarget(true, false, $this->appKernel->getProjectDir(), true);

        /* Get information about image. */
        $imageInfo = $this->getImageInfo($sourcePathFull);

        /* Create image. */
        $sourceImage = $this->createGdImageFromGivenPath($sourcePathFull);

        /* Image properties. */
        $width = intval($imageInfo[0]);
        $height = intval($imageInfo[1]);
        $imageType = intval($imageInfo[2]);
        $mimeType = strval($imageInfo['mime']);
        $color = imagecolorallocate($sourceImage, 255, 255, 0);
        $font = sprintf('%s/%s', $this->appKernel->getProjectDir(), self::PATH_FONT);
        $text = $this->translator->trans(self::TEXT_NOT_GENERATED);
        $fontSize = intval($height / 20);
        $angle = intval(atan($height / $width) / pi() * 2 * 90);

        /* Calculate box. */
        $box = imageftbbox($fontSize, $angle, $font, $text);

        if ($box === false) {
            throw new Exception(sprintf('Unable to get size (%s:%d).', __FILE__, __LINE__));
        }

        if ($color === false) {
            throw new Exception(sprintf('Unable build color (%s:%d).', __FILE__, __LINE__));
        }

        /* Get box. */
        list($left, $bottom, $right, , , $top) = $box;

        /* Calculate center. */
        $centerX = intval($width / 2);
        $centerY = intval($height / 2);

        /* Calculate offset. */
        $left_offset = intval(($right - $left) / 2);
        $top_offset = intval(($bottom - $top) / 2);

        /* Calculate position. */
        $x = $centerX - $left_offset;
        $y = $centerY + $top_offset;

        /* Add text. */
        imagettftext($sourceImage, $fontSize, $angle, $x, $y, $color, $font, $text);

        /* Create tmp image. */
        $this->saveImage($sourceImage, $targetPathFullTmp, $imageType, $mimeType);

        /* Return relative path */
        return $image->getPathTarget(false, false, '', true);
    }

    /**
     * Checks source image.
     *
     * @param Image $image
     * @throws Exception
     */
    public function checkSourceImage(Image $image): void
    {
        $sourcePathFull = $image->getPathSource(true, false, $this->appKernel->getProjectDir());

        if (!file_exists($sourcePathFull)) {
            throw new Exception(sprintf('Unable to load source image "%s" (%s:%d)', $sourcePathFull, __FILE__, __LINE__));
        }
    }

    /**
     * Checks target image.
     *
     * @param Image $image
     * @throws Exception
     */
    public function checkTargetImage(Image $image): void
    {
        $targetPathFull = $image->getPathSource(true, false, $this->appKernel->getProjectDir());

        if (!file_exists($targetPathFull)) {
            throw new Exception(sprintf('Unable to load target image "%s" (%s:%d)', $targetPathFull, __FILE__, __LINE__));
        }
    }

    /**
     * Creates source image (unsupported method).
     *
     * @param Image $image
     * @return void
     * @throws Exception
     */
    public function createSourceImage(Image $image): void
    {
        throw new Exception(sprintf('Unsupported method (%s:%d).', __FILE__, __LINE__));
    }

    /**
     * Creates target image.
     *
     * @param Image $image
     * @throws Exception
     */
    public function createTargetImage(Image $image): void
    {
        $this->checkSourceImage($image);

        $targetPathFull = $image->getPathTarget(true, false, $this->appKernel->getProjectDir());

        if (!file_exists($targetPathFull)) {
            $targetPathTmp = $this->createTmpTargetImage($image);

            $this->checkTargetImage($image);

            $image->setPathTarget($targetPathTmp);
        }
    }

    /**
     * Checks source image with given width.
     *
     * @param Image $image
     * @param int $width
     * @throws Exception
     */
    public function createSourceImageWidth(Image $image, int $width): void
    {
        $this->checkSourceImage($image);

        $sourcePathFullWidth = $image->getPathSource(true, false, $this->appKernel->getProjectDir(), false, $width);

        if (!file_exists($sourcePathFullWidth)) {
            $this->resizeImageWidth($image, $width);
        }
    }

    /**
     * Checks source image with given width.
     *
     * @param Image $image
     * @param int $width
     * @throws Exception
     */
    public function createTargetImageWidth(Image $image, int $width): void
    {
        $this->checkTargetImage($image);

        $targetPathFullWidth = $image->getPathTarget(true, false, $this->appKernel->getProjectDir(), false, $width);

        if (!file_exists($targetPathFullWidth)) {
            $this->resizeImageWidth($image, $width, Image::PATH_TYPE_TARGET);
        }
    }

    /**
     * Checks target and source path.
     *
     * @param string $idHash
     * @return bool
     * @throws Exception
     */
    public function checkPath(string $idHash): bool
    {
        $this->createSourceAndTargetFromIdHash($idHash);

        return true;
    }
}
