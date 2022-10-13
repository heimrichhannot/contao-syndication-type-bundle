<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\Manager;

use Contao\Model;
use HeimrichHannot\SyndicationTypeBundle\SyndicationContext\SyndicationContext;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkProviderGenerator;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkRenderer;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkRendererContext;
use HeimrichHannot\SyndicationTypeBundle\SyndicationType\ExportSyndicationHandler;
use Symfony\Component\HttpFoundation\RequestStack;

class SyndicationManager
{
    private SyndicationLinkProviderGenerator $linkProviderGenerator;
    private SyndicationLinkRenderer          $linkRenderer;
    private ExportSyndicationHandler         $exportSyndicationHandler;
    private RequestStack                     $requestStack;

    public function __construct(SyndicationLinkProviderGenerator $linkProviderGenerator, SyndicationLinkRenderer $linkRenderer, ExportSyndicationHandler $exportSyndicationHandler, RequestStack $requestStack)
    {
        $this->linkProviderGenerator = $linkProviderGenerator;
        $this->linkRenderer = $linkRenderer;
        $this->exportSyndicationHandler = $exportSyndicationHandler;
        $this->requestStack = $requestStack;
    }

    public function getLinkProviderGenerator(): SyndicationLinkProviderGenerator
    {
        return $this->linkProviderGenerator;
    }

    public function getLinkRenderer(): SyndicationLinkRenderer
    {
        return $this->linkRenderer;
    }

    public function getExportSyndicationHandler(): ExportSyndicationHandler
    {
        return $this->exportSyndicationHandler;
    }

    public function createContext(string $title, string $content, array $rowData, array $configuration = null, string $url = ''): SyndicationContext
    {
        if (null === $configuration) {
            $configuration = $rowData;
        }

        if (empty($url) && $this->requestStack->getCurrentRequest()) {
            if (method_exists($this->requestStack, 'getMainRequest')) {
                $url = $this->requestStack->getMainRequest()->getUri();
            } else {
                $url = $this->requestStack->getMasterRequest()->getUri();
            }
        }

        return new SyndicationContext($title, $content, $url, $rowData, $configuration);
    }

    public function createLinkRendererContextFromModel(Model $model): SyndicationLinkRendererContext
    {
        return new SyndicationLinkRendererContext(
            substr($model::getTable(), 3),
            $model::getTable(),
            $model->id,
            ['model' => $model]
        );
    }
}
