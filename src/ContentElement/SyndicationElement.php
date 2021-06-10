<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\ContentElement;

use Contao\BackendTemplate;
use Contao\ContentElement;
use Contao\ContentModel;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\System;
use HeimrichHannot\SyndicationTypeBundle\Event\BeforeContentElementParseEvent;
use HeimrichHannot\SyndicationTypeBundle\SyndicationContext\SyndicationContext;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkProviderGenerator;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkRenderer;
use HeimrichHannot\SyndicationTypeBundle\SyndicationType\SyndicationTypeCollection;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;

class SyndicationElement extends ContentElement
{
    public const TYPE = 'huh_syndication';
    protected $strTemplate = 'ce_huh_syndication';

    /**
     * @var SyndicationLinkProviderGenerator
     */
    protected $syndicationGenerator;

    /**
     * @var SyndicationLinkRenderer
     */
    protected $syndicationLinkRenderer;

    /**
     * @var SyndicationLinkProviderGenerator
     */
    protected $syndicationLinkProviderGenerator;

    /**
     * @var SyndicationTypeCollection
     */
    protected $syndicationTypeCollection;

    /**
     * @var ScopeMatcher
     */
    protected $scopeMatcher;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    public function __construct(ContentModel $objElement, string $strColumn = 'main')
    {
        parent::__construct($objElement, $strColumn);
        $this->syndicationGenerator = System::getContainer()->get(SyndicationLinkProviderGenerator::class);
        $this->syndicationLinkRenderer = System::getContainer()->get(SyndicationLinkRenderer::class);
        $this->syndicationLinkProviderGenerator = System::getContainer()->get(SyndicationLinkProviderGenerator::class);
        $this->syndicationTypeCollection = System::getContainer()->get(SyndicationTypeCollection::class);
        $this->requestStack = System::getContainer()->get('request_stack');
        $this->scopeMatcher = System::getContainer()->get('contao.routing.scope_matcher');
        $this->eventDispatcher = System::getContainer()->get('event_dispatcher');
    }

    protected function compile(): string
    {
        if ($this->scopeMatcher->isBackendRequest($this->requestStack->getCurrentRequest())) {
            $this->Template = new BackendTemplate($this->strTemplate);
        }

        if ($this->syndicationGenerator) {
            $title = $this->titleText;
            $content = $this->text;
            $url = $this->requestStack->getMasterRequest()->getUri();
            $data = $this->Template->getData();
            $configuration = $this->getModel()->row();

            /** @noinspection PhpMethodParametersCountMismatchInspection */
            /** @noinspection PhpParamsInspection */
            /** @var BeforeContentElementParseEvent $event */
            $event = $this->eventDispatcher->dispatch(BeforeContentElementParseEvent::class, new BeforeContentElementParseEvent($title, $content, $url, $data, $configuration));

            $context = new SyndicationContext($event->getTitle(), $event->getContent(), $event->getUrl(), $event->getData(), $event->getConfiguration());
            $linkProvider = $this->syndicationLinkProviderGenerator->generateFromContext($context);

            $this->Template->syndication = $this->syndicationLinkRenderer->renderProvider($linkProvider);
        }

        return $this->Template->parse();
    }
}
