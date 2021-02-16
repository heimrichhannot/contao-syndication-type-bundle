<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationType;

use HeimrichHannot\SyndicationTypeBundle\SyndicationContext\SyndicationContext;
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
    public function generate(SyndicationContext $context): SyndicationLink;

    /**
     * Return if the syndication link should be generated.
     */
    public function isEnabledByContext(SyndicationContext $context): bool;

    /**
     * Return the database field to active the syndication type.
     * Field name should be the keyword "syndication" + the type in camel case, "syndicationFacebook" for example.
     * Value should be included in the SyndicationContext data array.
     */
    public static function getActivationField(): string;

    /**
     * Return the syndication category.
     *
     * For supported types, see AbstractSyndicationType categoriy constants.
     */
    public function getCategory(): string;

    /**
     * Return the palette for this syndication type.
     * The palette is a comma seperated list of dca fields.
     * Don't add legends or semicolons.
     */
    public function getPalette(): string;
}
