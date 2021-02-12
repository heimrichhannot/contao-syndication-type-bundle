<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationType\Concrete;

use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLink;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkContext;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkFactory;
use HeimrichHannot\SyndicationTypeBundle\SyndicationType\AbstractSyndicationType;
use Symfony\Component\Translation\TranslatorInterface;

class TwitterSyndicationType extends AbstractSyndicationType
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
     * TwitterSyndicationType constructor.
     */
    public function __construct(SyndicationLinkFactory $linkFactory, TranslatorInterface $translator)
    {
        $this->linkFactory = $linkFactory;
        $this->translator = $translator;
    }

    public static function getType(): string
    {
        return 'twitter';
    }

    public function generate(SyndicationLinkContext $context): SyndicationLink
    {
        return $this->linkFactory->create(
            ['external'],
            sprintf('https://twitter.com/intent/tweet?url=%s&text=%s', rawurlencode($context->getUrl()), rawurlencode($context->getTitle())),
            $this->translator->trans('huh.syndication_type.types.twitter.title'),
            [
                'class' => 'twitter',
                'rel' => 'nofollow',
                'target' => '_blank',
                'onclick' => 'window.open(this.href,\'\',\'width=500,height=260,modal=yes,left=100,top=50,location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no\');return false',
            ],
            $this
        );
    }
}
