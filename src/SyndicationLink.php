<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle;

use Psr\Link\LinkInterface;

class SyndicationLink implements LinkInterface
{
    /** @var array */
    protected $data;

    /**
     * Return all link data.
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function getHref(): string
    {
        return $this->data['href'];
    }

    public function setHref(string $href): self
    {
        $this->data['href'] = $href;

        return $this;
    }

    public function getCssClass(): string
    {
        return $this->data['cssClass'];
    }

    public function setCssClass(string $cssClass): self
    {
        $this->data['cssClass'] = $cssClass;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->data['title'];
    }

    public function setTitle(string $title): self
    {
        $this->data['title'] = $title;

        return $this;
    }

    public function getContent(): string
    {
        return $this->data['content'];
    }

    public function setContent(string $content): self
    {
        $this->data['href'] = $content;

        return $this;
    }

    public function getTarget(): string
    {
        return $this->data['target'];
    }

    public function setTarget(string $target): self
    {
        $this->data['target'] = $target;

        return $this;
    }

    public function getName(): string
    {
        return $this->data['name'];
    }

    public function setName(string $name): self
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function getRel(): string
    {
        return $this->data['rel'];
    }

    public function setRel(string $rel): self
    {
        $this->data['rel'] = $rel;

        return $this;
    }

    public function getOnClick(): string
    {
        return $this->data['onClick'];
    }

    public function setOnClick(string $onClick): self
    {
        $this->data['onClick'] = $onClick;

        return $this;
    }

    public function setAttributes(array $attributes): self
    {
        $this->data['attributes'] = $attributes;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->data['attributes'];
    }

    public function addAttribute(string $key, string $value): self
    {
        $this->data['attributes'][$key] = $value;

        return $this;
    }

    public function removeAttribute($key): self
    {
        if ($this->hasAttribute($key)) {
            unset($this->data['attributes'][$key]);
        }

        return $this;
    }

    public function hasAttribute($key): bool
    {
        return isset($this->data['attributes'][$key]);
    }

    public function getAttribute($key): ?string
    {
        if ($this->hasAttribute($key)) {
            return $this->data['attributes'][$key];
        }

        return null;
    }

    public function isTemplated()
    {
        return false;
    }

    public function getRels()
    {
        // TODO: Implement getRels() method.
    }
}
