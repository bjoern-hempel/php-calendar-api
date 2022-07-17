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

use App\Entity\Calendar;
use App\Entity\CalendarImage;
use App\Entity\Event;
use App\Entity\Holiday;
use App\Entity\HolidayGroup;
use App\Entity\Image;
use App\Utils\SizeConverter;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use DateTime;
use Exception;
use GdImage;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CalendarBuilderService
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2021-12-29)
 * @package App\Command
 */
class CalendarBuilderService
{
    protected KernelInterface $appKernel;

    protected float $aspectRatio;

    protected int $width;

    protected int $height;

    protected float $calendarBoxBottomSizeReference = 1/6;

    protected float $calendarBoxBottomSize = 9/48;

    protected int $widthQrCode = 250;

    protected int $heightQrCode = 250;

    protected string $font = 'OpenSansCondensed-Light.ttf';

    protected string $pathRoot;

    protected string $pathData;

    protected string $pathSourceAbsolute;

    protected string $pathTargetAbsolute;

    protected string $pathFont;

    protected int $qualityTarget = 100;

    /* Transparency from 0 (visible) to 100 (invisible). */
    protected int $transparency = 40;

    protected int $year;

    protected int $month;

    protected int $fontSizeTitle = 60;

    protected int $fontSizePosition = 30;

    protected int $fontSizeYear = 100;

    protected int $fontSizeMonth = 220;

    protected int $fontSizeDay = 60;

    protected int $fontSizeTitlePage = 200;

    protected int $fontSizeTitlePageSubtext = 70;

    protected int $fontSizeTitlePageAuthor = 40;

    protected int $padding = 160;

    protected int $maxLength = 28;

    protected string $maxLengthAdd = '...';

    protected string $textTitle;

    protected string $textPosition;

    protected int $dayDistance = 40;

    protected int $widthSource;

    protected int $heightSource;

    protected int $yCalendarBoxBottom;

    protected int $x;

    protected int $y;

    /** @var int[] $colors */
    protected array $colors;

    protected float $zoom = 1.0;

    protected int $valignImage;

    protected string $url;

    /** @var array<string, array{x: int, y: int, align: int, dimension: int[], day: int}> $positionDays */
    protected array $positionDays = [];

    protected GdImage $imageTarget;

    protected GdImage $imageSource;

    protected CalendarImage $calendarImage;

    protected Calendar $calendar;

    protected Image $image;

    protected ?HolidayGroup $holidayGroup = null;

    protected bool $test = false;

    protected bool $useCalendarImagePath = false;

    /** @var array<array{name: string[]}> $eventsAndHolidaysRaw */
    protected array $eventsAndHolidaysRaw = [];

    /** @var array<array{name: string}> $eventsAndHolidays */
    protected array $eventsAndHolidays = [];

    /** @var array<bool> $holidays */
    protected array $holidays = [];

    protected int $qrCodeVersion = 5;

    protected bool $deleteTargetImages = false;

    public const BIRTHDAY_YEAR_NOT_GIVEN = 2100;

    public const ALIGN_LEFT = 1;

    public const ALIGN_CENTER = 2;

    public const ALIGN_RIGHT = 3;

    public const VALIGN_TOP = 1;

    public const VALIGN_BOTTOM = 2;

    public const ZOOM_HEIGHT_100 = 4000;

    public const DAY_SUNDAY = 0;

    public const DAY_MONDAY = 1;

    public const IMAGE_PNG = 'png';

    public const IMAGE_JPG = 'jpg';

    public const EVENT_TYPE_BIRTHDAY = 0;

    public const EVENT_TYPE_EVENT = 1;

    public const EVENT_TYPE_EVENT_GROUP = 2;

    public const DEFAULT_QR_CODE_VERSION = 5;

    /**
     * Calendar constructor
     *
     * @param KernelInterface $appKernel
     */
    public function __construct(KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;
    }

