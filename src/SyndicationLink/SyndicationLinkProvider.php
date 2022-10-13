<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationLink;

use Psr\Link\LinkInterface;
use Psr\Link\LinkProviderInterface;

class SyndicationLinkProvider implements LinkProviderInterface
{
    /**
     * @var SyndicationLink[]
     */
    protected array $links;

    /**
     * SyndicationLinkProvider constructor.
     *
     * @param SyndicationLink[] $links
     */
    public function __construct(array $links)
    {
        $this->links = $links;
    }

    /**
     * @return array|SyndicationLink[]|LinkInterface[]|\Traversable|iterable
     */
    public function getLinks(): iterable
    {
        return $this->links;
    }

    public function getLinksByRel($rel): iterable
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
