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

use App\Service\Entity\PlaceLoaderService;
use App\Utils\GPSConverter;
use App\Utils\SizeConverter;
use App\Utils\StringConverter;
use DateTime;
use DateTimeImmutable;
use Exception;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class ImageDataService
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-04-20)
 * @package App\Command
 */
class ImageDataService
{
    protected bool $debug = false;

    protected bool $verbose = false;

    /** @var array<string, array<string, mixed>>|null $imageDataFull */
    protected ?array $imageDataFull = null;

    final public const WIDTH_TITLE = 30;

    final public const KEY_NAME_FORMAT = 'format';

    final public const KEY_NAME_TITLE = 'title';

    final public const KEY_NAME_UNIT = 'unit';

    final public const KEY_NAME_UNIT_BEFORE = 'unit-before';

    final public const KEY_NAME_VALUE = 'value';

    final public const KEY_NAME_VALUE_ORIGINAL = 'value-original';

    final public const KEY_NAME_VALUE_FORMATTED = 'value-formatted';

    final public const KEY_NAME_VALUE_DATE_TIME = 'value-date-time';

    final public const KEY_NAME_DEVICE_MANUFACTURER = 'device-manufacturer';
    final public const KEY_NAME_DEVICE_MODEL = 'device-model';

    final public const KEY_NAME_EXIF_VERSION = 'exif-version';

    final public const KEY_NAME_IMAGE_APERTURE = 'image-aperture';
    final public const KEY_NAME_IMAGE_DATE_TIME_ORIGINAL = 'image-date-time-original';
    final public const KEY_NAME_IMAGE_EXPOSURE_BIAS_VALUE = 'image-exposure-bias-value';
    final public const KEY_NAME_IMAGE_EXPOSURE_TIME = 'image-exposure-time';
    final public const KEY_NAME_IMAGE_FILENAME = 'image-filename';
    final public const KEY_NAME_IMAGE_FOCAL_LENGTH = 'image-focal-length';
    final public const KEY_NAME_IMAGE_HEIGHT = 'image-height';
    final public const KEY_NAME_IMAGE_ISO = 'image-iso';
    final public const KEY_NAME_IMAGE_MIME = 'image-mime';
    final public const KEY_NAME_IMAGE_SIZE = 'image-size';
    final public const KEY_NAME_IMAGE_SIZE_HUMAN = 'image-size-human';
    final public const KEY_NAME_IMAGE_WIDTH = 'image-width';
    final public const KEY_NAME_IMAGE_X_RESOLUTION = 'image-x-resolution';
    final public const KEY_NAME_IMAGE_Y_RESOLUTION = 'image-y-resolution';

    final public const KEY_NAME_GPS_GOOGLE_LINK = 'gps-google-link';
    final public const KEY_NAME_GPS_HEIGHT = 'gps-height';
    final public const KEY_NAME_GPS_LATITUDE_DMS = 'gps-latitude-dms';
    final public const KEY_NAME_GPS_LATITUDE_DECIMAL_DEGREE = 'gps-latitude-decimal-degree';
    final public const KEY_NAME_GPS_LATITUDE_DIRECTION = 'gps-latitude-direction';
    final public const KEY_NAME_GPS_LONGITUDE_DMS = 'gps-longitude-dms';
    final public const KEY_NAME_GPS_LONGITUDE_DECIMAL_DEGREE = 'gps-longitude-decimal-degree';
    final public const KEY_NAME_GPS_LONGITUDE_DIRECTION = 'gps-longitude-direction';

    /**
     * ImageData constructor.
     *
     * @param string $imagePath
     * @param PlaceLoaderService|null $placeLoaderService
     * @param LocationDataService|null $locationDataService
     * @param bool $debug
     * @param bool $verbose
     */
    public function __construct(protected string $imagePath, protected ?\App\Service\Entity\PlaceLoaderService $placeLoaderService = null, protected ?\App\Service\LocationDataService $locationDataService = null, bool $debug = false, bool $verbose = false)
    {
        $this->placeLoaderService?->setDebug($debug);
        $this->placeLoaderService?->setVerbose($verbose);

        $this->debug = $debug;

        $this->verbose = $verbose;
    }

