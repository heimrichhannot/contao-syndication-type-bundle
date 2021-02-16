<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationType;

abstract class AbstractExportSyndicationType extends AbstractSyndicationType
{
    abstract public static function getParameter(): string;

    public function doExport(): void
    {
        if ($context->getData()['id'] == $this->requestStack->getMasterRequest()->get(static::PARAM)) {
        }
    }

    abstract protected function export(): void;
}
