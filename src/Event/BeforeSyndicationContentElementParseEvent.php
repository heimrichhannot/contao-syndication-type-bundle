<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class BeforeSyndicationContentElementParseEvent extends Event
{
    public const NAME = 'huh.syndication_type.before_syndication_content_element_parse';

    /**
     * @var string
     */
    protected $title;
    /**
     * @var string
     */
    protected $content;
    /**
     * @var string
     */
    protected $url;
    /**
     * @var array
     */
    protected $data;
    /**
     * @var array
     */
    protected $configuration;

    public function __construct(string $title, string $content, string $url, array $data, array $configuration)
    {
        $this->title = $title;
        $this->content = $content;
        $this->url = $url;
        $this->data = $data;
        $this->configuration = $configuration;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }
}
