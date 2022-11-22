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

use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Exception;

/**
 * Class EasyAdminField.
 *
 * @author Björn Hempel <bjoern@hempel.li>
 * @version 1.0 (2022-02-15)
 * @package App\Utils
 */
class EasyAdminField
{
    /**
     * EasyAdminField constructor.
     *
     * @param string $crudName
     */
    public function __construct(protected string $crudName)
    {
    }

    /**
     * Returns the crud name.
     *
     * @return string
     */
    protected function getCrudName(): string
    {
        return $this->crudName;
    }

    /**
     * Returns a choice field with label and help.
     *
     * @param string $fieldName
     * @param string[]|int[] $choices
     * @param bool $expanded
     * @return ChoiceField
     * @throws Exception
     */
    public function getChoiceField(string $fieldName, array $choices = [], bool $expanded = false): ChoiceField
    {
        $choicesRendered = [];
        array_walk($choices, function (&$value, $key) use (&$choicesRendered, $fieldName) {
            $choicesRendered[sprintf('admin.%s.fields.%s.entries.%s', $this->getCrudName(), $fieldName, $key)] = $value;
        });

        return ChoiceField::new($fieldName)
            ->renderExpanded($expanded)
            ->setRequired(true)
            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName))
            ->setChoices($choicesRendered);
    }

    /**
     * Returns an email field with label and help.
     *
     * @param string $fieldName
     * @return EmailField
     * @throws Exception
     */
    public function getEmailField(string $fieldName): EmailField
    {
        return EmailField::new($fieldName)
            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName));
    }

    /**
     * Returns a text field with label and help.
     *
     * @param string $fieldName
     * @return TextField
     * @throws Exception
     */
    public function getTextField(string $fieldName): TextField
    {
        return TextField::new($fieldName)
            ->setLabel(sprintf('admin.%s.fields.%s.label', $this->getCrudName(), $fieldName))
            ->setHelp(sprintf('admin.%s.fields.%s.help', $this->getCrudName(), $fieldName));
    }
}
