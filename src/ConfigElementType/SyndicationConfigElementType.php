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
use HeimrichHannot\SyndicationTypeBundle\SyndicationType\SyndicationTypeCollection;

class SyndicationConfigElementType implements ConfigElementTypeInterface
{
    /**
     * @var SyndicationTypeCollection
     */
    protected $typeCollection;

    /**
     * SyndicationConfigElementType constructor.
     */
    public function __construct(SyndicationTypeCollection $typeCollection)
    {
        $this->typeCollection = $typeCollection;
    }

    public static function getType(): string
    {
        return 'syndication';
    }

    public function getPalette(string $prependPalette, string $appendPalette): string
    {
        $palette = $prependPalette;

        $categories = $this->typeCollection->getCategories();

        foreach ($categories as $category) {
            $fields = [];
            $types = $this->typeCollection->getTypesByCategory($category);

            foreach ($types as $type) {
                $fields[] = $type->getActivationField();
            }

            if (empty($fields)) {
                continue;
            }
            $palette .= '{'.$category.'_legend},'.implode(',', $fields).';';
        }

        $palette .= $appendPalette;

        return $palette;
//
//        return $prependPalette
//            .'{config_legend},name,syndicationTemplate,syndicationFacebook,syndicationTwitter,syndicationGooglePlus,syndicationLinkedIn,syndicationXing,syndicationMail,syndicationFeedback,syndicationPdf,syndicationPrint,syndicationIcs,syndicationTumblr,syndicationPinterest,syndicationReddit,syndicationWhatsApp;'
//            .$appendPalette;
    }

    public function applyConfiguration(ConfigElementData $configElementData): ConfigElementResult
    {
        return new ConfigElementResult(ConfigElementResult::TYPE_FORMATTED_VALUE, []);
    }
}