    /**
     * This is a replacement for imageftbbox within test mode to avoid different dimensions within different versions of gd libs.
     * These settings are valid for GD GD Version 2.2.5. Some other versions could return other values for that.
     *
     * @param string $text
     * @param int $fontSize
     * @param int $angle
     * @return array<int, int>
     * @throws Exception
     */
    public function getTestImageftbbox(string $text, int $fontSize, int $angle = 0): array
    {
        $data = [
            "28°08’53.9\"N 15°25’53.0\"W - 6 - 90" => [1, 1, 1, -75, -7, -75, -7, 1, ],
            "01 - 44 - 0" => [0, 2, 48, 2, 48, -44, 0, -44, ],
            "2022 - 20 - 0" => [0, 1, 43, 1, 43, -20, 0, -20, ],
            "16 - 12 - 0" => [0, 1, 14, 1, 14, -13, 0, -13, ],
            "15 - 12 - 0" => [0, 1, 14, 1, 14, -13, 0, -13, ],
            "14 - 12 - 0" => [0, 1, 14, 1, 14, -13, 0, -13, ],
            "13 - 12 - 0" => [0, 1, 14, 1, 14, -13, 0, -13, ],
            "12 - 12 - 0" => [0, 1, 14, 1, 14, -13, 0, -13, ],
            "11 - 12 - 0" => [0, 1, 14, 1, 14, -13, 0, -13, ],
            "10 - 12 - 0" => [0, 1, 14, 1, 14, -13, 0, -13, ],
            "09 - 12 - 0" => [0, 1, 14, 1, 14, -13, 0, -13, ],
            "08 - 12 - 0" => [0, 1, 14, 1, 14, -13, 0, -13, ],
            "07 - 12 - 0" => [0, 1, 14, 1, 14, -13, 0, -13, ],
            "06 - 12 - 0" => [0, 1, 14, 1, 14, -13, 0, -13, ],
            "05 - 12 - 0" => [0, 1, 14, 1, 14, -13, 0, -13, ],
            "04 - 12 - 0" => [0, 1, 14, 1, 14, -13, 0, -13, ],
            "03 - 12 - 0" => [0, 1, 14, 1, 14, -13, 0, -13, ],
            "02 - 12 - 0" => [0, 1, 14, 1, 14, -13, 0, -13, ],
            "01 - 12 - 0" => [0, 1, 14, 1, 14, -13, 0, -13, ],
            "17 - 12 - 0" => [0, 1, 14, 1, 14, -13, 0, -13, ],
            "18 - 12 - 0" => [0, 1, 14, 1, 14, -13, 0, -13, ],
            "19 - 12 - 0" => [0, 1, 14, 1, 14, -13, 0, -13, ],
            "20 - 12 - 0" => [-1, 1, 14, 1, 14, -13, -1, -13, ],
            "21 - 12 - 0" => [-1, 1, 14, 1, 14, -13, -1, -13, ],
            "22 - 12 - 0" => [-1, 1, 14, 1, 14, -13, -1, -13, ],
            "23 - 12 - 0" => [-1, 1, 14, 1, 14, -13, -1, -13, ],
            "24 - 12 - 0" => [-1, 1, 14, 1, 14, -13, -1, -13, ],
            "25 - 12 - 0" => [-1, 1, 14, 1, 14, -13, -1, -13, ],
            "26 - 12 - 0" => [-1, 1, 14, 1, 14, -13, -1, -13, ],
            "27 - 12 - 0" => [-1, 1, 14, 1, 14, -13, -1, -13, ],
            "28 - 12 - 0" => [-1, 1, 14, 1, 14, -13, -1, -13, ],
            "29 - 12 - 0" => [-1, 1, 14, 1, 14, -13, -1, -13, ],
            "30 - 12 - 0" => [-1, 1, 14, 1, 14, -13, -1, -13, ],
            "31 - 12 - 0" => [-1, 1, 14, 1, 14, -13, -1, -13, ],
            "KW 02 > - 6 - 0" => [-1, 1, 22, 1, 22, -7, -1, -7, ],
            "KW 01 > - 6 - 0" => [-1, 1, 22, 1, 22, -7, -1, -7, ],
            "KW 03 > - 6 - 0" => [-1, 1, 22, 1, 22, -7, -1, -7, ],
            "KW 04 > - 6 - 0" => [-1, 1, 22, 1, 22, -7, -1, -7, ],
            "KW 05 > - 6 - 0" => [-1, 1, 22, 1, 22, -7, -1, -7, ],
            "Neujahr - 8 - 80" => [3, 1, 9, -28, -4, -30, -9, 0, ],
        ];

        $key = sprintf('%s - %s - %s', $text, $fontSize, $angle);

        if (!array_key_exists($key, $data)) {
            throw new Exception(sprintf('Data settings missing for key "%s" (%s:%d).', $key, __FILE__, __LINE__));
        }

        return $data[$key];
    }

    /**
     * Gets calendar from given calendar image.
     *
     * @param CalendarImage $calendarImage
     * @return Calendar
     * @throws Exception
     */
    protected function getCalendar(CalendarImage $calendarImage): Calendar
    {
        $calendar = $calendarImage->getCalendar();

        if ($calendar === null) {
            throw new Exception(sprintf('Calendar is missing (%s:%d).', __FILE__, __LINE__));
        }

        return $calendar;
    }

    /**
     * Gets image from given calendar image.
     *
     * @param CalendarImage $calendarImage
     * @return Image
     * @throws Exception
     */
    protected function getImage(CalendarImage $calendarImage): Image
    {
        $image = $calendarImage->getImage();

        if ($image === null) {
            throw new Exception(sprintf('Calendar is missing (%s:%d).', __FILE__, __LINE__));
        }

        return $image;
    }

    /**
     * Init function.
     *
     * @param CalendarImage $calendarImage
     * @param HolidayGroup|null $holidayGroup
     * @param bool $test
     * @param bool $useCalendarImagePath
     * @param int $qualityTarget
     * @param int $qrCodeVersion
     * @param bool $deleteTargetImages
     * @throws Exception
     */
    public function init(CalendarImage $calendarImage, HolidayGroup $holidayGroup = null, bool $test = false, bool $useCalendarImagePath = false, int $qualityTarget = 100, int $qrCodeVersion = self::DEFAULT_QR_CODE_VERSION, bool $deleteTargetImages = false): void
    {
        /* Clear positions */
        $this->positionDays = [];

        /* Test mode */
        $this->test = $test;

        /* Use CalendarImage path */
        $this->useCalendarImagePath = $useCalendarImagePath;

        /* Set quality */
        $this->qualityTarget = $qualityTarget;

        /* Set qr code version */
        $this->qrCodeVersion = $qrCodeVersion;

        /* Set delete target images */
        $this->deleteTargetImages = $deleteTargetImages;

        /* calendar instances */
        $this->calendarImage = $calendarImage;
        $this->holidayGroup = $holidayGroup;
        $this->calendar = $this->getCalendar($this->calendarImage);
        $this->image = $this->getImage($this->calendarImage);

        /* sizes */
        $this->aspectRatio = $this->calendar->getConfigObject()->getAspectRatio() ?? 3 / 2;
        $this->height = $this->calendar->getConfigObject()->getHeight() ?? 4000;
        $this->width = intval(floor($this->height * $this->aspectRatio));

        /* Root path */
        $this->pathRoot = $this->appKernel->getProjectDir();

        /* Font path */
        $this->pathData = sprintf('%s/data', $this->pathRoot);
        $this->pathFont = sprintf('%s/font/%s', $this->pathData, $this->font);

        /* Calculate zoom */
        $this->zoom = $this->height / self::ZOOM_HEIGHT_100;

        /* Calculate sizes */
        $this->fontSizeTitle = $this->getSize($this->fontSizeTitle);
        $this->fontSizePosition = $this->getSize($this->fontSizePosition);
        $this->fontSizeYear = $this->getSize($this->fontSizeYear);
        $this->fontSizeMonth = $this->getSize($this->fontSizeMonth);
        $this->fontSizeDay = $this->getSize($this->fontSizeDay);
        $this->fontSizeTitlePage = $this->getSize($this->fontSizeTitlePage);
        $this->fontSizeTitlePageSubtext = $this->getSize($this->fontSizeTitlePageSubtext);
        $this->fontSizeTitlePageAuthor = $this->getSize($this->fontSizeTitlePageAuthor);
        $this->padding = $this->getSize($this->padding);
        $this->heightQrCode = $this->getSize($this->heightQrCode);
        $this->widthQrCode = $this->getSize($this->widthQrCode);
        $this->dayDistance = $this->getSize($this->dayDistance);
    }

