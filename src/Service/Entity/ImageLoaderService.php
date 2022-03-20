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

namespace App\Service\Entity;

use App\Entity\EntityInterface;
use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Service\Entity\Base\BaseLoaderService;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ImageLoaderService
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-15)
 * @package App\Command
 */
class ImageLoaderService extends BaseLoaderService
{
    protected KernelInterface $appKernel;

    protected EntityManagerInterface $manager;

    protected SecurityService $securityService;

    /**
     * Calendar constructor
     *
     * @param KernelInterface $appKernel
     * @param EntityManagerInterface $manager
     * @param SecurityService $securityService
     */
    public function __construct(KernelInterface $appKernel, EntityManagerInterface $manager, SecurityService $securityService)
    {
        $this->appKernel = $appKernel;

        $this->manager = $manager;

        $this->securityService = $securityService;
    }

    /**
     * Returns the ImageRepository.
     *
     * @return ImageRepository
     * @throws Exception
     */
    protected function getImageRepository(): ImageRepository
    {
        $repository = $this->manager->getRepository(Image::class);

        if (!$repository instanceof ImageRepository) {
            throw new Exception('Error while getting ImageRepository.');
        }

        return $repository;
    }

    /**
     * Loads all images by permissions.
     *
     * @return Image[]
     * @throws Exception
     */
    public function loadImages(): array
    {
        if ($this->securityService->isGrantedByAnAdmin()) {
            return $this->getImageRepository()->findAll();
        }

        return $this->getImageRepository()->findBy(['user' => $this->securityService->getUser()]);
    }
}
