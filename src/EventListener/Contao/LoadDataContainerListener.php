<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\EventListener\Contao;

use HeimrichHannot\SyndicationTypeBundle\Dca\DcaFieldProvider;

class LoadDataContainerListener
{
    /**
     * @var DcaFieldProvider
     */
    protected $dcaFieldProvider;

    /**
     * LoadDataContainerListener constructor.
     */
    public function __construct(DcaFieldProvider $dcaFieldProvider)
    {
        $this->dcaFieldProvider = $dcaFieldProvider;
    }

    public function __invoke(string $table): void
    {
        switch ($table) {
            case 'tl_reader_config_element':
                $this->prepareReaderConfigElementTable($table);
        }
    }

    protected function prepareReaderConfigElementTable(string $table)
    {
        $dca = &$GLOBALS['TL_DCA'][$table];

        $dca['fields'] = array_merge($dca['fields'] ?: [], $this->dcaFieldProvider->getFields());
        $dca['subpalettes'] = array_merge($dca['subpalettes'] ?: [], $this->dcaFieldProvider->getSubpalettes());
        $dca['palettes']['__selector__'] = array_merge($dca['palettes']['__selector__'] ?: [], $this->dcaFieldProvider->getPalettesSelectors());
    }
}
