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

class FacebookSyndicationType extends AbstractSyndicationType
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
        return 'facebook';
    }

    public function generate(SyndicationContext $context): SyndicationLink
    {
        return $this->linkFactory->create(
            ['external'],
            sprintf('https://www.facebook.com/sharer/sharer.php?u=%s&t=%s', rawurlencode($context->getUrl()), rawurlencode($context->getTitle())),
            $this->translator->trans('huh.syndication_type.types.facebook.title'),
            [
                'class' => 'facebook',
                'rel' => 'external nofollow',
                'title' => $this->translator->trans('huh.syndication_type.types.facebook.title'),
                'target' => '_blank',
                'onclick' => 'window.open(this.href,\'\',\'width=640,height=380,modal=yes,left=100,top=50,location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no\');return false',
            ],
            $this
        );
    }
}
