<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\SyndicationTypeBundle\SyndicationContext;


class SyndicationContext
{
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
    protected $configuration;
    /**
     * @var array
     */
    protected $data;

    /**
     * SyndicationContext constructor.
     */
    public function __construct(string $title, string $content, string $url, array $data, array $configuration)
    {
        $this->title = $title;
        $this->content = $content;
        $this->url = $url;
        $this->configuration = $configuration;
        $this->data = $data;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function getData(): array
    {
        return $this->data;
    }
}