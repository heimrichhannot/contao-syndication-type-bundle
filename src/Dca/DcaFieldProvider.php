<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\Dca;

class DcaFieldProvider
{
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
}
