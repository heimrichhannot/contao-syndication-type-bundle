<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\ConfigElementType;

use Contao\Model;
use HeimrichHannot\ConfigElementTypeBundle\ConfigElementType\ConfigElementData;
use HeimrichHannot\ConfigElementTypeBundle\ConfigElementType\ConfigElementResult;
use HeimrichHannot\ConfigElementTypeBundle\ConfigElementType\ConfigElementTypeInterface;
use HeimrichHannot\HeadBundle\Tag\Meta\MetaDescription;
use HeimrichHannot\SyndicationTypeBundle\Dca\SyndicationTypeDcaProvider;
use HeimrichHannot\SyndicationTypeBundle\SyndicationContext\SyndicationContext;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkProviderGenerator;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkRenderer;
use HeimrichHannot\SyndicationTypeBundle\SyndicationType\SyndicationTypeCollection;
use Symfony\Component\HttpFoundation\RequestStack;

class SyndicationConfigElementType implements ConfigElementTypeInterface
{
    /**
     * @var SyndicationTypeCollection
     */
    protected $typeCollection;
    /**
     * @var SyndicationLinkProviderGenerator
     */
    protected $linkProviderGenerator;
    /**
     * @var MetaDescription
     */
    protected $metaDescription;
    /**
     * @var SyndicationLinkRenderer
     */
    protected $linkRenderer;
    /**
     * @var SyndicationTypeDcaProvider
     */
    protected $dcaFieldProvider;
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * SyndicationConfigElementType constructor.
     */
    public function __construct(SyndicationTypeCollection $typeCollection, SyndicationLinkProviderGenerator $linkProviderGenerator, SyndicationLinkRenderer $linkRenderer, SyndicationTypeDcaProvider $dcaFieldProvider, RequestStack $requestStack)
    {
        $this->typeCollection = $typeCollection;
        $this->linkProviderGenerator = $linkProviderGenerator;
        $this->linkRenderer = $linkRenderer;
        $this->dcaFieldProvider = $dcaFieldProvider;
        $this->requestStack = $requestStack;
    }

    public static function getType(): string
    {
        return 'syndication_type';
    }

    public function getPalette(string $prependPalette, string $appendPalette): string
    {
        $palette = $prependPalette;
        $palette .= '{config_element_config_legend},synTitleField,synContentField;';
        $palette .= $this->dcaFieldProvider->getPalette(true);
        $palette .= $appendPalette;

        return $palette;

//        return $prependPalette
//            .'{config_legend},name,syndicationTemplate,syndicationFacebook,syndicationTwitter,syndicationGooglePlus,syndicationLinkedIn,syndicationXing,syndicationMail,syndicationFeedback,syndicationPdf,syndicationPrint,syndicationIcs,syndicationTumblr,syndicationPinterest,syndicationReddit,syndicationWhatsApp;'
//            .$appendPalette;
    }

    public function applyConfiguration(ConfigElementData $configElementData): ConfigElementResult
    {
        $links = $this->linkProviderGenerator->generateFromContext($this->getSyndicationContext($configElementData->getItemData(), $configElementData->getConfiguration()));

        return new ConfigElementResult(ConfigElementResult::TYPE_FORMATTED_VALUE, $this->linkRenderer->renderProvider($links));
    }

    public function getSyndicationContext(array $data, Model $configuration): SyndicationContext
    {
        $title = $data[$configuration->synTitleField];
        $description = $data[$configuration->synContentField];
        $url = $this->requestStack->getMasterRequest()->getUri();

        return new SyndicationContext($title, $description, $url, $data, $configuration->row());
    }
}
