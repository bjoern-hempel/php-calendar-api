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

namespace App\Form\Type;

use EasyCorp\Bundle\EasyAdminBundle\Form\DataTransformer\StringToFileTransformer;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Model\FileUploadState;
use Exception;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Uid\Uuid;
use Traversable;

/**
 * FileUploadEmptyType class
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-19)
 * @package App\Field
 */
class FileUploadEmptyType extends FileUploadType
{
    private string $projectDir;

    /**
     * FileUploadEmptyType constructor.
     *
     * @param string $projectDir
     */
    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    /**
     * Build form.
     *
     * @param FormBuilderInterface $builder
     * @param string[]|bool[]|callable[] $options
     * @throws Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $uploadDir = strval($options['upload_dir']);
        $uploadFilename = $options['upload_filename'];
        $uploadValidate = $options['upload_validate'];
        $allowAdd = boolval($options['allow_add']);
        $multiple = boolval($options['multiple']);

        if (!is_callable($uploadFilename)) {
            throw new Exception(sprintf('The given callable is not callable (%s:%d).', __FILE__, __LINE__));
        }

        if (!is_callable($uploadValidate)) {
            throw new Exception(sprintf('The given callable is not callable (%s:%d).', __FILE__, __LINE__));
        }

        unset($options['upload_dir'], $options['upload_new'], $options['upload_delete'], $options['upload_filename'], $options['upload_validate'], $options['download_path'], $options['allow_add'], $options['allow_delete'], $options['compound']);

        $builder->add('file', FileType::class, $options);
        $builder->add('delete', CheckboxType::class, ['required' => false]);

        $builder->setDataMapper($this);
        $builder->setAttribute('state', new FileUploadState($allowAdd));
        $builder->addModelTransformer(new StringToFileTransformer($uploadDir, $uploadFilename, $uploadValidate, $multiple));
    }

    /**
     * Builds view.
     *
     * @param FormView $view
     * @param FormInterface $form
     * @param string[] $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        /** @var FileUploadState $state */
        $state = $form->getConfig()->getAttribute('state');

        if ([] === $currentFiles = $state->getCurrentFiles()) {
            $data = $form->getNormData();

            if (null !== $data && [] !== $data) {
                $currentFiles = \is_array($data) ? $data : [$data];

                foreach ($currentFiles as $i => $file) {
                    if ($file instanceof UploadedFile) {
                        unset($currentFiles[$i]);
                    }
                }
            }
        }

