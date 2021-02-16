<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationType;

use HeimrichHannot\SyndicationTypeBundle\SyndicationContext\SyndicationContext;

interface ExportSyndicationTypeInterface extends SyndicationTypeInterface
{
    public function shouldExport(SyndicationContext $context): bool;

    public function export(SyndicationContext $context): void;
}
