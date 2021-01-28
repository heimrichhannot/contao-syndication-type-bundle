<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationLink;

use HeimrichHannot\SyndicationTypeBundle\SyndicationType\SyndicationTypeCollection;

class SyndicationLinkProviderGenerator
{
    /**
     * @var SyndicationTypeCollection
     */
    protected $collection;

    /**
     * SyndicationLinkProviderGenerator constructor.
     */
    public function __construct(SyndicationTypeCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @param string[] $types a list of syndication types to render links for
     */
    public function generate(array $types, SyndicationLinkContext $context): SyndicationLinkProvider
    {
        $links = [];

        foreach ($types as $typeName) {
            if ($this->collection->hasType($typeName)) {
                $type = $this->collection->getType($typeName);

                if ($link = $type->generate($context)) {
                    $links[] = $link;
                }
            }
        }

        return $this->createProvider($links);
    }

    public function generateFromContext(SyndicationLinkContext $context): SyndicationLinkProvider
    {
        $links = [];

        foreach ($this->collection->getTypes() as $type) {
            if ($type->isEnabledByContext($context)) {
                if ($link = $type->generate($context)) {
                    $links[] = $link;
                }
            }
        }

        return $this->createProvider($links);
    }

    /**
     * @param SyndicationLink[] $links
     */
    public function createProvider(array $links): SyndicationLinkProvider
    {
        return new SyndicationLinkProvider($links);
    }
}