    /**
     * Returns a single data value.
     *
     * @param string $title
     * @param mixed $value
     * @param string $format
     * @param string|null $unit
     * @param string|null $unitBefore
     * @param string|null $valueFormatted
     * @param array<string, mixed|null> $addValues
     * @return array<string, mixed|null>
     */
    #[ArrayShape([self::KEY_NAME_TITLE => "string", self::KEY_NAME_FORMAT => "string", self::KEY_NAME_UNIT => "null|string", self::KEY_NAME_UNIT_BEFORE => "null|string", self::KEY_NAME_VALUE => "mixed", self::KEY_NAME_VALUE_FORMATTED => "string"])]
    protected function getData(string $title, mixed $value, string $format, ?string $unit, ?string $unitBefore = null, ?string $valueFormatted = null, array $addValues = null): array
    {
        $data = [
            self::KEY_NAME_TITLE => $title,
            self::KEY_NAME_FORMAT => $format,
            self::KEY_NAME_UNIT => $unit,
            self::KEY_NAME_UNIT_BEFORE => $unitBefore,
            self::KEY_NAME_VALUE => $value,
            self::KEY_NAME_VALUE_FORMATTED => sprintf('%s%s%s', $unitBefore, $valueFormatted ?? strval($value), $unit),
        ];

        if ($addValues !== null) {
            $data = array_merge($data, $addValues);
        }

        return $data;
    }

    /**
     * @param string[] $coordinate
     * @param string $ref
     * @return string
     * @throws Exception
     */
    protected function getCoordinate(array $coordinate, string $ref): string
    {
        $value1 = StringConverter::calculate($coordinate[0]);
        $value2 = StringConverter::calculate($coordinate[1]);
        $value3 = StringConverter::calculate($coordinate[2]);

        /* value3 given with value2 (from decimal points) */
        if (gettype($value2) === 'double') {
            $value3 += 60 * ($value2 - floor($value2));
        }

        return sprintf('%d°%d’%.2f"%s', $value1, $value2, $value3, $ref);
    }

