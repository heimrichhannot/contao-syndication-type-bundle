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
     * Return a type name for the syndication type. This type string is used for identifying the type and translations.
     * String should written like an alias, one word in lowercase or in snake_case for multiple words.
     *
     * Examples:
     * - facebook
     * - twitter
     * - export_pdf
     */
    public static function getType(): string;

    /**
     * Generate the syndication link.
     */
    public function generate(): SyndicationLink;

    /**
     * Return if the syndication link should be generated.
     */
    public function shouldBeApplied(): bool;
}
