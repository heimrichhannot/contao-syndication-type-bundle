<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationType;

use HeimrichHannot\SyndicationTypeBundle\SyndicationContext\SyndicationContext;

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
        if ($type = $this->willRunExport($type, $context)) {
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

    public function willRunExport(string $type, SyndicationContext $context): ?ExportSyndicationTypeInterface
    {
        if (!$this->syndicationTypeCollection->isExportType($type)) {
            return null;
        }

        /** @var ExportSyndicationTypeInterface $type */
        $type = $this->syndicationTypeCollection->getType($type);

        if ($type->shouldExport($context)) {
            return $type;
        }

        return null;
    }

    public function willRunExportByContext(SyndicationContext $context): bool
    {
        /** @var ExportSyndicationTypeInterface $type */
        foreach ($this->syndicationTypeCollection->getTypes() as $type) {
            if ($type->isEnabledByContext($context)) {
                if (null !== $this->willRunExport($type::getType(), $context)) {
                    return true;
                }
            }
        }

        return false;
    }
}
