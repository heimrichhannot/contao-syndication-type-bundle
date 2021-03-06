<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\EventListener\Dca;

use Contao\DataContainer;
use HeimrichHannot\ReaderBundle\Util\ReaderConfigUtil;

class FieldOptionsCallbackListener
{
    /**
     * @var ReaderConfigUtil
     */
    protected $readerConfigUtil;

    /**
     * FieldOptionsCallbackListener constructor.
     */
    public function __construct()
    {
    }

    public function __invoke(?DataContainer $dc)
    {
        if (!$dc || $dc->id < 1) {
            return [];
        }

        switch ($dc->table) {
            case 'tl_reader_config_element':
                if ($this->readerConfigUtil) {
                    return $this->readerConfigUtil->getFields($dc->activeRecord->pid);
                }

                break;
        }

        return [];
    }

    public function setReaderConfigUtil(ReaderConfigUtil $readerConfigUtil): void
    {
        $this->readerConfigUtil = $readerConfigUtil;
    }
}
