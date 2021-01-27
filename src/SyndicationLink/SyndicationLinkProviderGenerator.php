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
    }
}
