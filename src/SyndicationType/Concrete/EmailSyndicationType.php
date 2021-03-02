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

class EmailSyndicationType extends AbstractSyndicationType
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
     * EmailSyndicationType constructor.
     */
    public function __construct(SyndicationLinkFactory $linkFactory, TranslatorInterface $translator)
    {
        $this->linkFactory = $linkFactory;
        $this->translator = $translator;
    }

    public static function getType(): string
    {
        return 'email';
    }

    public function generate(SyndicationContext $context): SyndicationLink
    {
        $subject = strip_tags(str_replace(
            ['%title%', '%content%', '%url%'],
            [$context->getTitle(), $context->getContent(), $context->getUrl()],
            $context->getConfiguration()['synEmailSubject']
        ));
        $body = strip_tags(str_replace(
            ['%title%', '%content%', '%url%'],
            [$context->getTitle(), $context->getContent(), $context->getUrl()],
            $context->getConfiguration()['synEmailBody']
        ));

        $href = $this->generateMailToLink('', [
            'subject' => $subject,
            'body' => $body,
        ]);

        return $this->linkFactory->create(
            ['external', 'application'],
            $href,
            $this->translator->trans('huh.syndication_type.types.email.title'),
            [
                'class' => 'email mail',
                'title' => $this->translator->trans('huh.syndication_type.types.email.title'),
            ],
            $this
        );
    }

    public function generateMailToLink(string $receiver = '', array $parts = []): string
    {
        $link = 'mailto:';

        if (!empty($receiver)) {
            $link .= $receiver;
        }

        if (!empty($parts)) {
            $link .= '?'.http_build_query($parts, '', '&', PHP_QUERY_RFC3986);
        }

        return $link;
    }

    public function getPalette(): string
    {
        return 'synEmailSubject,synEmailBody';
    }
}
