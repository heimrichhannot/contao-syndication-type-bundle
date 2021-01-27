<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationLink;

class SyndicationLinkFactory
{
    /**
     * @param string[] $rels
     * @param string[] $attributes A key-value list of attributes
     */
    public function create(array $rels, string $href, array $attributes): SyndicationLink
    {
        return new SyndicationLink($rels, $href, $attributes);
    }
}
