<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\Dca;

use HeimrichHannot\SyndicationTypeBundle\EventListener\Dca\FieldOptionsCallbackListener;
use HeimrichHannot\SyndicationTypeBundle\SyndicationType\SyndicationTypeCollection;
use Symfony\Contracts\Translation\TranslatorInterface;

class SyndicationTypeDcaProvider extends AbstractDcaProvider
{
    /**
     * @var SyndicationTypeCollection
     */
    protected $typeCollection;
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(SyndicationTypeCollection $typeCollection, TranslatorInterface $translator)
    {
        $this->typeCollection = $typeCollection;
        $this->translator = $translator;
    }

    public function prepareDca(string $table): void
    {
        $dca = &$GLOBALS['TL_DCA'][$table];

        $selectors = [];
        $subpalettes = [];
        $activationFields = [];

        foreach ($this->typeCollection->getTypes() as $type) {
            if (!empty($type->getPalette())) {
                $subpalettes[$type::getActivationField()] = $type->getPalette();
                $selectors[] = $type::getActivationField();
                $this->addCheckboxField($type::getActivationField(), $activationFields, true);
            } else {
                $this->addCheckboxField($type::getActivationField(), $activationFields);
            }
        }

        $dca['fields'] = array_merge($dca['fields'] ?: [], $activationFields, $this->getFields());
        $dca['subpalettes'] = array_merge($dca['subpalettes'] ?: [], $subpalettes);
        $dca['palettes']['__selector__'] = array_merge($dca['palettes']['__selector__'] ?: [], $selectors);
    }

    /**
     * @return string[]
     */
    public function getSubpalettes(): array
    {
        $subpalettes = $this->getActivationSubpalettes();

        return $subpalettes;
    }

    public function getTypeSubpalettes(): array
    {
        return [
            'syndicationIcsAddTime' => 'syndicationIcsAddTimeField,syndicationIcsStartTimeField,syndicationIcsEndTimeField',
        ];
    }

    public function getActivationSubpalettes(): array
    {
        $subpalettes = [];

        foreach ($this->typeCollection->getTypes() as $type) {
            if (!empty($type->getPalette())) {
                $subpalettes[$type::getActivationField()] = $type->getPalette();
            }
        }

        return $subpalettes;
    }

    /**
     * @return string[]
     */
    public function getPalettesSelectors(): array
    {
        $selectors = [];

        foreach ($this->typeCollection->getTypes() as $type) {
            if (!empty($type->getPalette())) {
                $selectors[] = $type::getActivationField();
            }
        }

        return $selectors;
    }

    /**
     * @return array[]
     */
    public function getFields(): array
    {
        $fields = [];

        foreach ($this->typeCollection->getTypes() as $type) {
            if (!empty($type->getPalette())) {
                $this->addCheckboxField($type::getActivationField(), $fields, true);
            } else {
                $this->addCheckboxField($type::getActivationField(), $fields);
            }
        }

        $fields = array_merge($fields, $this->getConfigurationFields());

        return $fields;
    }

    /**
     * Return all non activation fields.
     */
    public function getConfigurationFields(): array
    {
        $fields = [
            'syndicationEmailAddress' => [
                'label' => $this->getLabel('syndicationEmailAddress'),
                'exclude' => true,
                'search' => true,
                'inputType' => 'text',
                'eval' => ['maxlength' => 64, 'tl_class' => 'w50', 'mandatory' => true],
                'sql' => "varchar(64) NOT NULL default ''",
            ],
            'syndicationEmailSubject' => [
                'label' => $this->getLabel('syndicationEmailSubject'),
                'exclude' => true,
                'search' => true,
                'inputType' => 'text',
                'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
                'sql' => "varchar(255) NOT NULL default ''",
            ],
            'syndicationEmailBody' => [
                'label' => $this->getLabel('syndicationEmailBody'),
                'exclude' => true,
                'search' => true,
                'inputType' => 'textarea',
                'eval' => ['maxlength' => 1000, 'tl_class' => 'long clr', 'rows' => 3],
                'sql' => 'text NULL',
            ],
        ];

        $this->addFieldSelectField('syndicationIcsLocationField', $fields);
        $this->addFieldSelectField('syndicationIcsStartDateField', $fields);
        $this->addFieldSelectField('syndicationIcsEndDateField', $fields);
        $this->addCheckboxField('syndicationIcsAddTime', $fields, true);
        $this->addFieldSelectField('syndicationIcsAddTimeField', $fields);
        $this->addFieldSelectField('syndicationIcsStartTimeField', $fields);
        $this->addFieldSelectField('syndicationIcsEndTimeField', $fields);

        return $fields;
    }

    /**
     * Return the dca palette for syndication types.
     *
     * @param bool $splitByCategories Set to false, if you want all fields are within an single syndication type legend.
     *                                Otherwise multiple legends returned based on categories of syndication (typical export and share).
     */
    public function getPalette(bool $splitByCategories = true): string
    {
        $palette = '';

        $categories = $this->typeCollection->getCategories();

        foreach ($categories as $category) {
            $fields = [];
            $types = $this->typeCollection->getTypesByCategory($category);

            foreach ($types as $type) {
                $fields[] = $type::getActivationField();
            }

            if (empty($fields)) {
                continue;
            }

            if ($splitByCategories) {
                $palette .= '{'.$category.'_legend},';
            }
            $palette .= implode(',', $fields).';';
        }

        if (!$splitByCategories) {
            $palette = '{syndication_type_legend},'.$palette;
        }

        return $palette;
    }

    protected function addCheckboxField(string $fieldName, array &$fields, bool $submitOnChange = false): void
    {
        $fields[$fieldName] = [
            'label' => $this->getLabel($fieldName),
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 clr', 'submitOnChange' => $submitOnChange],
            'sql' => "char(1) NOT NULL default ''",
        ];
    }

    protected function addFieldSelectField(string $fieldName, array &$fields): void
    {
        $fields[$fieldName] = [
            'label' => $this->getLabel($fieldName),
            'inputType' => 'select',
            'options_callback' => [FieldOptionsCallbackListener::class, '__invoke'],
            'exclude' => true,
            'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ];
    }
}
