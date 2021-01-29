<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\ConfigElementType;

use HeimrichHannot\ConfigElementTypeBundle\ConfigElementType\ConfigElementData;
use HeimrichHannot\ConfigElementTypeBundle\ConfigElementType\ConfigElementResult;
use HeimrichHannot\ConfigElementTypeBundle\ConfigElementType\ConfigElementTypeInterface;
use HeimrichHannot\HeadBundle\Tag\Meta\MetaDescription;
use HeimrichHannot\SyndicationTypeBundle\Dca\DcaFieldProvider;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkContext;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkProviderGenerator;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkRenderer;
use HeimrichHannot\SyndicationTypeBundle\SyndicationType\SyndicationTypeCollection;

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
     * @var DcaFieldProvider
     */
    protected $dcaFieldProvider;

    /**
     * SyndicationConfigElementType constructor.
     */
    public function __construct(SyndicationTypeCollection $typeCollection, SyndicationLinkProviderGenerator $linkProviderGenerator, SyndicationLinkRenderer $linkRenderer, DcaFieldProvider $dcaFieldProvider)
    {
        $this->typeCollection = $typeCollection;
        $this->linkProviderGenerator = $linkProviderGenerator;
        $this->linkRenderer = $linkRenderer;
        $this->dcaFieldProvider = $dcaFieldProvider;
    }

    public static function getType(): string
    {
        return 'syndication';
    }

    public function getPalette(string $prependPalette, string $appendPalette): string
    {
        $palette = $prependPalette;
        $palette .= $this->dcaFieldProvider->getPalette(true);
        $palette .= $appendPalette;

        return $palette;
//
//        return $prependPalette
//            .'{config_legend},name,syndicationTemplate,syndicationFacebook,syndicationTwitter,syndicationGooglePlus,syndicationLinkedIn,syndicationXing,syndicationMail,syndicationFeedback,syndicationPdf,syndicationPrint,syndicationIcs,syndicationTumblr,syndicationPinterest,syndicationReddit,syndicationWhatsApp;'
//            .$appendPalette;
    }

    public function applyConfiguration(ConfigElementData $configElementData): ConfigElementResult
    {
        $title = $configElementData->getItemData()['headline'];
        $description = $configElementData->getItemData()['teaser'];

        $links = $this->linkProviderGenerator->generateFromContext(new SyndicationLinkContext(
            $title, $description, 'http://google.com', $configElementData->getItemData(), $configElementData->getConfiguration()->row()
        ));

        return new ConfigElementResult(ConfigElementResult::TYPE_FORMATTED_VALUE, $this->linkRenderer->renderProvider($links));
    }
}
