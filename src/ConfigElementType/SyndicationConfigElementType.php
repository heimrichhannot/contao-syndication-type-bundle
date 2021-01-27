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

class SyndicationConfigElementType implements ConfigElementTypeInterface
{
    public static function getType(): string
    {
        // TODO: Implement getType() method.
    }

    public function getPalette(string $prependPalette, string $appendPalette): string
    {
        // TODO: Implement getPalette() method.
    }

    public function applyConfiguration(ConfigElementData $configElementData): ConfigElementResult
    {
        // TODO: Implement applyConfiguration() method.
    }
}
