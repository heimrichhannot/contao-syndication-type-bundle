<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationType\Concrete;

use HeimrichHannot\SyndicationTypeBundle\SyndicationContext\SyndicationContext;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLink;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkFactory;
use HeimrichHannot\SyndicationTypeBundle\SyndicationType\AbstractSyndicationType;
use Symfony\Component\Translation\TranslatorInterface;

class WhatsAppSyndicationType extends AbstractSyndicationType
{
    /**
     * @var SyndicationLinkFactory
     */
    protected $linkFactory;
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * FacebookSyndicationType constructor.
     */
    public function __construct(SyndicationLinkFactory $linkFactory, TranslatorInterface $translator)
    {
        $this->linkFactory = $linkFactory;
        $this->translator = $translator;
    }

    public static function getType(): string
    {
        return 'whatsapp';
    }

    public function generate(SyndicationContext $context): SyndicationLink
    {
        return $this->linkFactory->create(
            [static::REL_EXTERNAL, static::REL_NOFOLLOW],
            'https://wa.me/?text='.rawurlencode($context->getTitle()." \r\n".$context->getUrl()),
            $this->translator->trans('huh.syndication_type.types.whatsapp.title'),
            [
                'class' => '',
                'title' => $this->translator->trans('huh.syndication_type.types.whatsapp.title'),
                'data-action' => 'share/whatsapp/share',
            ],
            $this
        );
    }
}
