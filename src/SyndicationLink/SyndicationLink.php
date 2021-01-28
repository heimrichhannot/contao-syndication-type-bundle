<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationLink;

use Psr\Link\LinkInterface;

class SyndicationLink implements LinkInterface
{
    /**
     * @var array
     */
    protected $rels;
    /**
     * @var string
     */
    protected $href;
    /**
     * @var array
     */
    protected $attributes;
    /**
     * @var string
     */
    protected $content;

    /**
     * SyndicationLink constructor.
     */
    public function __construct(array $rels, string $href, string $content, array $attributes)
    {
        $this->rels = $rels;
        $this->href = $href;
        $this->content = $content;
        $this->attributes = $attributes;
    }

    public function getHref()
    {
        return $this->href;
    }

    public function isTemplated()
    {
        return false;
    }

    public function getRels()
    {
        return $this->rels;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