    /**
     * Returns the size depending on the zoom.
     *
     * @param int $size
     * @return int
     */
    protected function getSize(int $size): int
    {
        return intval(round($size * $this->zoom));
    }

    /**
     * Returns the dimension of given text, font size and angle.
     *
     * @param string $text
     * @param int $fontSize
     * @param int $angle
     * @return array{width: int, height: int}
     * @throws Exception
     */
    #[ArrayShape(['width' => "int", 'height' => "int"])]
    protected function getDimension(string $text, int $fontSize, int $angle = 0): array
    {
        $boundingBox = $this->test ? $this->getTestImageftbbox($text, $fontSize, $angle) : imageftbbox($fontSize, $angle, $this->pathFont, $text);

        if ($boundingBox === false) {
            throw new Exception(sprintf('Unable to get bounding box (%s:%d', __FILE__, __LINE__));
        }

        list($leftBottomX, $leftBottomY, $rightBottomX, $rightBottomY, $rightTopX, $rightTopY, $leftTopX, $leftTopY) = $boundingBox;

        return [
            'width' => $rightBottomX - $leftBottomX,
            'height' => $leftBottomY - $rightTopY,
        ];
    }

    /**
     * Prepare method.
     *
     * @throws Exception
     */
    protected function prepare(): void
    {
        $this->createImages();
        $this->createColors();
        $this->calculateVariables();
        $this->createEventsAndHolidays();
    }

    /**
     * Writes target image.
     */
    protected function writeImage(): void
    {
        /* Write image */
        imagejpeg($this->imageTarget, $this->pathTargetAbsolute, $this->qualityTarget);
    }

    /**
     * Destroys all images.
     */
    protected function destroy(): void
    {
        /* Destroy image */
        imagedestroy($this->imageTarget);
        imagedestroy($this->imageSource);
    }

    /**
     * Init x and y.
     *
     * @param int $x
     * @param int $y
     */
    protected function initXY(int $x = 0, int $y = 0): void
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Set x
     *
     * @param int $x
     */
    protected function setX(int $x): void
    {
        $this->x = $x;
    }

    /**
     * Set y
     *
     * @param int $y
     */
    protected function setY(int $y): void
    {
        $this->y = $y;
    }

    /**
     * Add x
     *
     * @param int $x
     */
    protected function addX(int $x): void
    {
        $this->x += $x;
    }

    /**
     * Add y
     *
     * @param int $y
     */
    protected function addY(int $y): void
    {
        $this->y += $y;
    }

    /**
     * Creates an empty image.
     *
     * @param int $width
     * @param int $height
     * @return GdImage
     * @throws Exception
     */
    protected function createImage(int $width, int $height): GdImage
    {
        $image = imagecreatetruecolor($width, $height);

        if ($image === false) {
            throw new Exception(sprintf('Unable to create image (%s:%d)', __FILE__, __LINE__));
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);

        return $image;
    }

    /**
     * Creates image from given filename.
     *
     * @param string $filename
     * @param string $type
     * @return GdImage
     * @throws Exception
     */
    protected function createImageFromImage(string $filename, string $type = self::IMAGE_JPG): GdImage
    {
        if (!file_exists($filename)) {
            throw new Exception(sprintf('Unable to find image "%s" (%s:%d)', $filename, __FILE__, __LINE__));
        }

        $image = match ($type) {
            self::IMAGE_JPG => imagecreatefromjpeg($filename),
            self::IMAGE_PNG => imagecreatefrompng($filename),
            default => throw new Exception(sprintf('Unknown given image type "%s" (%s:%d)', $type, __FILE__, __LINE__)),
        };

        if ($image === false) {
            throw new Exception(sprintf('Unable to create image (%s:%d)', __FILE__, __LINE__));
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);

        return $image;
    }

    /**
     * Creates the GdImage instances.
     * @throws Exception
     */
    protected function createImages(): void
    {
        $this->imageTarget = $this->createImage($this->width, $this->height);
        $this->imageSource = $this->createImageFromImage($this->pathSourceAbsolute);
    }

    /**
     * Create color from given red, green, blue and alpha value.
     *
     * @param GdImage $image
     * @param int $red
     * @param int $green
     * @param int $blue
     * @param int|null $alpha
     * @return int
     * @throws Exception
     */
    protected function createColor(GdImage $image, int $red, int $green, int $blue, ?int $alpha = null): int
    {
        if ($alpha === null) {
            $color = imagecolorallocate($image, $red, $green, $blue);
        } else {
            $color = imagecolorallocatealpha($image, $red, $green, $blue, $alpha);
        }

        if ($color === false) {
            throw new Exception(sprintf('Unable to create color (%s:%d)', __FILE__, __LINE__));
        }

        return $color;
    }

