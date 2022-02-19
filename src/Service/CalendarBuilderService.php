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

    protected string $pathSource;

    protected string $pathTarget;

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

    protected bool $test;

    /** @var array<array{name: string[]}> $eventsAndHolidaysRaw */
    protected array $eventsAndHolidaysRaw = [];

    /** @var array<array{name: string}> $eventsAndHolidays */
    protected array $eventsAndHolidays = [];

    /** @var array<bool> $holidays */
    protected array $holidays = [];

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
     * Init function.
     *
     * @param CalendarImage $calendarImage
     * @param HolidayGroup|null $holidayGroup
     * @param bool $test
     * @throws Exception
     */
    public function init(CalendarImage $calendarImage, HolidayGroup $holidayGroup = null, bool $test = false): void
    {
        /* Clear positions */
        $this->positionDays = [];

        /* Test mode */
        $this->test = $test;

        /* calendar instances */
        $this->calendarImage = $calendarImage;
        $this->holidayGroup = $holidayGroup;
        $this->calendar = $this->calendarImage->getCalendar();
        $this->image = $this->calendarImage->getImage();

        /* sizes */
        $this->aspectRatio = $this->calendarImage->getCalendar()->getConfigObject()->getAspectRatio() ?? 3 / 2;
        $this->height = $this->calendarImage->getCalendar()->getConfigObject()->getHeight() ?? 4000;
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
        $boundingBox = imageftbbox($fontSize, $angle, $this->pathFont, $text);

        if ($boundingBox === false) {
            throw new Exception(sprintf('Unable to get bounding box (%s:%d', __FILE__, __LINE__));
        }

        list($left, $bottom, $right, , , $top) = $boundingBox;

        return [
            'width' => $right - $left,
            'height' => $bottom - $top,
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
        imagejpeg($this->imageTarget, $this->pathTarget, $this->qualityTarget);
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
        $this->imageSource = $this->createImageFromImage($this->pathSource);
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
        $propertiesSource = getimagesize($this->pathSource);

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
        if (array_key_exists($this->getDayKey($day), $this->holidays)) {
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
            mkdir($pathToCheck);
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
            'version' => 5,
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
    }

    /**
     * Returns image properties from given image.
     *
     * @param string $path
     * @param string $keyPostfix
     * @return array<string|int>
     * @throws Exception
     */
    protected function getImageProperties(string $path, string $keyPostfix = 'Target'): array
    {
        /* Check created image */
        if (!file_exists($path)) {
            throw new Exception(sprintf('Missing file "%s" (%s:%d).', $path, __FILE__, __LINE__));
        }

        /* Get image properties */
        $image = getimagesize($path);

        /* Check image properties */
        if ($image === false) {
            throw new Exception(sprintf('Unable to get file information from "%s" (%s:%d).', $path, __FILE__, __LINE__));
        }

        /* Get file size */
        $sizeByte = filesize($path);

        /* Check image properties */
        if ($sizeByte === false) {
            throw new Exception(sprintf('Unable to get file size from "%s" (%s:%d).', $path, __FILE__, __LINE__));
        }

        /* Return the image properties */
        return [
            sprintf('path%s', $keyPostfix) => $path,
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
     * @param int $month
     * @return string
     */
    protected function getYearMonthKey(int $month): string
    {
        return sprintf('%04d-%02d', $this->year, $month);
    }

    /**
     * Adds given event.
     *
     * @param string $key
     * @param string $name
     */
    protected function addEventOrHoliday(string $key, string $name): void
    {
        /* Add new key */
        if (!array_key_exists($key, $this->eventsAndHolidaysRaw)) {
            $this->eventsAndHolidaysRaw[$key] = [
                'name' => [],
            ];
        }

        /* Add name */
        $this->eventsAndHolidaysRaw[$key]['name'][] = $name;
    }

    /**
     * Adds all events according to this month.
     *
     * @throws Exception
     */
    protected function addEvents(): void
    {
        /* Build current year and month */
        $yearMonthPage = $this->getYearMonthKey($this->month);

        /** @var Event $event */
        foreach ($this->calendarImage->getUser()->getEvents() as $event) {

            /* Get event key */
            $eventKey = $this->getDayKey(intval($event->getDate()->format('j')));

            /* Get year from event */
            $year = intval($event->getDate()->format('Y'));

            /* Calculate age from event */
            $age = $this->calendarImage->getYear() - $year;

            /* This event does not fit the month → Skip */
            if ($yearMonthPage !== $this->getYearMonthKey(intval($event->getDate()->format('n')))) {
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

        /* Build current year and month */
        $yearMonthPage = $this->getYearMonthKey($this->month);

        /** @var Holiday $holiday */
        foreach ($this->holidayGroup->getHolidays() as $holiday) {

            /* Get event key */
            $holidayKey = $this->getDayKey(intval($holiday->getDate()->format('j')));

            /* This event does not fit the month → Skip */
            if ($yearMonthPage !== $this->getYearMonthKey(intval($holiday->getDate()->format('n')))) {
                continue;
            }

            /* Add event or holiday label */
            $this->addEventOrHoliday($holidayKey, $holiday->getName());

            /* Add holiday */
            $this->holidays[$holidayKey] = true;
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
     * Builds the given source image to a calendar page.
     *
     * @return array<string|int>
     * @throws Exception
     */
    public function build(): array
    {
        /* Save given values */
        $this->pathSource = $this->image->getPathFull(Image::PATH_TYPE_SOURCE, $this->test, $this->pathRoot);
        $this->pathTarget = $this->image->getPathFull(Image::PATH_TYPE_TARGET, $this->test, $this->pathRoot);

        $this->textTitle = $this->calendarImage->getTitle() ?? '';
        $this->textPosition = $this->calendarImage->getPosition() ?? '';
        $this->year = $this->calendarImage->getYear();
        $this->month = $this->calendarImage->getMonth();
        $this->valignImage = $this->calendarImage->getConfigObject()->getValign() ?? self::VALIGN_TOP;
        $this->url = $this->calendarImage->getUrl() ?? 'https://github.com/';

        /* Check target path */
        $this->checkAndCreateDirectory($this->pathTarget, true);

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
            $this->getImageProperties($this->pathSource, 'Source'),
            $this->getImageProperties($this->pathTarget),
        );
    }
}
