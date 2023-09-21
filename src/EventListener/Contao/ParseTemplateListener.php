<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\EventListener\Contao;

use Contao\System;
use Contao\Template;
use HeimrichHannot\SyndicationTypeBundle\SyndicationContext\SyndicationContext;
use HeimrichHannot\SyndicationTypeBundle\SyndicationType\ExportSyndicationHandler;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @Hook("parseTemplate")
 */
class ParseTemplateListener
{
    /**
     * @var ExportSyndicationHandler
     */
    protected $exportSyndicationHandler;
    /**
     * @var RequestStack
     */
    protected $requestStack;
    /**
     * @var array
     */
    protected $bundleConfig;

    /**
     * ParseTemplateListener constructor.
     */
    public function __construct(ExportSyndicationHandler $exportSyndicationHandler, RequestStack $requestStack, array $bundleConfig)
    {
        $this->exportSyndicationHandler = $exportSyndicationHandler;
        $this->requestStack = $requestStack;
        $this->bundleConfig = $bundleConfig;
    }

    public function __invoke(Template $template): void
    {
        $this->doExport($template);
    }

    protected function doExport(Template $template): void
    {
        if (isset($this->bundleConfig['enable_article_syndication']) && true === $this->bundleConfig['enable_article_syndication']) {
            if ('article' !== $template->type || $template->isSyndicationExportTemplate) {
                return;
            }

            $template->printable = false;

            $buffer = $template->inherit();

            // HOOK: add custom parse filters
            if (isset($GLOBALS['TL_HOOKS']['parseFrontendTemplate']) && \is_array($GLOBALS['TL_HOOKS']['parseFrontendTemplate'])) {
                foreach ($GLOBALS['TL_HOOKS']['parseFrontendTemplate'] as $callback) {
                    $instance = System::importStatic($callback[0]);
                    $buffer = $instance->{$callback[1]}($buffer, $template->getName());
                }
            }

            $context = new SyndicationContext((string) $template->title, $buffer, $this->requestStack->getMasterRequest()->getUri(), $template->getData(), $template->getData());
            $this->exportSyndicationHandler->exportByContext($context);
        }
    }
}