    /**
     * Create the colors and save the integer values to color.
     *
     * @throws Exception
     */
    protected function createColors(): void
    {
        $this->colors = [
            'black' => $this->createColor($this->imageTarget, 0, 0, 0),
            'blackTransparency' => $this->createColor($this->imageTarget, 0, 0, 0, $this->transparency),
            'white' => $this->createColor($this->imageTarget, 255, 255, 255),
            'whiteTransparency' => $this->createColor($this->imageTarget, 255, 255, 255, $this->transparency),
            'red' => $this->createColor($this->imageTarget, 255, 0, 0),
            'redTransparency' => $this->createColor($this->imageTarget, 255, 0, 0, $this->transparency),
        ];
    }

    /**
     * Calculate variables.
     *
     * @throws Exception
     */
    protected function calculateVariables(): void
    {
        $propertiesSource = getimagesize($this->pathSourceAbsolute);

        if ($propertiesSource === false) {
            throw new Exception(sprintf('Unable to get image size (%s:%d)', __FILE__, __LINE__));
        }

        $this->widthSource = $propertiesSource[0];
        $this->heightSource = $propertiesSource[1];

        $this->yCalendarBoxBottom = intval(floor($this->height * (1 - $this->calendarBoxBottomSize)));
    }

    /**
     * Get days for left and write.
     *
     * @return array{left: array<int>, right: array<int>}
     * @throws Exception
     */
    #[ArrayShape(['left' => "array", 'right' => "array"])]
    protected function getDays(): array
    {
        $days = intval((new DateTime(sprintf('%d%02d01', $this->year, $this->month)))->format('t'));

        $dayToLeft = intval(ceil($days / 2));

        return [
            'left' => [
                'from' => 1,
                'to' => $dayToLeft,
            ],
            'right' => [
                'from' => $dayToLeft + 1,
                'to' => $days,
            ]
        ];
    }

    /**
     * Return timestamp from given year, month and year.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return int
     * @throws Exception
     */
    protected function getTimestamp(int $year, int $month, int $day): int
    {
        $timestamp = mktime(12, 0, 0, $month, $day, $year);

        if ($timestamp === false) {
            throw new Exception(sprintf('Unable to create timestamp (%s:%d)', __FILE__, __LINE__));
        }

        return $timestamp;
    }

    /**
     * Returns the day of week.
     * 0 - Sunday
     * 6 - Saturday
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return int
     * @throws Exception
     */
    protected function getDayOfWeek(int $year, int $month, int $day): int
    {
        return intval(date('w', $this->getTimestamp($year, $month, $day)));
    }

    /**
     * Returns the color of given day.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return int
     * @throws Exception
     */
    protected function getDayColor(int $year, int $month, int $day): int
    {
        /* Print day in red if sunday */
        if ($this->getDayOfWeek($year, $month, $day) === self::DAY_SUNDAY) {
            return $this->colors['red'];
        }

        /* Print day in red if holiday */
        $dayKey = $this->getDayKey($day);
        if (array_key_exists($dayKey, $this->holidays) && $this->holidays[$dayKey] === true) {
            return $this->colors['red'];
        }

        /* Print day in white otherwise */
        return $this->colors['white'];
    }

    /**
     * Returns the number of week.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return int
     * @throws Exception
     */
    protected function getWeekNumber(int $year, int $month, int $day): int
    {
        return intval(date('W', $this->getTimestamp($year, $month, $day)));
    }

    /**
     * Returns the number of week if monday. Otherwise, null.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return int|null
     * @throws Exception
     */
    protected function getCalendarWeekIfMonday(int $year, int $month, int $day): ?int
    {
        $dayOfWeek = $this->getDayOfWeek($year, $month, $day);

        if ($dayOfWeek !== self::DAY_MONDAY) {
            return null;
        }

        return $this->getWeekNumber($year, $month, $day);
    }

    /**
     * Returns the y correction for all calendar box.
     *
     * $this->calendarBoxBottomSizeReference is the reference for all positions. Move some elements to keep valign: bottom.
     *
     * @return int
     */
    protected function getCalendarBoxYCorrection(): int
    {
        if ($this->calendarBoxBottomSize === $this->calendarBoxBottomSizeReference) {
            return 0;
        }

        return intval(round($this->height * ($this->calendarBoxBottomSize - $this->calendarBoxBottomSizeReference)));
    }

    /**
     * Returns the day key.
     *
     * @param int $day
     * @return string
     */
    protected function getDayKey(int $day): string
    {
        return sprintf('%04d-%02d-%02d', $this->year, $this->month, $day);
    }

    /**
     * Checks and creates given directory or directory for given file
     *
     * @param string $path
     * @param bool $isFile
     * @return string
     * @throws Exception
     */
    protected function checkAndCreateDirectory(string $path, bool $isFile = false): string
    {
        if ($isFile) {
            $pathToCheck = dirname($path);
        } else {
            $pathToCheck = $path;
        }

        if (!file_exists($pathToCheck)) {
            mkdir($pathToCheck, 0775, true);
        }

        if (!file_exists($pathToCheck)) {
            throw new Exception(sprintf('Unable to create directory "%s" (%s:%d)', $pathToCheck, __FILE__, __LINE__));
        }

        return $pathToCheck;
    }

