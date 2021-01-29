<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\Dca;

use HeimrichHannot\SyndicationTypeBundle\SyndicationType\SyndicationTypeCollection;

class DcaFieldProvider
{
    /**
     * @var SyndicationTypeCollection
     */
    protected $typeCollection;

    public function __construct(SyndicationTypeCollection $typeCollection)
    {
        $this->typeCollection = $typeCollection;
    }

    public function getFields(): array
    {
        $fields = [];

        $fields['syndicationFacebook'] = [
            'label' => &$GLOBALS['TL_LANG']['tl_reader_config_element']['syndicationFacebook'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 clr'],
            'sql' => "char(1) NOT NULL default ''",
        ];

        return $fields;
    }

    /**
     * Return the dca palette for syndication types.
     *
     * @param bool $splitByCategories Set to false, if you want all fields are within an single syndication type legend.
     *                                Otherwise multiple legends returned based on categories of syndication (typical export and share).
     */
    public function getPalette(bool $splitByCategories = true): string
    {
        $palette = '';

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

            if ($splitByCategories) {
                $palette .= '{'.$category.'_legend},';
            }
            $palette .= implode(',', $fields).';';
        }

        if (!$splitByCategories) {
            $palette = '{syndication_type_legend},'.$palette;
        }

        return $palette;
    }
}
