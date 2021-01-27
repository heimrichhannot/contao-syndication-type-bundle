<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationLink;

use Psr\Link\LinkProviderInterface;

class SyndicationLinkProvider implements LinkProviderInterface
{
    /**
     * @var SyndicationLink[]
     */
    protected $links;

    /**
     * SyndicationLinkProvider constructor.
     *
     * @param SyndicationLink[]
     */
    public function __construct(array $links)
    {
        $this->links = $links;
    }

    public function getLinks()
    {
        return $this->links;
    }

    public function getLinksByRel($rel)
    {
        $results = [];

        foreach ($this->links as $link) {
            if (empty($link->getRels())) {
                continue;
            }

            if (\in_array($rel, $link->getRels())) {
                $results[] = $link;
            }
        }

        return $results;
    }
}