    /**
     * Add text.
     *
     * @param string $text
     * @param int $fontSize
     * @param ?int $color
     * @param int $paddingTop
     * @param int $align
     * @param int $valign
     * @param int $angle
     * @return array{width: int, height: int}
     * @throws Exception
     */
    #[ArrayShape(['width' => "int", 'height' => "int"])]
    protected function addText(string $text, int $fontSize, int $color = null, int $paddingTop = 0, int $align = self::ALIGN_LEFT, int $valign = self::VALIGN_BOTTOM, int $angle = 0): array
    {
        if ($color === null) {
            $color = $this->colors['white'];
        }

        $dimension = $this->getDimension($text, $fontSize, $angle);

        $x = match ($align) {
            self::ALIGN_CENTER => $this->x - intval(round($dimension['width'] / 2)),
            self::ALIGN_RIGHT => $this->x - $dimension['width'],
            default => $this->x,
        };

        $y = match ($valign) {
            self::VALIGN_TOP => $this->y + $fontSize,
            default => $this->y,
        };

        imagettftext($this->imageTarget, $fontSize, $angle, $x, $y + $paddingTop, $color, $this->pathFont, $text);

        return [
            'width' => $dimension['width'],
            'height' => $fontSize,
        ];
    }

    /**
     * Add image
     */
    protected function addImage(): void
    {
        $y = match ($this->valignImage) {
            self::VALIGN_BOTTOM => $this->yCalendarBoxBottom - $this->height,
            default => 0,
        };

        $x = 0;

        imagecopyresampled($this->imageTarget, $this->imageSource, $x, $y, 0, 0, $this->width, $this->height, $this->widthSource, $this->heightSource);
    }

    /**
     * Add bottom calendar box.
     */
    protected function addRectangle(): void
    {
        /* Add calendar area (rectangle) */
        imagefilledrectangle($this->imageTarget, 0, $this->yCalendarBoxBottom, $this->width, $this->height, $this->colors['blackTransparency']);
    }

    /**
     * Add the title and position.
     *
     * @throws Exception
     */
    protected function addImageDescriptionAndPositionOnCalendarPage(): void
    {
        /* Start y */
        $x = $this->padding;
        $y = $this->yCalendarBoxBottom + $this->padding;

        /* Add title */
        $fontSizeTitle = $this->fontSizeTitle;
        $angleAll = 0;
        imagettftext($this->imageTarget, $this->fontSizeTitle, $angleAll, $x, $y + $fontSizeTitle, $this->colors['white'], $this->pathFont, $this->textTitle);

        /* Add position */
        $anglePosition = 90;
        $dimensionPosition = $this->getDimension($this->textPosition, $this->fontSizePosition, $anglePosition);
        $xPosition = $this->padding + $dimensionPosition['width'] + $this->fontSizePosition;
        $yPosition = $this->yCalendarBoxBottom - $this->padding;
        imagettftext($this->imageTarget, $this->fontSizePosition, $anglePosition, $xPosition, $yPosition, $this->colors['white'], $this->pathFont, $this->textPosition);
    }

    /**
     * Adds the title page elements (instead of the calendar).
     *
     * @throws Exception
     */
    protected function addTitleOnTitlePage(): void
    {
        /* Set x and y */
        $xCenterCalendar = intval(round($this->width / 2));
        $this->initXY($xCenterCalendar, $this->yCalendarBoxBottom + $this->padding + $this->getCalendarBoxYCorrection());

        $paddingTopYear = $this->getSize(0);
        $dimensionYear = $this->addText(sprintf('%s', $this->calendar->getTitle()), $this->fontSizeTitlePage, $this->colors['white'], $paddingTopYear, self::ALIGN_CENTER, self::VALIGN_TOP);
        $this->addY($dimensionYear['height'] + $paddingTopYear);

        $paddingTopSubtext = $this->getSize(40);
        $dimensionYear = $this->addText(sprintf('%s', $this->calendar->getSubtitle()), $this->fontSizeTitlePageSubtext, $this->colors['white'], $paddingTopSubtext, self::ALIGN_CENTER, self::VALIGN_TOP);
        $this->addY($dimensionYear['height'] + $paddingTopSubtext);
    }

    /**
     * Add day to calendar.
     *
     * @param int $day
     * @param int $align
     * @throws Exception
     */
    protected function addDay(int $day, int $align = self::ALIGN_LEFT): void
    {
        /* Add distance for next day and between calendar weeks */
        $calendarWeekDistance = $this->getDayOfWeek($this->year, $this->month, $day) === self::DAY_MONDAY ? $this->dayDistance : 0;

        /* Add x for next day */
        $this->addX($align === self::ALIGN_LEFT ? ($this->dayDistance + $calendarWeekDistance) : -1 * $this->dayDistance);

        /* Add day */
        $color = $this->getDayColor($this->year, $this->month, $day);
        $dimension = $this->addText(sprintf('%02d', $day), $this->fontSizeDay, $color, align: $align);

        /* Save position */
        $this->positionDays[$this->getDayKey($day)] = [
            'x' => $this->x,
            'y' => $this->y,
            'align' => $align,
            'dimension' => $dimension,
            'day' => $day,
        ];

        /* Add x for next day */
        $this->addX($align === self::ALIGN_LEFT ? $dimension['width'] : -1 * ($dimension['width'] + $calendarWeekDistance));
    }

