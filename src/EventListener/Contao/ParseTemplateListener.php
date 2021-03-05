<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\EventListener\Contao;

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
     * ParseTemplateListener constructor.
     */
    public function __construct(ExportSyndicationHandler $exportSyndicationHandler, RequestStack $requestStack)
    {
        $this->exportSyndicationHandler = $exportSyndicationHandler;
        $this->requestStack = $requestStack;
    }

    public function __invoke(Template $template): void
    {
        if ('article' !== $template->type || $template->isSyndicationExportTemplate) {
            return;
        }

        $buffer = $template->inherit();

        // HOOK: add custom parse filters
        if (isset($GLOBALS['TL_HOOKS']['parseFrontendTemplate']) && \is_array($GLOBALS['TL_HOOKS']['parseFrontendTemplate'])) {
            foreach ($GLOBALS['TL_HOOKS']['parseFrontendTemplate'] as $callback) {
                $this->import($callback[0]);
                $buffer = $this->{$callback[0]}->{$callback[1]}($buffer, $template->getName());
            }
        }

        $context = new SyndicationContext($template->title, $buffer, $this->requestStack->getMasterRequest()->getUri(), $template->getData(), $template->getData());
        $this->exportSyndicationHandler->exportByContext($context);
//
//        $context = new SyndicationContext($template->title, $module->teaser, $this->requestStack->getMasterRequest()->getUri(), $template->getData(), $data);
//        $links = $this->linkRenderer->renderProvider($this->syndicationGenerator->generateFromContext($context));
//        $template->elements = array_merge([$links], $template->elements);
    }
}
