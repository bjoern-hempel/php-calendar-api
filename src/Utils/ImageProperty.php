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

use App\Entity\CalendarImage;
use App\Entity\Image;
use App\Entity\User;
use App\Service\Entity\PlaceLoaderService;
use App\Service\ImageDataService;
use App\Service\LocationDataService;
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
    final public const PATH_IMAGES = 'images';

    final public const PATH_TESTS = 'tests';

    final public const PATH_DATA = 'data';

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
     * @param PlaceLoaderService|null $placeLoaderService
     * @param LocationDataService|null $locationDataService
     */
    public function __construct(protected KernelInterface $kernel, protected ?\App\Service\Entity\PlaceLoaderService $placeLoaderService = null, protected ?\App\Service\LocationDataService $locationDataService = null)
    {
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
        return $this->getPathImages($test);
    }

    /**
     * Returns the image path.
     *
     * @param CalendarImage|null $calendarImage
     * @param Image|null $image
     * @param User|null $user
     * @param bool $test
     * @param string $type
     * @return string
     * @throws Exception
     */
    public function getPathImage(CalendarImage $calendarImage = null, Image $image = null, User $user = null, bool $test = false, string $type = Image::PATH_TYPE_SOURCE): string
    {
        $format = '%s/%s';

        if ($calendarImage !== null) {
            $image = $calendarImage->getImage();

            if ($image === null) {
                throw new Exception(sprintf('Unable to get Image class (%s:%d).', __FILE__, __LINE__));
            }

            $path = $image->getPath($type);

            if ($path === null) {
                throw new Exception(sprintf('Unexpected null value (%s:%d).', __FILE__, __LINE__));
            }

            return sprintf($format, $this->getPathUser($calendarImage->getUser(), $test), $path);
        }

        if ($image !== null && $user !== null) {
            $path = $image->getPath($type);

            if ($path === null) {
                throw new Exception(sprintf('Unexpected null value (%s:%d).', __FILE__, __LINE__));
            }

            return sprintf($format, $this->getPathUser($user, $test), $path);
        }

        throw new Exception(sprintf('Please specify $calendarImage or $image and $user (%s:%d).', __FILE__, __LINE__));
    }

    /**
     * Returns the image source path.
     *
     * @param CalendarImage|null $calendarImage
     * @param Image|null $image
     * @param User|null $user
     * @param bool $test
     * @return string
     * @throws Exception
     */
    public function getPathImageSource(CalendarImage $calendarImage = null, Image $image = null, User $user = null, bool $test = false): string
    {
        return $this->getPathImage($calendarImage, $image, $user, $test, Image::PATH_TYPE_SOURCE);
    }

    /**
     * Returns the image source path.
     *
     * @param CalendarImage|null $calendarImage
     * @param Image|null $image
     * @param User|null $user
     * @param bool $test
     * @return string
     * @throws Exception
     */
    public function getPathImageTarget(CalendarImage $calendarImage = null, Image $image = null, User $user = null, bool $test = false): string
    {
        return $this->getPathImage($calendarImage, $image, $user, $test, Image::PATH_TYPE_TARGET);
    }

    /**
     * Returns the image source path.
     *
     * @param CalendarImage|null $calendarImage
     * @param Image|null $image
     * @param User|null $user
     * @param bool $test
     * @return string
     * @throws Exception
     */
    public function getPathImageExpected(CalendarImage $calendarImage = null, Image $image = null, User $user = null, bool $test = false): string
    {
        return $this->getPathImage($calendarImage, $image, $user, $test, Image::PATH_TYPE_EXPECTED);
    }

    /**
     * Returns the image source path.
     *
     * @param CalendarImage|null $calendarImage
     * @param Image|null $image
     * @param User|null $user
     * @param bool $test
     * @return string
     * @throws Exception
     */
    public function getPathImageCompare(CalendarImage $calendarImage = null, Image $image = null, User $user = null, bool $test = false): string
    {
        return $this->getPathImage($calendarImage, $image, $user, $test, Image::PATH_TYPE_COMPARE);
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
            'bits' => array_key_exists('bits', $imageSize) ? intval($imageSize['bits']) : 0,
            'channels' => array_key_exists('channels', $imageSize) ? intval($imageSize['channels']) : 0,
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
        $pathImage = $this->getPathImageSource(image: $image, user: $user, test: $test);

        /* Check image path. */
        self::checkPathImage($pathImage);

        /* Get image dimensions. */
        ['width' => $this->width, 'height' => $this->height, 'bits' => $this->bits, 'channels' => $this->channels, 'mime' => $this->mime] = self::getImageDimensions($pathImage);

        /* Get file size. */
        $this->size = self::getFileSize($pathImage);

        /* Set dimensions and size */
        $image->setWidth($this->width);
        $image->setHeight($this->height);
        $image->setSize($this->size);

        /* To get mor image data we need the following services. */
        if ($this->placeLoaderService === null || $this->locationDataService === null) {
            return;
        }

        $imageData = new ImageDataService(
            $pathImage,
            $this->placeLoaderService,
            $this->locationDataService,
            false,
            false
        );

        $image->setInformation($imageData->getImageData());

        $gpsLatitudeDecimalDegree = $imageData->getValueFloat(ImageDataService::KEY_NAME_GPS_LATITUDE_DECIMAL_DEGREE);
        if ($gpsLatitudeDecimalDegree !== null) {
            $image->setLatitude($gpsLatitudeDecimalDegree);
        }

        $gpsLongitudeDecimalDegree = $imageData->getValueFloat(ImageDataService::KEY_NAME_GPS_LONGITUDE_DECIMAL_DEGREE);
        if ($gpsLongitudeDecimalDegree !== null) {
            $image->setLongitude($gpsLongitudeDecimalDegree);
        }

        $url = $imageData->getValue(LocationDataService::KEY_NAME_PLACE_GOOGLE_LINK);
        if (empty($image->getUrl()) && $url !== null) {
            $image->setUrl($url);
        }

        $height = $imageData->getValueInt(LocationDataService::KEY_NAME_PLACE_DEM);
        if ($height !== null) {
            $image->setGpsHeight($height);
        }

        $iso = $imageData->getValueInt(ImageDataService::KEY_NAME_IMAGE_ISO);
        if ($iso !== null) {
            $image->setIso($iso);
        }

        $mime = $imageData->getValue(ImageDataService::KEY_NAME_IMAGE_MIME);
        if ($mime !== null) {
            $image->setMime($mime);
        }

        $placeFullValue = $imageData->getValue(LocationDataService::KEY_NAME_PLACE_FULL);
        if ($placeFullValue !== null) {
            $image->setPlace($placeFullValue);
        }
        $placeDistrict = $imageData->getValue(LocationDataService::KEY_NAME_PLACE_DISTRICT);
        if ($placeDistrict !== null) {
            $image->setPlaceDistrict($placeDistrict);
        }
        $placeCity = $imageData->getValue(LocationDataService::KEY_NAME_PLACE_CITY);
        if ($placeCity !== null) {
            $image->setPlaceCity($placeCity);
        }
        $placeState = $imageData->getValue(LocationDataService::KEY_NAME_PLACE_STATE);
        if ($placeState !== null) {
            $image->setPlaceState($placeState);
        }
        $placeCountry = $imageData->getValue(LocationDataService::KEY_NAME_PLACE_COUNTRY);
        if ($placeCountry !== null) {
            $image->setPlaceCountry($placeCountry);
        }
        $placeTimezone = $imageData->getValue(LocationDataService::KEY_NAME_PLACE_TIMEZONE);
        if ($placeTimezone !== null) {
            $image->setPlaceTimezone($placeTimezone);
        }

        $takenAt = $imageData->getValueDateTimeImmutable(ImageDataService::KEY_NAME_IMAGE_DATE_TIME_ORIGINAL);
        if ($takenAt !== null) {
            $image->setTakenAt($takenAt);
        }

        if (empty($image->getTitle()) && !empty($image->getPlace())) {
            $image->setTitle($image->getPlace());
        }
    }
}