    /**
     * Adds the calendar (year, month and days).
     *
     * @throws Exception
     */
    protected function addYearMonthAndDays(): void
    {
        /* Set x and y */
        $xCenterCalendar = intval(round($this->width / 2) + round($this->width / 8));
        $this->initXY($xCenterCalendar, $this->yCalendarBoxBottom + $this->padding + $this->getCalendarBoxYCorrection());

        /* Add month */
        $paddingTop = $this->getSize(0);
        $dimensionMonth = $this->addText(sprintf('%02d', $this->month), $this->fontSizeMonth, $this->colors['white'], $paddingTop, self::ALIGN_CENTER, self::VALIGN_TOP);
        $this->addY($dimensionMonth['height'] + $paddingTop);

        /* Add year */
        $paddingTop = $this->getSize(20);
        $dimensionYear = $this->addText(sprintf('%s', $this->year), $this->fontSizeYear, $this->colors['white'], $paddingTop, self::ALIGN_CENTER, self::VALIGN_TOP);
        $this->addY($dimensionYear['height'] + $paddingTop);

        /* Add days */
        $days = $this->getDays();

        /* Add first days (left side) */
        $this->setX($xCenterCalendar - intval(round($dimensionYear['width'] / 2)));
        $this->addX(-$this->dayDistance);
        for ($day = $days['left']['to']; $day >= $days['left']['from']; $day--) {
            $this->addDay($day, self::ALIGN_RIGHT);
        }

        /* Add second part of days (right side) */
        $this->setX($xCenterCalendar + intval(round($dimensionYear['width'] / 2)));
        $this->addX($this->dayDistance);
        for ($day = $days['right']['from']; $day <= $days['right']['to']; $day++) {
            $this->addDay($day);
        }
    }

    /**
     * Adds calendar week to day.
     *
     * @param string $dayKey
     * @throws Exception
     */
    protected function addCalendarWeek(string $dayKey): void
    {
        $positionDay = $this->positionDays[$dayKey];
        $day = $positionDay['day'];
        $dimensionDay = $positionDay['dimension'];
        $align = $positionDay['align'];

        $weekNumber = $this->getCalendarWeekIfMonday($this->year, $this->month, $day);

        /* Add calendar week, if day is monday */
        if ($weekNumber === null) {
            return;
        }

        /* Set x and y */
        $this->setX($positionDay['x']);
        $this->setY($positionDay['y']);

        /* Set calendar week position (ALIGN_LEFT -> right side) */
        $this->x -= $align === self::ALIGN_LEFT ? 0 : $dimensionDay['width'];
        $this->y += intval(round(1.0 * $this->fontSizeDay));

        /* Build calendar week text */
        $weekNumberText = sprintf('KW %02d >', $weekNumber);

        /* Add calendar week */
        $this->addText($weekNumberText, intval(ceil($this->fontSizeDay * 0.5)), $this->colors['white']);

        /* Add line */
        $x = $this->x - intval(round($this->dayDistance / 1));
        imageline($this->imageTarget, $x, $this->y, $x, $positionDay['y'] - $this->fontSizeDay, $this->colors['white']);
    }

    /**
     * Adds calendar week to days.
     *
     * @throws Exception
     */
    protected function addCalendarWeeks(): void
    {
        foreach ($this->positionDays as $dayKey => $positionDay) {
            $this->addCalendarWeek($dayKey);
        }
    }

    /**
     * Add holiday or event to day.
     *
     * @param string $dayKey
     * @throws Exception
     */
    protected function addHolidayOrEvent(string $dayKey): void
    {
        $positionDay = $this->positionDays[$dayKey];
        $day = $positionDay['day'];
        $dimensionDay = $positionDay['dimension'];
        $align = $positionDay['align'];

        $dayKey = $this->getDayKey($day);

        if (!array_key_exists($dayKey, $this->eventsAndHolidays)) {
            return;
        }

        $eventOrHoliday = $this->eventsAndHolidays[$dayKey];

        /* Set x and y */
        $this->setX($positionDay['x']);
        $this->setY($positionDay['y']);

        /* Angle and font size */
        $angleEvent = 80;
        $fontSizeEvent = intval(ceil($this->fontSizeDay * 0.6));

        /* Get name */
        $name = strlen($eventOrHoliday['name']) > $this->maxLength ?
            substr($eventOrHoliday['name'], 0, $this->maxLength - strlen($this->maxLengthAdd)).$this->maxLengthAdd :
            $eventOrHoliday['name'];

        /* Dimension Event */
        $xEvent = $fontSizeEvent + intval(round(($dimensionDay['width'] - $fontSizeEvent) / 2));

        /* Set event position */
        $this->x -= $align === self::ALIGN_LEFT ? 0 : $dimensionDay['width'];
        $this->x += $xEvent;
        $this->y -= intval(round(1.5 * $this->fontSizeDay));

        /* Add Event */
        $this->addText(text: $name, fontSize: $fontSizeEvent, color: $this->colors['white'], angle: $angleEvent);
    }

    /**
     * Adds holidays and events to days.
     *
     * @throws Exception
     */
    protected function addHolidaysAndEvents(): void
    {
        foreach ($this->positionDays as $dayKey => $positionDay) {
            $this->addHolidayOrEvent($dayKey);
        }
    }

    /**
     * Adds QR Code.
     *
     * @throws Exception
     */
    protected function addQrCode(): void
    {
        /* Set background color */
        $backgroundColor = [255, 0, 0];

        /* Matrix length of qrCode */
        $matrixLength = 37;

        /* Wanted width (and height) of qrCode */
        $width = 800;

        /* Calculate scale of qrCode */
        $scale = intval(ceil($width / $matrixLength));

        /* Set options for qrCode */
        $options = [
            'eccLevel' => QRCode::ECC_H,
            'outputType' => QRCode::OUTPUT_IMAGICK,
            'version' => $this->qrCodeVersion,
            'addQuietzone' => false,
            'scale' => $scale,
            'markupDark' => '#fff',
            'markupLight' => '#f00',
        ];

        /* Get blob from qrCode image */
        $qrCodeBlob = (new QRCode(new QROptions($options)))->render($this->url);

        /* Create GDImage from blob */
        $imageQrCode = imagecreatefromstring(strval($qrCodeBlob));

        /* Check creating image. */
        if ($imageQrCode === false) {
            throw new Exception(sprintf('An error occurred while creating GDImage from blob (%s:%d)', __FILE__, __LINE__));
        }

        /* Get height from $imageQrCode */
        $widthQrCode  = imagesx($imageQrCode);
        $heightQrCode = imagesy($imageQrCode);

        /* Create transparent color */
        $transparentColor = imagecolorexact($imageQrCode, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);

        /* Set background color to transparent */
        imagecolortransparent($imageQrCode, $transparentColor);

        /* Add dynamically generated qr image to main image */
        imagecopyresized($this->imageTarget, $imageQrCode, $this->padding, $this->height - $this->padding - $this->heightQrCode, 0, 0, $this->widthQrCode, $this->heightQrCode, $widthQrCode, $heightQrCode);

        /* Destroy image. */
        imagedestroy($imageQrCode);
    }

