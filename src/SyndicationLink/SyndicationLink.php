<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationLink;

use Psr\Link\LinkInterface;

class SyndicationLink implements LinkInterface
{
    protected array $rels;
    protected string $href;
    protected array $attributes;
    protected string $content;
    private string $type;

    /**
     * SyndicationLink constructor.
     */
    public function __construct(string $type, array $rels, string $href, string $content, array $attributes)
    {
        $this->rels = $rels;
        $this->href = $href;
        $this->content = $content;
        $this->attributes = $attributes;
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function isTemplated(): bool
    {
        return false;
    }

    public function getRels(): array
    {
        return $this->rels;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
