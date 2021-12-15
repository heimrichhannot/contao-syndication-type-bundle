<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationLink;

use HeimrichHannot\SyndicationTypeBundle\SyndicationType\SyndicationTypeInterface;

class SyndicationLinkFactory
{
    /**
     * Return an SyndicationLink object.
     *
     * @param string[] $rels       Define the link releationship. See https://www.php-fig.org/psr/psr-13/#13-relationships for more information and allowed values.
     * @param string   $href       The target link. See LinkInterface (https://www.php-fig.org/psr/psr-13/#31-psrlinklinkinterface) for more information.
     * @param string[] $attributes A key-value list of attributes
     */
    public function create(array $rels, string $href, string $content, array $attributes, SyndicationTypeInterface $type): SyndicationLink
    {
        if (!\in_array($type->getCategory(), $rels)) {
            $rels[] = $type->getCategory();
        }

        if (!\in_array($type::getType(), $rels)) {
            $rels[] = $type::getType();
        }

        $attributes['class'] = trim(($attributes['class'] ?? '').' '.$type::getType());

        return new SyndicationLink($type::getType(), $rels, $href, $content, $attributes);
    }
}
