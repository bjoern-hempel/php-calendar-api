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

namespace App\Twig;

use App\Controller\Base\BaseController;
use App\Entity\Image;
use App\Service\ImageService;
use App\Service\UrlService;
use App\Utils\FileNameConverter;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Exception;
use GdImage;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * AppExtension class
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0.1 (2022-03-05)
 * @package App\Entity
 */
class AppExtension extends AbstractExtension
{
    protected KernelInterface $kernel;

    protected UrlGeneratorInterface $generator;

    protected ImageService $imageService;

    /**
     * AppExtension constructor.
     *
     * @param KernelInterface $kernel
     * @param UrlGeneratorInterface $generator
     * @param ImageService $imageService
     */
    public function __construct(KernelInterface $kernel, UrlGeneratorInterface $generator, ImageService $imageService)
    {
        $this->kernel = $kernel;

        $this->generator = $generator;

        $this->imageService = $imageService;
    }

    /**
     * Returns the TwigFilter[].
     *
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('preg_replace', [$this, 'pregReplace']),
            new TwigFilter('path_orig', [$this, 'getPathOrig']),
            new TwigFilter('path_preview', [$this, 'getPathPreview']),
            new TwigFilter('add_hash', [$this, 'addHash']),
            new TwigFilter('check_path', [$this, 'checkPath']),
            new TwigFilter('url_absolute', [$this, 'urlAbsolute']),
            new TwigFilter('month_translation', [$this, 'getMonthTranslationKey']),
            new TwigFilter('qr_code', [$this, 'getQrCode'])
        ];
    }

    /**
     * Returns the TwigFunction[].
     *
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('path_encoded', [$this, 'pathEncoded']),
        ];
    }

    /**
     * TwigFilter: Twig filter preg_replace.
     *
     * @param string $subject
     * @param string $pattern
     * @param string $replacement
     * @return string
     * @throws Exception
     */
    public function pregReplace(string $subject, string $pattern, string $replacement): string
    {
        $replaced = preg_replace($pattern, $replacement, $subject);

        if (!is_string($replaced)) {
            throw new Exception(sprintf('Unable to replace string (%s:%d).', __FILE__, __LINE__));
        }

        return $replaced;
    }

    /**
     * TwigFilter: Returns the orig path of given path.
     *
     * @param string $path
     * @param string $outputMode
     * @return string
     * @throws Exception
     */
    public function getPathOrig(string $path, string $outputMode = FileNameConverter::MODE_OUTPUT_RELATIVE): string
    {
        $fileNameConverter = new FileNameConverter($path, $this->kernel->getProjectDir(), false, $outputMode);

        $type = $fileNameConverter->getType($path);

        $pathOrig = $fileNameConverter->getFilename($type);

        return $this->checkPath($pathOrig);
    }

    /**
     * TwigFilter: Returns the preview path of given path.
     *
     * @param string $path
     * @param int $width
     * @param string $outputMode
     * @return string
     * @throws Exception
     */
    public function getPathPreview(string $path, int $width = 400, string $outputMode = FileNameConverter::MODE_OUTPUT_RELATIVE): string
    {
        $fileNameConverter = new FileNameConverter($path, $this->kernel->getProjectDir(), false, $outputMode);

        $type = $fileNameConverter->getType($path);

        $pathFull = $fileNameConverter->getFilename($type, null, false, null, FileNameConverter::MODE_OUTPUT_ABSOLUTE);
        $pathPreview = $fileNameConverter->getFilename($type, $width);
        $pathPreviewFull = $fileNameConverter->getFilename($type, $width, false, null, FileNameConverter::MODE_OUTPUT_ABSOLUTE);

        /* Resize image if image does not exist. */
        if (!file_exists($pathPreviewFull)) {
            $this->imageService->resizeImage($pathFull, $pathPreviewFull, $width);
        }

        return $this->checkPath($pathPreview);
    }

    /**
     * TwigFilter: Adds hash to the end of image path.
     *
     * @param string $path
     * @return string
     * @throws Exception
     */
    public function addHash(string $path): string
    {
        $fullPath = $this->getFullPath($path);

        if (!file_exists($fullPath)) {
            throw new Exception(sprintf('Image "%s" was not found (%s:%d).', $path, __FILE__, __LINE__));
        }

        $md5 = md5_file($fullPath);

        if ($md5 === false) {
            throw new Exception(sprintf('Unable to calculate md5 hash from file "%s" (%s:%d).', $path, __FILE__, __LINE__));
        }

        return sprintf('%s?%s', $path, $md5);
    }

