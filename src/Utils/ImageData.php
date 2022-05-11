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

use App\Entity\Place;
use App\Repository\PlaceRepository;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Exception;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class ImageData
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-04-20)
 * @package App\Command
 */
class ImageData
{
    protected string $imagePath;

    protected ?PlaceRepository $placeRepository;

    protected const DEBUG = false;

    public const WIDTH_TITLE = 30;

    public const KEY_NAME_FORMAT = 'format';

    public const KEY_NAME_TITLE = 'title';

    public const KEY_NAME_UNIT = 'unit';

    public const KEY_NAME_UNIT_BEFORE = 'unit-before';

    public const KEY_NAME_VALUE = 'value';

    public const KEY_NAME_VALUE_ORIGINAL = 'value-original';

    public const KEY_NAME_VALUE_FORMATTED = 'value-formatted';

    public const KEY_NAME_VALUE_DATE_TIME = 'value-date-time';

    public const KEY_NAME_DEVICE_MANUFACTURER = 'device-manufacturer';
    public const KEY_NAME_DEVICE_MODEL = 'device-model';

    public const KEY_NAME_EXIF_VERSION = 'exif-version';

    public const KEY_NAME_IMAGE_APERTURE = 'image-aperture';
    public const KEY_NAME_IMAGE_DATE_TIME_ORIGINAL = 'image-date-time-original';
    public const KEY_NAME_IMAGE_EXPOSURE_BIAS_VALUE = 'image-exposure-bias-value';
    public const KEY_NAME_IMAGE_EXPOSURE_TIME = 'image-exposure-time';
    public const KEY_NAME_IMAGE_FILENAME = 'image-filename';
    public const KEY_NAME_IMAGE_FOCAL_LENGTH = 'image-focal-length';
    public const KEY_NAME_IMAGE_HEIGHT = 'image-height';
    public const KEY_NAME_IMAGE_ISO = 'image-iso';
    public const KEY_NAME_IMAGE_MIME = 'image-mime';
    public const KEY_NAME_IMAGE_SIZE = 'image-size';
    public const KEY_NAME_IMAGE_SIZE_HUMAN = 'image-size-human';
    public const KEY_NAME_IMAGE_WIDTH = 'image-width';
    public const KEY_NAME_IMAGE_X_RESOLUTION = 'image-x-resolution';
    public const KEY_NAME_IMAGE_Y_RESOLUTION = 'image-y-resolution';

    public const KEY_NAME_GPS_GOOGLE_LINK = 'gps-google-link';
    public const KEY_NAME_GPS_HEIGHT = 'gps-height';
    public const KEY_NAME_GPS_LATITUDE_DMS = 'gps-latitude-dms';
    public const KEY_NAME_GPS_LATITUDE_DECIMAL_DEGREE = 'gps-latitude-decimal-degree';
    public const KEY_NAME_GPS_LATITUDE_DIRECTION = 'gps-latitude-direction';
    public const KEY_NAME_GPS_LONGITUDE_DMS = 'gps-longitude-dms';
    public const KEY_NAME_GPS_LONGITUDE_DECIMAL_DEGREE = 'gps-longitude-decimal-degree';
    public const KEY_NAME_GPS_LONGITUDE_DIRECTION = 'gps-longitude-direction';

    public const KEY_NAME_PLACE = 'place';
    public const KEY_NAME_PLACE_COUNTRY_CODE = 'place-country-code';
    public const KEY_NAME_PLACE_TIMEZONE = 'place-timezone';
    public const KEY_NAME_PLACE_POPULATION = 'place-population';
    public const KEY_NAME_PLACE_ELEVATION = 'place-elevation';
    public const KEY_NAME_PLACE_FEATURE_CLASS = 'place-feature-class';
    public const KEY_NAME_PLACE_FEATURE_CODE = 'place-feature-code';
    public const KEY_NAME_PLACE_DISTANCE = 'place-distance';
    public const KEY_NAME_PLACE_DEM = 'place-dem';
    public const KEY_NAME_PLACE_ADMIN1 = 'place-admin1';
    public const KEY_NAME_PLACE_ADMIN2 = 'place-admin2';
    public const KEY_NAME_PLACE_ADMIN3 = 'place-admin3';
    public const KEY_NAME_PLACE_ADMIN4 = 'place-admin4';

    /**
     * ImageData constructor.
     *
     * @param string $imagePath
     * @param PlaceRepository|null $placeRepository
     */
    public function __construct(string $imagePath, ?PlaceRepository $placeRepository = null)
    {
        $this->imagePath = $imagePath;

        $this->placeRepository = $placeRepository;
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
     * @param array<string, string|mixed|null> $addValues
     * @return array<string, string|mixed|null>
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
            self::KEY_NAME_VALUE_FORMATTED => sprintf('%s%s%s', $unitBefore, $valueFormatted !== null ? $valueFormatted : strval($value), $unit),
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

        return sprintf('%d°%d’%.2f"%s', $value1, $value2, $value3, $ref);
    }

