<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\EventListener\Contao;

use Contao\CoreBundle\Routing\ScopeMatcher;
use HeimrichHannot\SyndicationTypeBundle\ContentElement\SyndicationElement;
use HeimrichHannot\SyndicationTypeBundle\Dca\ConfigElementTypeDcaProvider;
use HeimrichHannot\SyndicationTypeBundle\Dca\SyndicationTypeDcaProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;

class LoadDataContainerListener
{
    /**
     * @var SyndicationTypeDcaProvider
     */
    protected $syndicationTypeDcaProvider;
    /**
     * @var ConfigElementTypeDcaProvider
     */
    protected $configElementTypeDcaProvider;
    /**
     * @var array
     */
    protected $bundleConfig;
    /**
     * @var RequestStack
     */
    protected $requestStack;
    /**
     * @var ScopeMatcher
     */
    protected $scopeMatcher;
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * LoadDataContainerListener constructor.
     */
    public function __construct(SyndicationTypeDcaProvider $syndicationTypeDcaProvider, ConfigElementTypeDcaProvider $configElementTypeDcaProvider, array $bundleConfig, RequestStack $requestStack, ScopeMatcher $scopeMatcher, TranslatorInterface $translator)
    {
        $this->syndicationTypeDcaProvider = $syndicationTypeDcaProvider;
        $this->configElementTypeDcaProvider = $configElementTypeDcaProvider;
        $this->bundleConfig = $bundleConfig;
        $this->requestStack = $requestStack;
        $this->scopeMatcher = $scopeMatcher;
        $this->translator = $translator;
    }

    public function __invoke(string $table): void
    {
        switch ($table) {
            case 'tl_reader_config_element':
                $this->prepareReaderConfigElementTable($table);

                break;

            case 'tl_article':
                $this->prepareArticleTable($table);

                break;

            case 'tl_content':
                $this->prepareContentTable($table);

                break;
        }

        $request = $this->requestStack->getCurrentRequest();

        if ($request && $this->scopeMatcher->isBackendRequest($request)) {
            $GLOBALS['TL_CSS']['huh_syndication.backend'] = 'bundles/heimrichhannotsyndicationtype/assets/css/backend.css';
        }
    }

    public function prepareArticleTable(string $table)
    {
        if (isset($this->bundleConfig['enable_article_syndication']) && true === $this->bundleConfig['enable_article_syndication']) {
            $dca = &$GLOBALS['TL_DCA']['tl_article'];
            $dca['palettes']['default'] = str_replace('printable', $this->syndicationTypeDcaProvider->getPalette(false), $dca['palettes']['default']);
            $this->syndicationTypeDcaProvider->prepareDca($table);
        }
    }

    protected function prepareReaderConfigElementTable(string $table)
    {
        $this->configElementTypeDcaProvider->prepareDca($table);
        $this->syndicationTypeDcaProvider->prepareDca($table);

        $GLOBALS['TL_LANG'][$table]['config_element_config_legend'] = $this->translator->trans('huh.syndication_type.legends.tl_reader_config_element.config_element_config_legend');
    }

    protected function prepareContentTable(string $table)
    {
        if (isset($this->bundleConfig['enable_content_syndication']) && true === $this->bundleConfig['enable_content_syndication']) {
            $dca = &$GLOBALS['TL_DCA']['tl_content'];
            $dca['palettes'][SyndicationElement::TYPE] = '{type_legend},type;{template_legend:hide},customTpl;{syndication_config_legend},titleText,text;'.$this->syndicationTypeDcaProvider->getPalette(true);
            $dca['fields']['text']['label'] = $GLOBALS['TL_LANG']['tl_page']['description'];
            $dca['fields']['titleText']['label'] = $GLOBALS['TL_LANG']['tl_page']['pageTitle'];
            $dca['fields']['titleText']['eval']['tl_class'] = 'w50 clr';
            $dca['fields']['text']['eval']['tl_class'] = 'clr';
            $this->syndicationTypeDcaProvider->prepareDca($table);
        }
    }
}