    /**
     * Returns image properties from given image.
     *
     * @param string $pathAbsolute
     * @param string $pathRelative
     * @param string $keyPostfix
     * @return array<string|int>
     * @throws Exception
     */
    protected function getImageProperties(string $pathAbsolute, string $pathRelative, string $keyPostfix = 'Target'): array
    {
        /* Check created image */
        if (!file_exists($pathAbsolute)) {
            throw new Exception(sprintf('Missing file "%s" (%s:%d).', $pathAbsolute, __FILE__, __LINE__));
        }

        /* Get image properties */
        $image = getimagesize($pathAbsolute);

        /* Check image properties */
        if ($image === false) {
            throw new Exception(sprintf('Unable to get file information from "%s" (%s:%d).', $pathAbsolute, __FILE__, __LINE__));
        }

        /* Get file size */
        $sizeByte = filesize($pathAbsolute);

        /* Check image properties */
        if ($sizeByte === false) {
            throw new Exception(sprintf('Unable to get file size from "%s" (%s:%d).', $pathAbsolute, __FILE__, __LINE__));
        }

        /* Return the image properties */
        return [
            sprintf('path%s', $keyPostfix) => $pathAbsolute,
            sprintf('pathRelative%s', $keyPostfix) => $pathRelative,
            sprintf('width%s', $keyPostfix) => intval($image[0]),
            sprintf('height%s', $keyPostfix) => intval($image[1]),
            sprintf('mime%s', $keyPostfix) => strval($image['mime']),
            sprintf('size%s', $keyPostfix) => $sizeByte,
            sprintf('sizeHuman%s', $keyPostfix) => SizeConverter::getHumanReadableSize($sizeByte),
        ];
    }


    /**
     * Returns the year month key.
     *
     * @param int $year
     * @param int $month
     * @return string
     */
    protected function getYearMonthKey(int $year, int $month): string
    {
        return sprintf('%04d-%02d', $year, $month);
    }

    /**
     * Adds given event.
     *
     * @param string $key
     * @param string $name
     * @param bool $holiday
     */
    protected function addEventOrHoliday(string $key, string $name, bool $holiday = false): void
    {
        /* Add new key */
        if (!array_key_exists($key, $this->eventsAndHolidaysRaw)) {
            $this->eventsAndHolidaysRaw[$key] = [
                'name' => [],
            ];
        }

        /* Add name */
        $this->eventsAndHolidaysRaw[$key]['name'][] = $name;

        /* Add holiday */
        $this->holidays[$key] = $holiday;
    }

    /**
     * Adds all events according to this month.
     *
     * @throws Exception
     */
    protected function addEvents(): void
    {
        /* Build current year and month */
        $yearMonthPage = $this->getYearMonthKey($this->year, $this->month);

        /** @var Event $event */
        foreach ($this->calendarImage->getUser()->getEvents() as $event) {

            /* Get event key */
            $eventKey = $this->getDayKey(intval($event->getDate()->format('j')));

            /* Get year from event */
            $year = intval($event->getDate()->format('Y'));

            /* Calculate age from event */
            $age = $this->calendarImage->getYear() - $year;

            /* This event does not fit the month → Skip */
            if ($yearMonthPage !== $this->getYearMonthKey($this->year, intval($event->getDate()->format('n')))) {
                continue;
            }

            /* No birthday event → Only show name */
            if ($event->getType() !== CalendarBuilderService::EVENT_TYPE_BIRTHDAY) {
                $this->addEventOrHoliday($eventKey, $event->getName());
                continue;
            }

            /* Birthday event → But no year given */
            if ($year === self::BIRTHDAY_YEAR_NOT_GIVEN) {
                $this->addEventOrHoliday($eventKey, $event->getName());
                continue;
            }

            /* Birthday event → Age must be greater than 0 */
            if ($age <= 0) {
                $this->addEventOrHoliday($eventKey, $event->getName());
                continue;
            }

            /* Birthday event → Add age to name */
            $this->addEventOrHoliday($eventKey, sprintf('%s (%d)', $event->getName(), $age));
        }
    }

    /**
     * Add holidays to this month.
     *
     * @throws Exception
     */
    public function addHolidays(): void
    {
        /* No holiday group class given → no holidays */
        if ($this->holidayGroup === null) {
            return;
        }

        /** @var Holiday $holiday */
        foreach ($this->holidayGroup->getHolidays() as $holiday) {

            /* Get event key */
            $holidayKey = $this->getDayKey(intval($holiday->getDate()->format('j')));

            /* Get year and month */
            $year = intval($holiday->getDate()->format('Y'));
            $month = intval($holiday->getDate()->format('n'));

            /* Check holiday (month) → Skip if not equal */
            if ($this->month !== $month) {
                continue;
            }

            /* Check holiday (year) → Skip if not equal */
            if (!$holiday->getYearly() && $this->year !== $year) {
                continue;
            }

            /* Add event or holiday label */
            $this->addEventOrHoliday($holidayKey, $holiday->getName(), $holiday->getType() === Holiday::FIELD_TYPE_PUBLIC_DATE);
        }
    }

