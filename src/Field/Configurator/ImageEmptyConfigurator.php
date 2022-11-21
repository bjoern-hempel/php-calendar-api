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

namespace App\Field\Configurator;

use App\Field\ImageEmptyField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use Exception;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;

use function Symfony\Component\String\u;

use const DIRECTORY_SEPARATOR;

/**
 * ImageEmptyConfigurator class.
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-19)
 * @package App\Field
 */
final class ImageEmptyConfigurator implements FieldConfiguratorInterface
{
    private string $projectDir;

    /**
     * ImageEmptyConfigurator constructor
     *
     * @param string $projectDir
     */
    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    /**
     * Check if given field is supported.
     *
     * @param FieldDto $field
     * @param EntityDto $entityDto
     * @return bool
     */
    #[Pure]
    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return ImageEmptyField::class === $field->getFieldFqcn();
    }

    /**
     * Configures this configuration.
     *
     * @param FieldDto $field
     * @param EntityDto $entityDto
     * @param AdminContext $context
     * @throws Exception
     */
    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $configuredBasePath = $field->getCustomOption(ImageEmptyField::OPTION_BASE_PATH);

        if (!is_string($configuredBasePath) && !is_null($configuredBasePath)) {
            throw new Exception('Unexpected case (string or null expected).');
        }

        $formattedValue = \is_array($field->getValue())
            ? $this->getImagesPaths($field->getValue(), $configuredBasePath)
            : $this->getImagePath(strval($field->getValue()), $configuredBasePath);
        $field->setFormattedValue($formattedValue);

        $field->setFormTypeOption('upload_filename', $field->getCustomOption(ImageEmptyField::OPTION_UPLOADED_FILE_NAME_PATTERN));

        // this check is needed to avoid displaying broken images when image properties are optional
        if (empty($formattedValue) || $formattedValue === rtrim($configuredBasePath ?? '', '/')) {
            $field->setTemplateName('label/empty');
        }

        if ($context->getCrud() === null) {
            throw new Exception(sprintf('No crud was found (%s:%d).', __FILE__, __LINE__));
        }

        if (!in_array($context->getCrud()->getCurrentPage(), [Crud::PAGE_EDIT, Crud::PAGE_NEW], true)) {
            return;
        }

        $relativeUploadDir = $field->getCustomOption(ImageEmptyField::OPTION_UPLOAD_DIR);

        if (!is_string($relativeUploadDir)) {
            throw new InvalidArgumentException(sprintf('The "%s" image field must define the directory where the images are uploaded using the setUploadDir() method.', $field->getProperty()));
        }

        $relativeUploadDir = u($relativeUploadDir)->trimStart(DIRECTORY_SEPARATOR)->ensureEnd(DIRECTORY_SEPARATOR)->toString();
        $absoluteUploadDir = u($relativeUploadDir)->ensureStart($this->projectDir. DIRECTORY_SEPARATOR)->toString();

        $field->setFormTypeOption('upload_dir', $absoluteUploadDir);
    }

    /**
     * Returns image paths.
     *
     * @param string[]|null[]|null $images
     * @param string|null $basePath
     * @return string[]
     */
    private function getImagesPaths(?array $images, ?string $basePath): array
    {
        $imagesPaths = [];

        if ($images === null) {
            return $imagesPaths;
        }

        foreach ($images as $image) {
            if ($image === null) {
                continue;
            }

            $imagesPaths[] = $this->getImagePath($image, $basePath);
        }

        return $imagesPaths;
    }

    /**
     * Returns image path.
     *
     * @param string $imagePath
     * @param string|null $basePath
     * @return string
     */
    private function getImagePath(string $imagePath, ?string $basePath): string
    {
        // add the base path only to images that are not absolute URLs (http or https) or protocol-relative URLs (//)
        if (0 !== preg_match('/^(http[s]?|\/\/)/i', $imagePath)) {
            return $imagePath;
        }

        // remove project path from filepath
        $imagePath = str_replace($this->projectDir.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR, '', $imagePath);

        return isset($basePath)
            ? rtrim($basePath, '/').'/'.ltrim($imagePath, '/')
            : '/'.ltrim($imagePath, '/');
    }
}
