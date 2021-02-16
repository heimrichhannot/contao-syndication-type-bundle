<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\ExportSyndication;

use HeimrichHannot\SyndicationTypeBundle\SyndicationContext\SyndicationContext;
use HeimrichHannot\SyndicationTypeBundle\SyndicationType\ExportSyndicationTypeInterface;
use HeimrichHannot\SyndicationTypeBundle\SyndicationType\SyndicationTypeCollection;

class ExportSyndicationHandler
{
    /**
     * @var SyndicationTypeCollection
     */
    protected $syndicationTypeCollection;

    /**
     * ExportSyndicationHandler constructor.
     */
    public function __construct(SyndicationTypeCollection $syndicationTypeCollection)
    {
        $this->syndicationTypeCollection = $syndicationTypeCollection;
    }

    public function export(string $type, SyndicationContext $context): void
    {
        if (!$this->syndicationTypeCollection->isExportType($type)) {
            return;
        }
        /** @var ExportSyndicationTypeInterface $type */
        $type = $this->syndicationTypeCollection->getType($type);

        if ($type->shouldExport($context)) {
            $type->export($context);
        }
    }

    public function exportByContext(SyndicationContext $context): void
    {
        /** @var ExportSyndicationTypeInterface $type */
        foreach ($this->syndicationTypeCollection->getTypes() as $type) {
            if ($type->isEnabledByContext($context)) {
                $this->export($type::getType(), $context);
            }
        }
    }
}