    /**
     * Combine entries from $this->eventsAndHolidaysRaw to $this->eventsAndHolidays
     *
     * @return array<array{name: string}>
     */
    protected function combineEventsAndHolidays(): array
    {
        $eventsAndHolidays = [];

        foreach ($this->eventsAndHolidaysRaw as $key => $eventOrHoliday) {
            $eventsAndHolidays[$key] = [
                'name' => implode(', ', $eventOrHoliday['name']),
            ];
        }

        /* Return events and holidays. */
        return $eventsAndHolidays;
    }

    /**
     * Build all events and holidays according to this month.
     *
     * @return void
     * @throws Exception
     */
    public function createEventsAndHolidays(): void
    {
        /* Reset events and holidays. */
        $this->eventsAndHolidaysRaw = [];

        /* Add events. */
        $this->addEvents();

        /* Add holidays */
        $this->addHolidays();

        /* Combine events and holidays */
        $this->eventsAndHolidays = $this->combineEventsAndHolidays();
    }

    /**
     * Sets the QR Code version.
     *
     * @param int $qrCodeVersion
     * @return void
     */
    public function setQrCodeVersion(int $qrCodeVersion)
    {
        $this->qrCodeVersion = $qrCodeVersion;
    }

    /**
     * Gets all target paths.
     *
     * @param string $pathTargetAbsolute
     * @return string[]
     * @throws Exception
     */
    protected function getAllTargetImages(string $pathTargetAbsolute): array
    {
        $pathTargetAbsolutePattern = preg_replace('~\.([a-z][a-z0-9]+)$~i', '.*.$1', $pathTargetAbsolute);

        if (!is_string($pathTargetAbsolutePattern)) {
            throw new Exception(sprintf('Unable to replace string (%s:%d).', __FILE__, __LINE__));
        }

        $imageFiles = glob($pathTargetAbsolutePattern);

        if ($imageFiles === false) {
            throw new Exception(sprintf('Unable to get files via glob (%s:%d).', __FILE__, __LINE__));
        }

        $imageFiles[] = $pathTargetAbsolute;

        return $imageFiles;
    }

    /**
     * Removes target images.
     *
     * @param string $pathTargetAbsolute
     * @return bool
     * @throws Exception
     */
    protected function removeTargetImages(string $pathTargetAbsolute): bool
    {
        $imageFiles = $this->getAllTargetImages($pathTargetAbsolute);

        foreach ($imageFiles as $imageFile) {

            /* To avoid accidental deletion. */
            $matches = [];
            if (!preg_match('~([a-f0-9]{40}/target/[0-9]+)/(?:([a-f0-9]{10})\.)?([^\.]+)(?:\.([0-9]+))?\.([a-z][a-z0-9]+)$~', $imageFile, $matches)) {
                throw new Exception(sprintf('Unexpected image path given: "%s" (%s:%d).', $imageFile, __FILE__, __LINE__));
            }

            list($path, $fileHash, $fileName, $width, $extension) = $matches;

            if (file_exists($imageFile)) {
                unlink($imageFile);
            }
        }

        return true;
    }

    /**
     * Builds the given source image to a calendar page.
     *
     * @return array<string|int>
     * @throws Exception
     */
    public function build(): array
    {
        /* Save given values */
        $this->pathSourceAbsolute = $this->image->getPathFull(type: Image::PATH_TYPE_SOURCE, test: $this->test, rootPath: $this->pathRoot);
        $pathSourceRelative = $this->image->getPath(type: Image::PATH_TYPE_SOURCE, test: $this->test);
        $this->pathTargetAbsolute = $this->image->getPathFull(
            type: Image::PATH_TYPE_TARGET,
            test: $this->test,
            rootPath: $this->pathRoot,
            calendarImage: $this->useCalendarImagePath ? $this->calendarImage : null
        );
        $pathTargetRelative = $this->image->getPath(
            type: Image::PATH_TYPE_TARGET,
            test: $this->test,
            calendarImage: $this->useCalendarImagePath ? $this->calendarImage : null
        );

        if ($pathSourceRelative === null) {
            throw new Exception(sprintf('Unexpected null value (%s:%d).', __FILE__, __LINE__));
        }

        if ($pathTargetRelative === null) {
            throw new Exception(sprintf('Unexpected null value (%s:%d).', __FILE__, __LINE__));
        }

        if ($this->deleteTargetImages) {
            $this->removeTargetImages($this->pathTargetAbsolute);
        }

        $this->textTitle = $this->calendarImage->getTitle() ?? '';
        $this->textPosition = $this->calendarImage->getPosition() ?? '';
        $this->year = $this->calendarImage->getYear();
        $this->month = $this->calendarImage->getMonth();
        $this->valignImage = $this->calendarImage->getConfigObject()->getValign() ?? self::VALIGN_TOP;
        $this->url = $this->calendarImage->getUrl() ?? 'https://github.com/';

        /* Check target path */
        $this->checkAndCreateDirectory($this->pathTargetAbsolute, true);

        /* Init */
        $this->prepare();

        /* Add main image */
        $this->addImage();

        /* Add calendar area */
        $this->addRectangle();

        /* Add title, position, etc. */
        $this->addImageDescriptionAndPositionOnCalendarPage();

        /* Add calendar */
        if ($this->month === 0) {
            $this->addTitleOnTitlePage();
        } else {
            $this->addYearMonthAndDays();
            $this->addCalendarWeeks();
            $this->addHolidaysAndEvents();
        }

        /* Add qr code */
        $this->addQrCode();

        /* Write image */
        $this->writeImage();

        /* Destroy image */
        $this->destroy();

        return array_merge(
            $this->getImageProperties($this->pathSourceAbsolute, $pathSourceRelative, 'Source'),
            $this->getImageProperties($this->pathTargetAbsolute, $pathTargetRelative),
        );
    }
}