    /**
     * Adds places information.
     *
     * @param array<string, array<string, mixed>> $data
     * @return void
     * @throws DoctrineDBALException
     */
    protected function addPlaceInformation(array &$data): void
    {
        if (!$this->placeRepository instanceof PlaceRepository) {
            return;
        }

        if (array_key_exists('gps-latitude-decimal-degree', $data) && array_key_exists('gps-longitude-decimal-degree', $data)) {
            $latitude = floatval($data['gps-latitude-decimal-degree']['value']);
            $longitude = floatval($data['gps-longitude-decimal-degree']['value']);

            /* PPLX */
            $places = $this->placeRepository->findPlaceByPosition($latitude, $longitude, 1);

//            foreach (PlaceRepository::FEATURE_CLASSES_ALL as $featureClass) {
//                $p = $this->placeRepository->findByPosition($latitude, $longitude, 1, $featureClass);
//
//                if (count($p) <= 0) {
//                    continue;
//                }
//
//                print sprintf('%s: %s', $featureClass, $p[0]->getName());
//                print "\n";
//            }

            if (count($places) <= 0) {
                return;
            }

            $place = $places[0];

            $data = array_merge(
                $data,
                [
                    self::KEY_NAME_PLACE => $this->getData('Place City', $place->getName(), '%s', null),
                    self::KEY_NAME_PLACE_COUNTRY_CODE => $this->getData('Place Country Code', $place->getCountryCode(), '%s', null),
                    self::KEY_NAME_PLACE_TIMEZONE => $this->getData('Place Timezone', $place->getTimezone(), '%s', null),
                    self::KEY_NAME_PLACE_POPULATION => $this->getData('Place Population', $place->getPopulation(), '%s', null),
                    self::KEY_NAME_PLACE_ELEVATION => $this->getData('Place Elevation', $place->getElevation(), '%s', ' m'),
                    self::KEY_NAME_PLACE_FEATURE_CLASS => $this->getData('Place Feature Class', $place->getFeatureClass(), '%s', null),
                    self::KEY_NAME_PLACE_FEATURE_CODE => $this->getData('Place Feature Code', $place->getFeatureCode(), '%s', null),
                    self::KEY_NAME_PLACE_DISTANCE => $this->getData('Place Feature Code', $place->getDistance(), '%s', null),
                    self::KEY_NAME_PLACE_DEM => $this->getData('Digital Elevation Model', $place->getDem(), '%s', null),
                    self::KEY_NAME_PLACE_ADMIN1 => $this->getData('Admin1 Code', $place->getAdmin1Code(), '%s', null),
                    self::KEY_NAME_PLACE_ADMIN2 => $this->getData('Admin2 Code', $place->getAdmin2Code(), '%s', null),
                    self::KEY_NAME_PLACE_ADMIN3 => $this->getData('Admin3 Code', $place->getAdmin3Code(), '%s', null),
                    self::KEY_NAME_PLACE_ADMIN4 => $this->getData('Admin4 Code', $place->getAdmin4Code(), '%s', null),
                ]
            );
        }
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

        /** @phpstan-ignore-next-line → I know that this condition is always false. ;) */
        if (self::DEBUG) {
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

            $dataExifReturn[self::KEY_NAME_GPS_GOOGLE_LINK] = $this->getData('GPS Google', GPSConverter::decimalDegree2google(
                floatval($dataExifReturn[self::KEY_NAME_GPS_LONGITUDE_DECIMAL_DEGREE]['value']),
                floatval($dataExifReturn[self::KEY_NAME_GPS_LATITUDE_DECIMAL_DEGREE]['value']),
                strval($dataExifReturn[self::KEY_NAME_GPS_LONGITUDE_DIRECTION]['value']),
                strval($dataExifReturn[self::KEY_NAME_GPS_LATITUDE_DIRECTION]['value'])
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

        /** @phpstan-ignore-next-line → I know that this condition is always false. ;) */
        if (self::DEBUG) {
            print_r($dataExifReturn);
        }

        return $dataExifReturn;
    }

    /**
     * Gets data of image.
     *
     * @return array<string, array<string, mixed>>
     * @throws Exception
     */
    public function getDataImage(): array
    {
        $data = $this->getDataExif();

        $imageSize = getimagesize($this->imagePath);

        if ($imageSize === false) {
            throw new Exception(sprintf('Unable to get image size (%s:%d).', __FILE__, __LINE__));
        }

        list($width, $height) = $imageSize;

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

        /* Add image mime */
        $data[self::KEY_NAME_IMAGE_MIME] = $this->getData('Image Mime', $imageMime, '%s', null);

        /* Add image size */
        $data[self::KEY_NAME_IMAGE_SIZE] = $this->getData('Image Size', $fileSize, '%d', ' Bytes');
        $data[self::KEY_NAME_IMAGE_SIZE_HUMAN] = $this->getData('Image Size Human', SizeConverter::getHumanReadableSize($fileSize), '%s', null);

        $this->addPlaceInformation($data);

        /* Sort by key */
        ksort($data);

        return $data;
    }

    /**
     * Prints image data.
     *
     * @return void
     * @throws Exception
     */
    public function printDataImage(): void
    {
        $dataImage = $this->getDataImage();

        foreach ($dataImage as $key => $data) {
            $value = $data[self::KEY_NAME_VALUE];

            if (!is_bool($value) && !is_float($value) && !is_int($value) && !is_string($value) && !is_null($value)) {
                throw new Exception(sprintf('Unsupported type "%s" given (%s:%d).', gettype($value), __FILE__, __LINE__));
            }

            $format = sprintf('%%-%ds %%-%ds %%s%s%%s'."\n", self::WIDTH_TITLE, self::WIDTH_TITLE, strval($data[self::KEY_NAME_FORMAT]));
            print sprintf($format, strval($data[self::KEY_NAME_TITLE]), $key, strval($data[self::KEY_NAME_UNIT_BEFORE]), $value, strval($data[self::KEY_NAME_UNIT]));
        }
    }
}
