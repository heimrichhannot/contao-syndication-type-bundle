<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationType;

use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLink;

interface SyndicationTypeInterface
{
    /**
     * Generate the syndication link.
     */
    public function generate(): SyndicationLink;

    /**
     * Return if the syndication link should be generated.
     */
    public function shouldBeApplied(): bool;
}
