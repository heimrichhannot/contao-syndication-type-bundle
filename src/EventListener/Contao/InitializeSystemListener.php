<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\EventListener\Contao;

use HeimrichHannot\SyndicationTypeBundle\ContentElement\SyndicationElement;

class InitializeSystemListener
{
    /**
     * @var array
     */
    protected $bundleConfig;

    public function __construct(array $bundleConfig)
    {
        $this->bundleConfig = $bundleConfig;
    }

    public function __invoke()
    {
        if (isset($this->bundleConfig['enable_content_syndication']) && true === $this->bundleConfig['enable_content_syndication']) {
            $GLOBALS['TL_CTE']['links'][SyndicationElement::TYPE] = SyndicationElement::class;
        }
    }
}
