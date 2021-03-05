<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationType;

abstract class AbstractExportSyndicationType extends AbstractSyndicationType implements ExportSyndicationTypeInterface
{
    public function getCategory(): string
    {
        return static::CATEGORY_EXPORT;
    }
}
