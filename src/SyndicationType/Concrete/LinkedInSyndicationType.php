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

class LinkedInSyndicationType extends AbstractSyndicationType
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
        return 'linkedin';
    }

    public function generate(SyndicationContext $context): SyndicationLink
    {
        return $this->linkFactory->create(
            [static::REL_EXTERNAL, static::REL_NOFOLLOW],
            sprintf('https://www.linkedin.com/sharing/share-offsite/?url=%s', rawurlencode($context->getUrl())),
            $this->translator->trans('huh.syndication_type.types.linked_in.title'),
            [
                'class' => 'linked-in',
                'target' => '_blank',
                'title' => $this->translator->trans('huh.syndication_type.types.linked_in.title'),
                'onclick' => 'window.open(this.href,\'\',\'width=500,height=260,modal=yes,left=100,top=50,location=no,menubar=no,resizable=yes,scrollbars=yes,status=no,toolbar=no\');return false',
            ],
            $this
        );
    }
}
