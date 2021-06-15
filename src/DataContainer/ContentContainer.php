<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\DataContainer;

use Contao\ContentModel;
use Contao\DataContainer;
use Symfony\Component\HttpFoundation\RequestStack;

class ContentContainer
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onloadCallback(DataContainer $dc)
    {
        if (null === $dc || !$dc->id || 'edit' !== $this->requestStack->getCurrentRequest()->query->get('act')) {
            return;
        }

        $element = ContentModel::findById($dc->id);

        if (null === $element || 'huh_syndication' !== $element->type) {
            return;
        }

        $dca = &$GLOBALS['TL_DCA']['tl_content'];

        $dca['fields']['text']['label'] = $GLOBALS['TL_LANG']['tl_page']['description'];
        $dca['fields']['text']['eval']['tl_class'] = 'clr';
        $dca['fields']['titleText']['label'] = $GLOBALS['TL_LANG']['tl_page']['pageTitle'];
        $dca['fields']['titleText']['eval']['tl_class'] = 'w50 clr';
    }
}