    /**
     * Returns the exif data from given image path.
     *
     * @return array<string, array<string, mixed>>
     * @throws Exception
     */
    public function getDataExif(): array
    {
        $dataExif = @exif_read_data($this->imagePath, 'EXIF');

        if ($dataExif === false) {
            return [];
        }

        if ($this->debug) {
            print_r($dataExif);
        }

        $dataExifReturn = [];

        /* Device properties */
        if (array_key_exists('Make', $dataExif)) {
            $dataExifReturn[self::KEY_NAME_DEVICE_MANUFACTURER] = $this->getData('Device Manufacturer', strval($dataExif['Make']), '%s', null);
        }
        if (array_key_exists('Model', $dataExif)) {
            $dataExifReturn[self::KEY_NAME_DEVICE_MODEL] = $this->getData('Device Model', strval($dataExif['Model']), '%s', null);
        }

        /* Exif version */
        if (array_key_exists('ExifVersion', $dataExif)) {
            $dataExifReturn[self::KEY_NAME_EXIF_VERSION] = $this->getData('Exif Version', strval($dataExif['ExifVersion']), '%s', null);
        }

        /* GPS properties */
        if (array_key_exists('GPSAltitude', $dataExif)) {
            $dataExifReturn[self::KEY_NAME_GPS_HEIGHT] = $this->getData('GPS Height', StringConverter::calculate($dataExif['GPSAltitude']), '%.2f', ' m');
        }
        if (array_key_exists('GPSLatitude', $dataExif) && array_key_exists('GPSLongitude', $dataExif)) {
            $dataExifReturn[self::KEY_NAME_GPS_LATITUDE_DMS] = $this->getData('GPS Latitude DMS', $this->getCoordinate($dataExif['GPSLatitude'], $dataExif['GPSLatitudeRef']), '%s', null);
            $latitudeDirection = GPSConverter::dms2Direction(strval($dataExifReturn[self::KEY_NAME_GPS_LATITUDE_DMS]['value']));
            $dataExifReturn[self::KEY_NAME_GPS_LATITUDE_DECIMAL_DEGREE] = $this->getData('GPS Latitude Decimal Degree', GPSConverter::dms2DecimalDegree(strval($dataExifReturn[self::KEY_NAME_GPS_LATITUDE_DMS]['value']), $latitudeDirection), '%s', '°');
            $dataExifReturn[self::KEY_NAME_GPS_LATITUDE_DIRECTION] = $this->getData('GPS Latitude Direction', $latitudeDirection, '%s', null);

            $dataExifReturn[self::KEY_NAME_GPS_LONGITUDE_DMS] = $this->getData('GPS Longitude', $this->getCoordinate($dataExif['GPSLongitude'], $dataExif['GPSLongitudeRef']), '%s', null);
            $longitudeDirection = GPSConverter::dms2Direction(strval($dataExifReturn[self::KEY_NAME_GPS_LONGITUDE_DMS]['value']));
            $dataExifReturn[self::KEY_NAME_GPS_LONGITUDE_DECIMAL_DEGREE] = $this->getData('GPS Longitude Decimal Degree', GPSConverter::dms2DecimalDegree(strval($dataExifReturn[self::KEY_NAME_GPS_LONGITUDE_DMS]['value']), $longitudeDirection), '%s', '°');
            $dataExifReturn[self::KEY_NAME_GPS_LONGITUDE_DIRECTION] = $this->getData('GPS Longitude Direction', $longitudeDirection, '%s', null);

            $dataExifReturn[self::KEY_NAME_GPS_GOOGLE_LINK] = $this->getData('GPS Google', GPSConverter::decimalDegree2GoogleLink(
                floatval($dataExifReturn[self::KEY_NAME_GPS_LATITUDE_DECIMAL_DEGREE]['value']),
                floatval($dataExifReturn[self::KEY_NAME_GPS_LONGITUDE_DECIMAL_DEGREE]['value']),
                strval($dataExifReturn[self::KEY_NAME_GPS_LATITUDE_DIRECTION]['value']),
                strval($dataExifReturn[self::KEY_NAME_GPS_LONGITUDE_DIRECTION]['value'])
            ), '%s', null);
        }

        /* Image properties */
        if (array_key_exists('FNumber', $dataExif)) {
            $dataExifReturn[self::KEY_NAME_IMAGE_APERTURE] = $this->getData('Image Aperture', StringConverter::calculate($dataExif['FNumber']), '%.1f', null, 'F/');
        }
        if (array_key_exists('DateTimeOriginal', $dataExif)) {
            $dataExifReturn[self::KEY_NAME_IMAGE_DATE_TIME_ORIGINAL] = $this->getData(
                'Image Date Time Original',
                StringConverter::convertDateTimeFormat($dataExif['DateTimeOriginal'], 'Y-m-d\\TH:i:s'),
                '%s',
                null,
                null,
                null,
                [
                    self::KEY_NAME_VALUE_DATE_TIME => StringConverter::convertDateTime($dataExif['DateTimeOriginal']),
                ]
            );
        }
        if (array_key_exists('ExposureBiasValue', $dataExif)) {
            $dataExifReturn[self::KEY_NAME_IMAGE_EXPOSURE_BIAS_VALUE] =  $this->getData('Image Exposure Bias Value', StringConverter::calculate($dataExif['ExposureBiasValue']), '%d', ' steps');
        }
        if (array_key_exists('ExposureTime', $dataExif)) {
            $dataExifReturn[self::KEY_NAME_IMAGE_EXPOSURE_TIME] =  $this->getData(
                'Image Exposure Time',
                StringConverter::calculate($dataExif['ExposureTime'], 5),
                '%s',
                ' s',
                null,
                StringConverter::optimizeSlashString($dataExif['ExposureTime']),
                [
                    self::KEY_NAME_VALUE_ORIGINAL => StringConverter::optimizeSlashString($dataExif['ExposureTime'])
                ]
            );
        }
        if (array_key_exists('FileName', $dataExif)) {
            $dataExifReturn[self::KEY_NAME_IMAGE_FILENAME] = $this->getData('Image Filename', strval($dataExif['FileName']), '%s', null);
        }
        if (array_key_exists('FocalLength', $dataExif)) {
            $dataExifReturn[self::KEY_NAME_IMAGE_FOCAL_LENGTH] =  $this->getData('Image Focal Length', StringConverter::calculate($dataExif['FocalLength']), '%d', ' mm');
        }
        if (array_key_exists('ImageLength', $dataExif)) {
            $dataExifReturn[self::KEY_NAME_IMAGE_HEIGHT] = $this->getData('Image Height', intval($dataExif['ImageLength']), '%d', ' px');
        }
        if (array_key_exists('ISOSpeedRatings', $dataExif)) {
            $dataExifReturn[self::KEY_NAME_IMAGE_ISO] = $this->getData('Image ISO', intval($dataExif['ISOSpeedRatings']), '%d', null, 'ISO-');
        }
        if (array_key_exists('ImageWidth', $dataExif)) {
            $dataExifReturn[self::KEY_NAME_IMAGE_WIDTH] = $this->getData('Image Width', intval($dataExif['ImageWidth']), '%d', ' px');
        }
        if (array_key_exists('XResolution', $dataExif)) {
            $dataExifReturn[self::KEY_NAME_IMAGE_X_RESOLUTION] = $this->getData('Image X-Resolution', StringConverter::calculate($dataExif['XResolution']), '%d', ' dpi');
        }
        if (array_key_exists('YResolution', $dataExif)) {
            $dataExifReturn[self::KEY_NAME_IMAGE_Y_RESOLUTION] = $this->getData('Image Y-Resolution', StringConverter::calculate($dataExif['YResolution']), '%d', ' dpi');
        }

        if ($this->debug) {
            print_r($dataExifReturn);
        }

        return $dataExifReturn;
    }

