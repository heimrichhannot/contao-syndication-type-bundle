<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\EventListener\Contao;

use HeimrichHannot\SyndicationTypeBundle\Dca\ConfigElementTypeDcaProvider;
use HeimrichHannot\SyndicationTypeBundle\Dca\SyndicationTypeDcaProvider;

class LoadDataContainerListener
{
    /**
     * @var SyndicationTypeDcaProvider
     */
    protected $syndicationTypeDcaProvider;
    /**
     * @var ConfigElementTypeDcaProvider
     */
    protected $configElementTypeDcaProvider;

    /**
     * LoadDataContainerListener constructor.
     */
    public function __construct(SyndicationTypeDcaProvider $syndicationTypeDcaProvider, ConfigElementTypeDcaProvider $configElementTypeDcaProvider)
    {
        $this->syndicationTypeDcaProvider = $syndicationTypeDcaProvider;
        $this->configElementTypeDcaProvider = $configElementTypeDcaProvider;
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
        $this->configElementTypeDcaProvider->prepareDca($table);
        $this->syndicationTypeDcaProvider->prepareDca($table);
    }
}
