<?php declare(strict_types=1);

/*
 * MIT License
 *
 * Copyright (c) 2021 Björn Hempel <bjoern@hempel.li>
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

namespace App\Service;

use App\Entity\Calendar;
use App\Entity\CalendarImage;
use App\Entity\HolidayGroup;
use App\Entity\Image;
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

    protected int $widthQrCode = 200;

    protected int $heightQrCode = 200;

    protected string $font = 'OpenSansCondensed-Light.ttf';

    protected string $pathRoot;

    protected string $pathUpload;

    protected string $pathSource;

    protected string $pathTarget;

    protected string $pathQrCode;

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

    protected string $textTitle;

    protected string $textPosition;

    protected int $dayDistance = 40;

    protected int $widthSource;

    protected int $heightSource;

    protected int $widthSourceQrCode;

    protected int $heightSourceQrCode;

    protected int $yRect;

    protected int $x;

    protected int $y;

    protected int $xRemember;

    protected int $yRemember;

    /** @var int[] $colors */
    protected array $colors;

    protected float $zoom = 1.0;

    protected int $valignImage;

    protected GdImage $imageTarget;

    protected GdImage $imageSource;

    protected GdImage $imageQrCode;

    protected CalendarImage $calendarImage;

    protected Calendar $calendar;

    protected Image $image;

    protected ?HolidayGroup $holidayGroup = null;

    /** @var array<string> $holidays */
    protected array $holidays = [];

    const ALIGN_LEFT = 1;

    const ALIGN_CENTER = 2;

    const ALIGN_RIGHT = 3;

    const VALIGN_TOP = 1;

    const VALIGN_BOTTOM = 2;

    const ZOOM_HEIGHT_100 = 4000;

    const DAY_SUNDAY = 0;

    const DAY_MONDAY = 1;

    const IMAGE_PNG = 'png';

    const IMAGE_JPG = 'jpg';

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
     * @throws Exception
     */
    public function init(CalendarImage $calendarImage): void
    {
        /* calendar instances */
        $this->calendarImage = $calendarImage;
        $this->calendar = $this->calendarImage->getCalendar();
        $this->image = $this->calendarImage->getImage();

        /* sizes */
        $this->aspectRatio = $this->calendarImage->getCalendar()->getConfigObject()->getAspectRatio() ?? 3 / 2;
        $this->height = $this->calendarImage->getImage()->getHeight() ?? 4000;
        $this->width = intval(floor($this->height * $this->aspectRatio));

        /* Root path */
        $this->pathRoot = $this->appKernel->getProjectDir();

        /* Font path */
        $this->pathUpload = sprintf('%s/data/', $this->pathRoot);
        $this->pathFont = sprintf('%s/font/%s', $this->pathUpload, $this->font);

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
     * Sets the holiday group.
     *
     * @param HolidayGroup $holidayGroup
     */
    public function setHolidayGroup(HolidayGroup $holidayGroup): void
    {
        $this->holidayGroup = $holidayGroup;

        $this->holidays = $this->holidayGroup->getHolidayArray();
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
        $this->imageQrCode = $this->createImageFromImage($this->pathQrCode, self::IMAGE_PNG);
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
     * Create the colors and save the integer values to colors.
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

        $propertiesQrCode = getimagesize($this->pathQrCode);

        if ($propertiesQrCode === false) {
            throw new Exception(sprintf('Unable to get image size (%s:%d)', __FILE__, __LINE__));
        }

        $this->widthSourceQrCode = $propertiesQrCode[0];
        $this->heightSourceQrCode = $propertiesQrCode[1];

        $this->yRect = intval(floor($this->height * 19.5 / 24));
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
            self::VALIGN_BOTTOM => $this->yRect - $this->height,
            default => 0,
        };

        $x = 0; // round(($this->width - $this->widthSource) / 2);

        imagecopyresampled($this->imageTarget, $this->imageSource, $x, $y, 0, 0, $this->width, $this->height, $this->widthSource, $this->heightSource);
    }

    /**
     * Add bottom text and calendar rectangle.
     */
    protected function addRectangle(): void
    {
        /* Add calendar area (rectangle) */
        imagefilledrectangle($this->imageTarget, 0, $this->yRect, $this->width, $this->height, $this->colors['blackTransparency']);
    }

    /**
     * Add the title and position.
     *
     * @throws Exception
     */
    protected function addTitleAndPositionOnCalendarPage(): void
    {
        /* Start y */
        $x = $this->padding;
        $y = $this->yRect + $this->padding;

        /* Add title */
        $fontSizeTitle = $this->fontSizeTitle;
        $angleAll = 0;
        $dimensionTitle = $this->getDimension($this->textTitle, $this->fontSizeTitle, $angleAll);
        imagettftext($this->imageTarget, $this->fontSizeTitle, $angleAll, $x, $y + $fontSizeTitle, $this->colors['white'], $this->pathFont, $this->textTitle);

        /* Add position */
        $anglePosition = 90;
        $dimensionPosition = $this->getDimension($this->textPosition, $this->fontSizePosition, $anglePosition);
        $xPosition = $this->padding + $dimensionPosition['width'] + $this->fontSizePosition;
        $yPosition = $this->yRect - $this->padding;
        imagettftext($this->imageTarget, $this->fontSizePosition, $anglePosition, $xPosition, $yPosition, $this->colors['white'], $this->pathFont, $this->textPosition);
    }

    /**
     * Adds the title page.
     *
     * @param int $y
     * @throws Exception
     */
    protected function addTitleOnTitlePage(int $y = 0): void
    {
        /* Set x and y */
        $xCenterCalendar = intval(round($this->width / 2));
        $this->initXY($xCenterCalendar, $this->yRect + $this->padding + $y);

        $paddingTopYear = $this->getSize(0);
        $dimensionYear = $this->addText(sprintf('%s', $this->calendar->getTitle()), $this->fontSizeTitlePage, $this->colors['white'], $paddingTopYear, self::ALIGN_CENTER, self::VALIGN_TOP);
        $this->addY($dimensionYear['height'] + $paddingTopYear);

        $paddingTopSubtext = $this->getSize(40);
        $dimensionYear = $this->addText(sprintf('%s', $this->calendar->getSubtitle()), $this->fontSizeTitlePageSubtext, $this->colors['white'], $paddingTopSubtext, self::ALIGN_CENTER, self::VALIGN_TOP);
        $this->addY($dimensionYear['height'] + $paddingTopSubtext);
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
        return $this->getDayOfWeek($year, $month, $day) === self::DAY_SUNDAY ?
            $this->colors['red'] :
            $this->colors['white'];
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
     * Returns the number of week if monday. Otherwise null.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return int|null
     * @throws Exception
     */
    protected function getWeekNumberIfMonday(int $year, int $month, int $day): ?int
    {
        $dayOfWeek = $this->getDayOfWeek($year, $month, $day);

        if ($dayOfWeek !== self::DAY_MONDAY) {
            return null;
        }

        return $this->getWeekNumber($year, $month, $day);
    }

    /**
     * Remember last x and y position.
     */
    protected function rememberPosition(): void
    {
        $this->xRemember = $this->x;
        $this->yRemember = $this->y;
    }

    /**
     * Resets the x and y position from remembered position.
     *
     * @throws Exception
     */
    protected function resetPosition(): void
    {
        if (!isset($this->xRemember) || !isset($this->yRemember)) {
            throw new Exception(sprintf('Call rememberPosition before resetPosition (%s:%d)', __FILE__, __LINE__));
        }

        $this->x = $this->xRemember;
        $this->y = $this->yRemember;
    }

    /**
     * Adds week number to day (addDay).
     *
     * @param int|null $weekNumber
     * @param array{width: int, height: int} $dimensionDay
     * @param int $align
     * @throws Exception
     */
    protected function addWeekNumber(?int $weekNumber, array $dimensionDay, int $align): void
    {
        /* Add calendar week, if day is monday */
        if ($weekNumber !== null) {
            /* Remember current x and y position. */
            $this->rememberPosition();

            /* Set calendar week position (ALIGN_LEFT -> right side) */
            $this->x -= $align === self::ALIGN_LEFT ? 0 : $dimensionDay['width'];
            $this->y += intval(round(1.0 * $this->fontSizeDay));

            /* Add calendar week */
            $this->addText(sprintf('%02d', $weekNumber), intval(ceil($this->fontSizeDay * 0.5)), $this->colors['white']);

            /* Set remembered position */
            $this->resetPosition();
        }
    }

    /**
     * Add event to day.
     *
     * @param int $day
     * @param array{width: int, height: int} $dimensionDay
     * @param int $align
     * @throws Exception
     */
    protected function addEvent(int $day, array $dimensionDay, int $align): void
    {
        $date = sprintf('%04d-%02d-%02d', $this->year, $this->month, $day);

        if (!array_key_exists($date, $this->holidays)) {
            return;
        }

        $event = $this->holidays[$date];

        /* Remember current x and y position. */
        $this->rememberPosition();

        /* Angle and font size */
        $angleEvent = 90;
        $fontSizeEvent = intval(ceil($this->fontSizeDay * 0.6));

        /* Dimension Event */
        $dimensionEvent = $this->getDimension($event, $fontSizeEvent, $angleEvent);
        $xEvent = $dimensionEvent['width'] + $fontSizeEvent;

        /* Set event position */
        $this->x -= $align === self::ALIGN_LEFT ? 0 : $dimensionDay['width'];
        $this->x += $xEvent;
        $this->y -= intval(round(1.5 * $this->fontSizeDay));

        /* Add Event */
        $this->addText(text: $event, fontSize: $fontSizeEvent, color: $this->colors['white'], align: self::ALIGN_LEFT, angle: $angleEvent);

        /* Set remembered position */
        $this->resetPosition();
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
        /* Add x for next day */
        $this->addX($align === self::ALIGN_LEFT ? $this->dayDistance : -$this->dayDistance);

        /* Add day */
        $color = $this->getDayColor($this->year, $this->month, $day);
        $dimensionDay = $this->addText(sprintf('%02d', $day), $this->fontSizeDay, $color, align: $align);
        $weekNumber = $this->getWeekNumberIfMonday($this->year, $this->month, $day);

        /* Add week number */
        $this->addWeekNumber($weekNumber, $dimensionDay, $align);

        /* Add Event */
        $this->addEvent($day, $dimensionDay, $align);

        /* Add x for next day */
        $this->addX($align === self::ALIGN_LEFT ? $dimensionDay['width'] : -$dimensionDay['width']);
    }

    /**
     * Adds the calendar.
     *
     * @throws Exception
     */
    protected function addCalendar(): void
    {
        /* Change calendar box size from 5:6 (20:24) to 19.5:24 -> 4:24 to 4.5:24 */
        $y = intval(round($this->height * 4.5 / 24 - $this->height * 4 / 24));

        /* This is the title page */
        if ($this->month === 0) {
            $this->addTitleOnTitlePage($y);
            return;
        }

        /* Set x and y */
        $xCenterCalendar = intval(round($this->width / 2) + round($this->width / 8));
        $this->initXY($xCenterCalendar, $this->yRect + $this->padding + $y);

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
     * Adds QR Code.
     *
     * ❯ qrencode -o 11.orig.png --margin=0 https://calendar2022.hempel.li/11/r
     *
     * ❯ convert 00.orig.png -fuzz 0% -fill transparent -opaque white -fill white -opaque black 00.png
     * ❯ convert 01.orig.png -fuzz 0% -fill transparent -opaque white -fill white -opaque black 01.png
     * ❯ convert 02.orig.png -fuzz 0% -fill transparent -opaque white -fill white -opaque black 02.png
     * ❯ convert 03.orig.png -fuzz 0% -fill transparent -opaque white -fill white -opaque black 03.png
     * ❯ convert 04.orig.png -fuzz 0% -fill transparent -opaque white -fill white -opaque black 04.png
     * ❯ convert 05.orig.png -fuzz 0% -fill transparent -opaque white -fill white -opaque black 05.png
     * ❯ convert 06.orig.png -fuzz 0% -fill transparent -opaque white -fill white -opaque black 06.png
     * ❯ convert 07.orig.png -fuzz 0% -fill transparent -opaque white -fill white -opaque black 07.png
     * ❯ convert 08.orig.png -fuzz 0% -fill transparent -opaque white -fill white -opaque black 08.png
     * ❯ convert 09.orig.png -fuzz 0% -fill transparent -opaque white -fill white -opaque black 09.png
     * ❯ convert 10.orig.png -fuzz 0% -fill transparent -opaque white -fill white -opaque black 10.png
     * ❯ convert 11.orig.png -fuzz 0% -fill transparent -opaque white -fill white -opaque black 11.png
     * ❯ convert 12.orig.png -fuzz 0% -fill transparent -opaque white -fill white -opaque black 12.png
     */
    protected function addQrCode(): void
    {
        imagecopyresized($this->imageTarget, $this->imageQrCode, $this->padding, $this->height - $this->padding - $this->heightQrCode, 0, 0, $this->widthQrCode, $this->heightQrCode, $this->widthSourceQrCode, $this->heightSourceQrCode);
    }

    /**
     * Builds the given source image to a calendar page.
     *
     * @throws Exception
     */
    public function build(): void
    {
        /* Save given values */
        $this->pathSource = sprintf('%s/%s', $this->pathUpload, $this->image->getSourcePath());
        $this->pathTarget = sprintf('%s/%s', $this->pathUpload, $this->image->getTargetPath());
        $this->pathQrCode = str_replace('.jpg', '.png', $this->pathSource);
        $this->textTitle = $this->calendarImage->getTitle() ?? '';
        $this->textPosition = $this->calendarImage->getPosition() ?? '';
        $this->year = $this->calendarImage->getYear();
        $this->month = $this->calendarImage->getMonth();
        $this->valignImage = $this->calendarImage->getConfigObject()->getValign() ?? self::VALIGN_TOP;

        /* Init */
        $this->prepare();

        /* Build calender image */
        $this->addImage();
        $this->addRectangle();
        $this->addTitleAndPositionOnCalendarPage();
        $this->addCalendar();
        $this->addQrCode();

        /* Write image */
        $this->writeImage();

        /* Destroy image */
        $this->destroy();
    }
}