    /**
     * Gets full image data.
     *
     * @param bool $force
     * @param bool $set
     * @return array<string, array<string, mixed>>
     * @throws Exception
     */
    public function getImageDataFull(bool $force = true, bool $set = false): array
    {
        /* Get cache data */
        if ($this->imageDataFull !== null && !$force) {
            return $this->imageDataFull;
        }

        $data = $this->getDataExif();

        $imageSize = getimagesize($this->imagePath);

        if ($imageSize === false) {
            throw new Exception(sprintf('Unable to get image size (%s:%d).', __FILE__, __LINE__));
        }

        [$width, $height] = $imageSize;

        $imageMime = mime_content_type($this->imagePath);

        if ($imageMime === false) {
            throw new Exception(sprintf('Unable to get image mime (%s:%d).', __FILE__, __LINE__));
        }

        $fileSize = filesize($this->imagePath);

        if ($fileSize === false) {
            throw new Exception(sprintf('Unable to get file size (%s:%d).', __FILE__, __LINE__));
        }

        /* Add image width and height. */
        $data[self::KEY_NAME_IMAGE_WIDTH] = $this->getData('Image Width', intval($width), '%d', ' px');
        $data[self::KEY_NAME_IMAGE_HEIGHT] = $this->getData('Image Height', intval($height), '%d', ' px');

        /* Add image mime. */
        $data[self::KEY_NAME_IMAGE_MIME] = $this->getData('Image Mime', $imageMime, '%s', null);

        /* Add image size. */
        $data[self::KEY_NAME_IMAGE_SIZE] = $this->getData('Image Size', $fileSize, '%d', ' Bytes');
        $data[self::KEY_NAME_IMAGE_SIZE_HUMAN] = $this->getData('Image Size Human', SizeConverter::getHumanReadableSize($fileSize), '%s', null);

        /* Add place information. */
        if ($this->locationDataService !== null && array_key_exists('gps-latitude-decimal-degree', $data) && array_key_exists('gps-longitude-decimal-degree', $data)) {
            $latitude = floatval($data['gps-latitude-decimal-degree']['value']);
            $longitude = floatval($data['gps-longitude-decimal-degree']['value']);

            $dataPlaceInformation = $this->locationDataService->getLocationDataFull($latitude, $longitude);

            $data = [...$data, ...$dataPlaceInformation];
        }

        /* Sort by key */
        ksort($data);

        /* Cache data */
        if ($set) {
            $this->imageDataFull = $data;
        }

        return $data;
    }

