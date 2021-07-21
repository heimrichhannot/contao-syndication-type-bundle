<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\Dca;

use HeimrichHannot\SyndicationTypeBundle\Event\AddSyndicationTypeFieldsEvent;
use HeimrichHannot\SyndicationTypeBundle\Event\AddSyndicationTypePaletteSelectorsEvent;
use HeimrichHannot\SyndicationTypeBundle\Event\AddSyndicationTypeSubpalettesEvent;
use HeimrichHannot\SyndicationTypeBundle\EventListener\Dca\FieldOptionsCallbackListener;
use HeimrichHannot\SyndicationTypeBundle\SyndicationType\SyndicationTypeCollection;
use HeimrichHannot\TwigSupportBundle\Filesystem\TwigTemplateLocator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

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
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var TwigTemplateLocator
     */
    protected $templateLocator;

    public function __construct(SyndicationTypeCollection $typeCollection, TranslatorInterface $translator, EventDispatcherInterface $eventDispatcher, TwigTemplateLocator $templateLocator)
    {
        $this->typeCollection = $typeCollection;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
        $this->templateLocator = $templateLocator;
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

        $dca['fields'] = array_merge($dca['fields'] ?: [], $activationFields, $this->getFields(true));
        $dca['subpalettes'] = array_merge($dca['subpalettes'] ?: [], $subpalettes, $this->getSubpalettes($subpalettes));
        $dca['palettes']['__selector__'] = array_merge($dca['palettes']['__selector__'] ?: [], $selectors, $this->getPalettesSelectors(true));

        $this->addTranslations($table);
    }

    public function addTranslations(string $table): void
    {
        foreach ($this->typeCollection->getCategories() as $category) {
            $GLOBALS['TL_LANG'][$table][$category.'_legend'] = $this->translator->trans('huh.syndication_type.legends.syndication_categories.'.$category.'_legend');
        }
    }

    /**
     * @return string[]
     */
    public function getSubpalettes(array $subpalettes = []): array
    {
        if (empty($subpalettes)) {
            $subpalettes = $this->getActivationSubpalettes();
        }
        $subpalettes = array_merge($subpalettes, $this->getTypeSubpalettes());

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        /** @noinspection PhpParamsInspection */
        /** @var AddSyndicationTypeSubpalettesEvent $event */
        $event = $this->eventDispatcher->dispatch(AddSyndicationTypeSubpalettesEvent::class, new AddSyndicationTypeSubpalettesEvent($subpalettes));

        return $event->getSubpalettes();
    }

    public function getTypeSubpalettes(): array
    {
        return [
            'synIcsAddTime' => 'synIcsAddTimeField,synIcsStartTimeField,synIcsEndTimeField',
            'synUsePrintTemplate' => 'synPrintTemplate',
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
    public function getPalettesSelectors(bool $skipActivationFieldSelectors = false): array
    {
        $selectors = [];

        if (!$skipActivationFieldSelectors) {
            foreach ($this->typeCollection->getTypes() as $type) {
                if (!empty($type->getPalette())) {
                    $selectors[] = $type::getActivationField();
                }
            }
        }

        $selectors[] = 'synIcsAddTime';
        $selectors[] = 'synUsePrintTemplate';

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        /** @noinspection PhpParamsInspection */
        /** @var AddSyndicationTypePaletteSelectorsEvent $event */
        $event = $this->eventDispatcher->dispatch(AddSyndicationTypePaletteSelectorsEvent::class, new AddSyndicationTypePaletteSelectorsEvent());

        return array_merge($selectors, $event->getSelectors());
    }

    /**
     * @return array[]
     */
    public function getFields(bool $skipActivationFields = false): array
    {
        $fields = [];

        if (!$skipActivationFields) {
            foreach ($this->typeCollection->getTypes() as $type) {
                if (!empty($type->getPalette())) {
                    $this->addCheckboxField($type::getActivationField(), $fields, true);
                } else {
                    $this->addCheckboxField($type::getActivationField(), $fields);
                }
            }
        }

        $fields = array_merge($fields, $this->getConfigurationFields());

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        /** @noinspection PhpParamsInspection */
        /** @var AddSyndicationTypeFieldsEvent $event */
        $event = $this->eventDispatcher->dispatch(AddSyndicationTypeFieldsEvent::class, new AddSyndicationTypeFieldsEvent($fields, $this));

        return array_merge($event->getFields());
    }

    /**
     * Return all non activation fields.
     */
    public function getConfigurationFields(): array
    {
        $fields = [
            'synEmailAddress' => [
                'label' => $this->getLabel('synEmailAddress'),
                'exclude' => true,
                'search' => true,
                'inputType' => 'text',
                'eval' => ['maxlength' => 64, 'tl_class' => 'w50', 'mandatory' => true],
                'sql' => "varchar(64) NOT NULL default ''",
            ],
            'synEmailSubject' => [
                'label' => $this->getLabel('synEmailSubject'),
                'exclude' => true,
                'search' => true,
                'inputType' => 'text',
                'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
                'sql' => "varchar(255) NOT NULL default ''",
            ],
            'synEmailBody' => [
                'label' => $this->getLabel('synEmailBody'),
                'exclude' => true,
                'search' => true,
                'inputType' => 'textarea',
                'eval' => ['maxlength' => 1000, 'tl_class' => 'long clr', 'rows' => 3],
                'sql' => 'text NULL',
            ],
        ];

        $this->addFieldSelectField('synIcsStreetField', $fields);
        $this->addFieldSelectField('synIcsCityField', $fields);
        $this->addFieldSelectField('synIcsPostalField', $fields);
        $this->addFieldSelectField('synIcsLocationField', $fields);
        $this->addFieldSelectField('synIcsStartDateField', $fields);
        $this->addFieldSelectField('synIcsEndDateField', $fields);
        $this->addCheckboxField('synIcsAddTime', $fields, true);
        $this->addFieldSelectField('synIcsAddTimeField', $fields);
        $this->addFieldSelectField('synIcsStartTimeField', $fields);
        $this->addFieldSelectField('synIcsEndTimeField', $fields);

        $this->addCheckboxField('synUsePrintTemplate', $fields, true);
        $this->addTemplateSelectField('synPrintTemplate', $fields, 'syndication_type_print_');

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
            $palette .= implode(',', $fields);

            if ($splitByCategories) {
                $palette .= ';';
            } else {
                $palette .= ',';
            }
        }

        if ($splitByCategories) {
            $palette = '{syndication_type_legend},'.$palette;
        }

        return $palette;
    }

    public function addCheckboxField(string $fieldName, array &$fields, bool $submitOnChange = false): void
    {
        $fields[$fieldName] = [
            'label' => $this->getLabel($fieldName),
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 clr', 'submitOnChange' => $submitOnChange],
            'sql' => "char(1) NOT NULL default ''",
        ];
    }

    public function addFieldSelectField(string $fieldName, array &$fields): void
    {
        $this->addSelectField($fieldName, $fields, [
            'options_callback' => [FieldOptionsCallbackListener::class, '__invoke'],
        ]);
    }

    public function addTemplateSelectField(string $fieldName, array &$fields, string $templateGroup): void
    {
        $templateLocator = $this->templateLocator;
        $this->addSelectField($fieldName, $fields, ['options_callback' => function ($dc) use ($templateGroup, $templateLocator) {
            return $templateLocator->getTemplateGroup($templateGroup);
        }]);
    }

    public function addSelectField(string $fieldName, array &$fields, array $config): void
    {
        $fields[$fieldName] = [
            'label' => $this->getLabel($fieldName),
            'inputType' => 'select',
            'options_callback' => $config['options_callback'],
            'exclude' => true,
            'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ];
    }
}