        $view->vars['currentFiles'] = $currentFiles;
        $view->vars['multiple'] = $options['multiple'];
        $view->vars['allow_add'] = $options['allow_add'];
        $view->vars['allow_delete'] = $options['allow_delete'];
        $view->vars['download_path'] = $options['download_path'];
    }

    /**
     * Configures options.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $uploadNew = static function (UploadedFile $file, string $uploadDir, string $fileName) {
            $file->move($uploadDir, $fileName);
        };

        $uploadDelete = static function (File $file) {
            unlink($file->getPathname());
        };

        $uploadFilename = static function (UploadedFile $file): string {
            return $file->getClientOriginalName();
        };

        $uploadValidate = static function (string $filename): string {
            if (!file_exists($filename)) {
                return $filename;
            }

            $index = 1;
            $pathInfo = pathinfo($filename);

            $extension = '';
            if (array_key_exists('extension', $pathInfo)) {
                $extension = sprintf('.%s', $pathInfo['extension']);
            }

            while (file_exists($filename = sprintf('%s/%s_%d%s', $pathInfo['dirname'], $pathInfo['filename'], $index, $extension))) {
                ++$index;
            }

            return $filename;
        };

        $downloadPath = function (Options $options) {
            return mb_substr($options['upload_dir'], mb_strlen($this->projectDir.'/public/'));
        };

        $allowAdd = static function (Options $options) {
            return $options['multiple'];
        };

        $dataClass = static function (Options $options) {
            return $options['multiple'] ? null : File::class;
        };

        $emptyData = static function (Options $options) {
            return $options['multiple'] ? [] : null;
        };

        $resolver->setDefaults([
            'upload_dir' => $this->projectDir.'/public/uploads/files/',
            'upload_new' => $uploadNew,
            'upload_delete' => $uploadDelete,
            'upload_filename' => $uploadFilename,
            'upload_validate' => $uploadValidate,
            'download_path' => $downloadPath,
            'allow_add' => $allowAdd,
            'allow_delete' => true,
            'data_class' => $dataClass,
            'empty_data' => $emptyData,
            'multiple' => false,
            'required' => false,
            'error_bubbling' => false,
            'allow_file_upload' => true,
        ]);

        $resolver->setAllowedTypes('upload_dir', 'string');
        $resolver->setAllowedTypes('upload_new', 'callable');
        $resolver->setAllowedTypes('upload_delete', 'callable');
        $resolver->setAllowedTypes('upload_filename', ['string', 'callable']);
        $resolver->setAllowedTypes('upload_validate', 'callable');
        $resolver->setAllowedTypes('download_path', ['null', 'string']);
        $resolver->setAllowedTypes('allow_add', 'bool');
        $resolver->setAllowedTypes('allow_delete', 'bool');

        $resolver->setNormalizer('upload_dir', function (Options $options, string $value): string {
            if (\DIRECTORY_SEPARATOR !== mb_substr($value, -1)) {
                $value .= \DIRECTORY_SEPARATOR;
            }

            if (0 !== mb_strpos($value, $this->projectDir)) {
                $value = $this->projectDir.'/'.$value;
            }

            if ('' !== $value && (!is_dir($value) || !is_writable($value))) {
                throw new InvalidArgumentException(sprintf('Invalid upload directory "%s" it does not exist or is not writable.', $value));
            }

            return $value;
        });
        $resolver->setNormalizer('upload_filename', static function (Options $options, $fileNamePatternOrCallable) {
            if (\is_callable($fileNamePatternOrCallable)) {
                return $fileNamePatternOrCallable;
            }

            return static function (UploadedFile $file) use ($fileNamePatternOrCallable) {
                return strtr($fileNamePatternOrCallable, [
                    '[contenthash]' => sha1_file($file->getRealPath() ?: ''),
                    '[day]' => date('d'),
                    '[extension]' => $file->guessClientExtension(),
                    '[month]' => date('m'),
                    '[name]' => pathinfo($file->getClientOriginalName(), \PATHINFO_FILENAME),
                    '[randomhash]' => bin2hex(random_bytes(20)),
                    '[slug]' => (new AsciiSlugger())
                        ->slug(pathinfo($file->getClientOriginalName(), \PATHINFO_FILENAME))
                        ->lower()
                        ->toString(),
                    '[timestamp]' => time(),
                    '[uuid]' => Uuid::v4()->toRfc4122(),
                    '[ulid]' => new Ulid(),
                    '[year]' => date('Y'),
                ]);
            };
        });
        $resolver->setNormalizer('allow_add', static function (Options $options, string $value): string {
            if ($value && !$options['multiple']) {
                throw new InvalidArgumentException('Setting "allow_add" option to "true" when "multiple" option is "false" is not supported.');
            }

            return $value;
        });
    }

    /**
     * Returns the block prefix.
     *
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'ea_fileupload';
    }

    /**
     * Maps data to forms.
     *
     * @param mixed $currentFiles
     * @param FormInterface[]|Traversable $forms
     */
    public function mapDataToForms($currentFiles, $forms): void
    {
        /** @var FormInterface $fileForm */
        $fileForm = current(iterator_to_array($forms));
        $fileForm->setData($currentFiles);
    }

    /**
     * Maps forms to data.
     *
     * @param FormInterface[]|Traversable $forms
     * @param File[] $currentFiles
     * @throws Exception
     */
    public function mapFormsToData($forms, &$currentFiles): void
    {
        /** @var FormInterface[] $children */
        $children = iterator_to_array($forms);

        /** @var File[] $uploadedFiles */
        $uploadedFiles = $children['file']->getData();

        if ($children['file']->getParent() === null) {
            throw new Exception(sprintf('Unable to get parent class (%s:%d).', __FILE__, __LINE__));
        }

        /** @var FileUploadState $state */
        $state = $children['file']->getParent()->getConfig()->getAttribute('state');
        $state->setCurrentFiles($currentFiles);
        $state->setUploadedFiles($uploadedFiles);
        $state->setDelete(boolval($children['delete']->getData()));

        if (!$state->isModified()) {
            return;
        }

        if ($state->isAddAllowed() && !$state->isDelete()) {
            $currentFiles = array_merge($currentFiles, $uploadedFiles);
        } else {
            $currentFiles = $uploadedFiles;
        }
    }
}
