<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationType\Concrete;

use Contao\StringUtil;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLink;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkContext;
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

    public function generate(SyndicationLinkContext $context): SyndicationLink
    {
        $emailData = str_replace(
            ['%title%', '%content%', '%url%'],
            [$context->getTitle(), $context->getContent(), $context->getUrl()],
            [
                'subject' => $context->getData()['syndicationEmailSubject'],
                'body' => $context->getData()['syndicationEmailBody'],
            ]
        );

        $href = sprintf('mailto:?subject=%s&body=%s', $emailData['subject'], $emailData['body']);

//        $href = sprintf('mailto:?subject=%s&body=%s',
//            rawurlencode(StringUtil::decodeEntities($this->translator->trans(
//                $context->getData()['mailSubjectLabel'], ['%title%' => $context->getTitle(), '%url' => $context->getUrl()])
//            )),
//            rawurlencode(StringUtil::decodeEntities($this->translator->trans(
//                $context->getData()['mailBodyLabel'], ['%title%' => $context->getTitle(), '%url%' => $context->getUrl()])))
//        );

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
}
