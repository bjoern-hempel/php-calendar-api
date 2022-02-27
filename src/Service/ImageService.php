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
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityBuiltEvent;
use Exception;
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
     * Returns user id hash from request.
     *
     * @return string|null
     * @throws Exception
     */
    protected function getUserIdHashFromRequest(): ?string
    {
        $request = $this->getCurrentRequest();

        $image = $request->get('Image');

        if (!is_array($image)) {
            return null;
        }

        if (!array_key_exists('user', $image)) {
            return null;
        }

        $user = $this->userLoaderService->getUserRepository()->find($image['user']);

        if (!$user instanceof User) {
            return null;
        }

        return $user->getIdHash();
    }

    /**
     * Creates the source and target directory from user id hash.
     *
     * @throws Exception
     */
    protected function createSourceAndTargetFromIdHash(): void
    {
        $idHash = $this->getUserIdHashFromRequest();

        if ($idHash === null) {
            return;
        }

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
     * Creates a temporary target image.
     *
     * @param Image $image
     * @return string
     * @throws Exception
     */
    protected function createTmpTargetImage(Image $image): string
    {
        $sourcePathFull = $image->getPathSource(true, false, $this->appKernel->getProjectDir());

        /* Get information about image. */
        $imageInfo = getimagesize($sourcePathFull);

        if ($imageInfo === false) {
            throw new Exception(sprintf('Unable to get image information (%s:%d).', __FILE__, __LINE__));
        }

        /* Create image. */
        $imageGp = match ($imageInfo[2]) {
            IMAGETYPE_GIF => imagecreatefromgif($sourcePathFull),
            IMAGETYPE_PNG => imagecreatefrompng($sourcePathFull),
            IMAGETYPE_JPEG => imagecreatefromjpeg($sourcePathFull),
            default => throw new Exception(sprintf('Unsupported image type %d - %s (%s:%d)', $imageInfo[2], $imageInfo['mime'], __FILE__, __LINE__)),
        };

        if ($imageGp === false) {
            throw new Exception(sprintf('Unable to load image (%s:%d).', __FILE__, __LINE__));
        }

        /* Image properties. */
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $color = imagecolorallocate($imageGp, 255, 255, 0);
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
        imagettftext($imageGp, $fontSize, $angle, $x, $y, $color, $font, $text);

        /* Create image. */
        $targetPathFullTmp = $image->getPathTarget(true, false, $this->appKernel->getProjectDir(), true);
        $status = match ($imageInfo[2]) {
            IMAGETYPE_GIF => imagegif($imageGp, $targetPathFullTmp),
            IMAGETYPE_PNG => imagepng($imageGp, $targetPathFullTmp),
            IMAGETYPE_JPEG => imagejpeg($imageGp, $targetPathFullTmp),
            default => throw new Exception(sprintf('Unsupported image type %d - %s (%s:%d)', $imageInfo[2], $imageInfo['mime'], __FILE__, __LINE__)),
        };

        /* Check image */
        if ($status === false || !file_exists($targetPathFullTmp)) {
            throw new Exception(sprintf('Unable to generate picture (%s:%d).', __FILE__, __LINE__));
        }

        /* Return relative path */
        return $image->getPathTarget(false, false, '', true);
    }

    /**
     * Checks target image.
     *
     * @param Image $image
     * @throws Exception
     */
    protected function checkTargetImage(Image $image): void
    {
        $sourcePathFull = $image->getPathSource(true, false, $this->appKernel->getProjectDir());
        $targetPathFull = $image->getPathTarget(true, false, $this->appKernel->getProjectDir());

        if (!file_exists($sourcePathFull)) {
            throw new Exception(sprintf('Unable to load source image "%s" (%s:%d)', $sourcePathFull, __FILE__, __LINE__));
        }

        if (!file_exists($targetPathFull)) {
            $targetPathTmp = $this->createTmpTargetImage($image);

            $image->setPathTarget($targetPathTmp);
        }
    }

    /**
     * Checks image directories and target image.
     *
     * @param AfterEntityBuiltEvent $event
     * @throws Exception
     */
    public function checkImage(AfterEntityBuiltEvent $event): void
    {
        /** Image was posted. */
        if ($this->isImagePosted()) {
            $this->createSourceAndTargetFromIdHash();
            return;
        }

        $entity = $event->getEntity()->getInstance();

        /* Create target image if necessary */
        if ($entity instanceof Image) {
            $this->checkTargetImage($entity);
        }
    }
}
