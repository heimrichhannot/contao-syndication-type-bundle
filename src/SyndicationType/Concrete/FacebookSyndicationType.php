<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationType\Concrete;

use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLink;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkFactory;
use HeimrichHannot\SyndicationTypeBundle\SyndicationType\SyndicationTypeInterface;

class FacebookSyndicationType implements SyndicationTypeInterface
{
    /**
     * @var SyndicationLinkFactory
     */
    protected $linkFactory;

    /**
     * FacebookSyndicationType constructor.
     */
    public function __construct(SyndicationLinkFactory $linkFactory)
    {
        $this->linkFactory = $linkFactory;
    }

    public static function getType(): string
    {
        return 'facebook';
    }

    public function generate(): SyndicationLink
    {
        return $this->linkFactory->create(['external'], 'http://facebook.com', []);
    }

    public function shouldBeApplied(): bool
    {
        return false;
    }
}
