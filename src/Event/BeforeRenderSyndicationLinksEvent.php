<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\Event;

use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkProvider;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkRendererContext;
use Symfony\Component\EventDispatcher\Event;

class BeforeRenderSyndicationLinksEvent extends Event
{
    /**
     * @var array
     */
    protected $links;
    /**
     * @var SyndicationLinkProvider
     */
    protected $provider;
    /**
     * @var array
     */
    protected $linkRenderOptions;
    /**
     * @var array
     */
    protected $providerRendererOptions;
    /**
     * @var SyndicationLinkRendererContext
     */
    private $rendererContext;

    public function __construct(array $links, SyndicationLinkProvider $provider, array $linkRenderOptions, array $options, SyndicationLinkRendererContext $rendererContext)
    {
        $this->links = $links;
        $this->provider = $provider;
        $this->linkRenderOptions = $linkRenderOptions;
        $this->providerRendererOptions = $options;
        $this->rendererContext = $rendererContext;
    }

    public function getLinks(): array
    {
        return $this->links;
    }

    public function setLinks(array $links): void
    {
        $this->links = $links;
    }

    public function getProvider(): SyndicationLinkProvider
    {
        return $this->provider;
    }

    public function getLinkRenderOptions(): array
    {
        return $this->linkRenderOptions;
    }

    public function setLinkRenderOptions(array $linkRenderOptions): void
    {
        $this->linkRenderOptions = $linkRenderOptions;
    }

    public function getProviderRendererOptions(): array
    {
        return $this->providerRendererOptions;
    }

    /**
     * @return SyndicationLinkRendererContext
     */
    public function getRendererContext(): SyndicationLinkRendererContext
    {
        return $this->rendererContext;
    }


}
