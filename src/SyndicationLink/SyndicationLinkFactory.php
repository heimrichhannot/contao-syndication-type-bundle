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
     * @param string[] $rels       Define the link releationship. See https://www.php-fig.org/psr/psr-13/#13-relationships for more information and allowed values.
     * @param string   $href       The target link. See LinkInterface (https://www.php-fig.org/psr/psr-13/#31-psrlinklinkinterface) for more information.
     * @param string[] $attributes A key-value list of attributes
     */
    public function create(array $rels, string $href, string $content, array $attributes): SyndicationLink
    {
        return new SyndicationLink($rels, $href, $content, $attributes);
    }
}