    /**
     * TwigFilter: Checks the given path and add .tmp if the file does not exists.
     *
     * @param string $path
     * @return string
     * @throws Exception
     */
    public function checkPath(string $path): string
    {
        $fullPath = $this->getFullPath($path);

        if (file_exists($fullPath)) {
            return $path;
        }

        throw new Exception(sprintf('Unable to find image "%s" (%s:%d).', $fullPath, __FILE__, __LINE__));
    }

    /**
     * TwigFilter: Add url extensions.
     *
     * @param string $path
     * @return string
     */
    public function urlAbsolute(string $path): string
    {
        if (preg_match('~^http[s]?://~', $path)) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return $path;
        }

        return sprintf('/%s', $path);
    }

    /**
     * TwigFunction: Returns encoded path.
     *
     * @param string $name
     * @param array<string, int|string> $parameters
     * @param bool $relative
     * @return string
     * @throws Exception
     */
    public function pathEncoded(string $name, array $parameters = [], bool $relative = false): string
    {
        $configName = sprintf('CONFIG_%s', strtoupper($name));

        $constantName = sprintf('%s::%s', BaseController::class, $configName);

        $config = constant($constantName);

        if ($config === null) {
            throw new Exception(sprintf('Constant name "%s" is not defined (%s:%d).', $constantName, __FILE__, __LINE__));
        }

        if (!is_array($config)) {
            throw new Exception(sprintf('Array data type expected (%s:%d).', __FILE__, __LINE__));
        }

        $encoded = UrlService::encode($config, $parameters);

        $nameEncoded = sprintf('%s_%s', $name, BaseController::KEY_NAME_ENCODED);

        $parametersEncoded = [
            BaseController::KEY_NAME_ENCODED => $encoded,
        ];

        return $this->generator->generate($nameEncoded, $parametersEncoded, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * Get month translation key.
     *
     * @param int $month
     * @return string
     * @throws Exception
     */
    public function getMonthTranslationKey(int $month): string
    {
        $name = match ($month) {
            0 => 'title',
            1 => 'january',
            2 => 'february',
            3 => 'march',
            4 => 'april',
            5 => 'may',
            6 => 'june',
            7 => 'july',
            8 => 'august',
            9 => 'september',
            10 => 'october',
            11 => 'november',
            12 => 'december',
            default => throw new Exception(sprintf('Unknown month (%s:%d).', __FILE__, __LINE__)),
        };

        return sprintf('admin.calendarImage.fields.month.entries.%s', $name);
    }

    /**
     * Get QrCode value.
     *
     * @param string $url
     * @param int $qrCodeVersion
     * @return string
     * @throws Exception
     */
    public function getQrCode(string $url, int $qrCodeVersion = QRCode::VERSION_AUTO): string
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
            'version' => $qrCodeVersion,
            'addQuietzone' => false,
            'scale' => $scale,
            'markupDark' => '#000',
            'markupLight' => '#f00', // $backgroundColor = [255, 0, 0];
        ];

        /* Get blob from qrCode image */
        $qrCodeBlob = (new QRCode(new QROptions($options)))->render($url);

        /* Create GDImage from blob */
        $imageQrCode = imagecreatefromstring(strval($qrCodeBlob));

        if (!$imageQrCode instanceof GdImage) {
            throw new Exception(sprintf('Unable to create image QRCode (%s:%d).', __FILE__, __LINE__));
        }

        /* Create transparent color */
        $transparentColor = imagecolorexact($imageQrCode, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);

        /* Set background color to transparent */
        imagecolortransparent($imageQrCode, $transparentColor);

        /* Get QrCode */
        ob_start();
        imagepng($imageQrCode);
        $png = ob_get_clean();

        if (!is_string($png)) {
            throw new Exception(sprintf('Unable to get image QRCode (%s:%d).', __FILE__, __LINE__));
        }

        /* Return QrCode for: <img src="{{ 'https://www.link.de'|qr_code }}" style="width: 200px;"> */
        return sprintf("data:image/png;base64,%s", base64_encode($png));
    }

    /**
     * Returns the full path.
     *
     * @param string $path
     * @return string
     */
    protected function getFullPath(string $path): string
    {
        return sprintf('%s/%s', $this->kernel->getProjectDir(), $path);
    }

    /**
     * Add tmp part to given file.
     *
     * @param string $path
     * @return string
     * @throws Exception
     */
    protected function addTmp(string $path): string
    {
        $path = preg_replace('~(\.[0-9]+)?(\.[a-z]+)$~i', '.tmp$1$2', $path);

        if (!is_string($path)) {
            throw new Exception(sprintf('Unable to replace string (%s:%d).', __FILE__, __LINE__));
        }

        return $path;
    }
}
