<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\Dca;

use Contao\DataContainer;
use Contao\DC_Table;
use HeimrichHannot\ReaderBundle\Util\ReaderConfigUtil;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfigElementTypeDcaProvider extends AbstractDcaProvider
{
    /** @var ReaderConfigUtil */
    protected $readerConfigUtil;
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * ConfigElementTypeDcaProvider constructor.
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function setReaderConfigUtil(ReaderConfigUtil $readerConfigUtil): void
    {
        $this->readerConfigUtil = $readerConfigUtil;
    }

    public function getFields(): array
    {
        $fields = [];

        $fields['syndicationTitleField'] = [
            'label' => $this->getLabel('syndicationTitleField'),
            'inputType' => 'select',
            'options_callback' => [self::class, 'onFieldOptionsCallback'],
            'exclude' => true,
            'eval' => ['includeBlankOption' => true, 'mandatory' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ];
        $fields['syndicationContentField'] = [
            'label' => $this->getLabel('syndicationContentField'),
            'inputType' => 'select',
            'options_callback' => [self::class, 'onFieldOptionsCallback'],
            'exclude' => true,
            'eval' => ['includeBlankOption' => true, 'mandatory' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ];

        return $fields;
    }

    public function getSubpalettes(): array
    {
        return [];
    }

    public function getPalettesSelectors(): array
    {
        return [];
    }

    /**
     * @param DataContainer|DC_Table $dataContainer
     */
    public function onFieldOptionsCallback(DataContainer $dataContainer): array
    {
        if ($dataContainer->id > 0) {
            switch ($dataContainer->table) {
                case 'tl_reader_config_element':
                    if ($this->readerConfigUtil) {
                        return $this->readerConfigUtil->getFields($dataContainer->activeRecord->pid);
                    }

                    break;
            }
        }

        return [];
    }
}
