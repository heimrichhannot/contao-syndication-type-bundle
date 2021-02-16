<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationType;

class ExportSyndicationContext
{
    /**
     * @var array
     */
    protected $data;
    /**
     * @var array
     */
    protected $configuration;

    /**
     * ExportSyndicationContext constructor.
     */
    public function __construct(array $data, array $configuration)
    {
        $this->data = $data;
        $this->configuration = $configuration;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }
}
