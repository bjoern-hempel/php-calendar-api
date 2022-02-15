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
use App\Repository\ImageRepository;
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
class ImageLoaderService
{
    protected KernelInterface $appKernel;

    protected EntityManagerInterface $manager;

    /**
     * Calendar constructor
     *
     * @param KernelInterface $appKernel
     * @param EntityManagerInterface $manager
     */
    public function __construct(KernelInterface $appKernel, EntityManagerInterface $manager)
    {
        $this->appKernel = $appKernel;

        $this->manager = $manager;
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
}