    /**
     * Gets image data.
     *
     * @return array<string, mixed>
     * @throws Exception
     */
    public function getImageData(): array
    {
        $imageData = $this->getImageDataFull();

        $array = [];

        foreach ($imageData as $key => $data) {
            $array[$key] = $data['value'];
        }

        return $array;
    }

    /**
     * Gets area of data.
     *
     * @param string $key
     * @return array<string, mixed>|null
     * @throws Exception
     */
    public function getAreaData(string $key): ?array
    {
        $imageDataFull = $this->getImageDataFull(false);

        if (!array_key_exists($key, $imageDataFull)) {
            return null;
        }

        return $imageDataFull[$key];
    }

    /**
     * Get full place value.
     *
     * @param string $key
     * @return string|null
     * @throws Exception
     */
    public function getValue(string $key): ?string
    {
        $areaData = $this->getAreaData($key);

        if ($areaData === null) {
            return null;
        }

        if (!array_key_exists(self::KEY_NAME_VALUE, $areaData)) {
            return null;
        }

        return strval($areaData[self::KEY_NAME_VALUE]);
    }

    /**
     * Get full place value as floatval.
     *
     * @param string $key
     * @return float|null
     * @throws Exception
     */
    public function getValueFloat(string $key): ?float
    {
        $areaData = $this->getAreaData($key);

        if ($areaData === null) {
            return null;
        }

        if (!array_key_exists(self::KEY_NAME_VALUE, $areaData)) {
            return null;
        }

        return floatval($areaData[self::KEY_NAME_VALUE]);
    }

    /**
     * Get full place value as integer.
     *
     * @param string $key
     * @return int|null
     * @throws Exception
     */
    public function getValueInt(string $key): ?int
    {
        $areaData = $this->getAreaData($key);

        if ($areaData === null) {
            return null;
        }

        if (!array_key_exists(self::KEY_NAME_VALUE, $areaData)) {
            return null;
        }

        return intval($areaData[self::KEY_NAME_VALUE]);
    }

    /**
     * Get full place value as DateTimeImmutable.
     *
     * @param string $key
     * @return DateTimeImmutable|null
     * @throws Exception
     */
    public function getValueDateTimeImmutable(string $key): ?DateTimeImmutable
    {
        $areaData = $this->getAreaData($key);

        if ($areaData === null) {
            return null;
        }

        if (!array_key_exists(self::KEY_NAME_VALUE_DATE_TIME, $areaData)) {
            return null;
        }

        if ($areaData[self::KEY_NAME_VALUE_DATE_TIME] instanceof DateTime) {
            return DateTimeImmutable::createFromMutable($areaData[self::KEY_NAME_VALUE_DATE_TIME]);
        }

        if ($areaData[self::KEY_NAME_VALUE_DATE_TIME] instanceof DateTimeImmutable) {
            return $areaData[self::KEY_NAME_VALUE_DATE_TIME];
        }

        return null;
    }
}
