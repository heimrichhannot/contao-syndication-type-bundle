<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationType;

use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkContext;

abstract class AbstractSyndicationType implements SyndicationTypeInterface
{
    const CATEGORY_SHARE = 'share';
    const CATEGORY_EXPORT = 'export';

    public static function getActivationField(): string
    {
        return 'syndication'.ucfirst(static::getType());
    }

    public function isEnabledByContext(SyndicationLinkContext $context): bool
    {
        return true === (bool) $context->getConfiguration()[$this->getActivationField()];
    }

    public function getCategory(): string
    {
        return static::CATEGORY_SHARE;
    }
}
