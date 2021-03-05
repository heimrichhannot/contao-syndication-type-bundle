<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\EventListener\Contao;

use Contao\FrontendTemplate;
use Contao\Module;
use HeimrichHannot\SyndicationTypeBundle\SyndicationContext\SyndicationContext;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkProviderGenerator;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkRenderer;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @Hook("compileArticle")
 */
class CompileArticleListener
{
    /**
     * @var SyndicationLinkProviderGenerator
     */
    protected $syndicationGenerator;
    /**
     * @var RequestStack
     */
    protected $requestStack;
    /**
     * @var SyndicationLinkRenderer
     */
    protected $linkRenderer;
    /**
     * @var array
     */
    protected $bundleConfig;

    /**
     * CompileArticleListener constructor.
     */
    public function __construct(SyndicationLinkProviderGenerator $syndicationGenerator, RequestStack $requestStack, SyndicationLinkRenderer $linkRenderer, array $bundleConfig)
    {
        $this->syndicationGenerator = $syndicationGenerator;
        $this->requestStack = $requestStack;
        $this->linkRenderer = $linkRenderer;
        $this->bundleConfig = $bundleConfig;
    }

    public function __invoke(FrontendTemplate $template, array $data, Module $module): void
    {
        if (isset($this->bundleConfig['enable_article_syndication']) && true === $this->bundleConfig['enable_article_syndication']) {
            $context = new SyndicationContext($module->title, $module->teaser, $this->requestStack->getMasterRequest()->getUri(), $template->getData(), $data);
            $links = $this->linkRenderer->renderProvider($this->syndicationGenerator->generateFromContext($context));
            $template->elements = array_merge([$links], $template->elements);
        }
    }
}
