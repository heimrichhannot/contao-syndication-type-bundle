<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationType\Concrete;

use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLink;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkContext;

class FeedbackEmailSyndication extends EmailSyndicationType
{
    public static function getType(): string
    {
        return 'feedback_email';
    }

    public function generate(SyndicationLinkContext $context): SyndicationLink
    {
        $subject = strip_tags(str_replace(
            ['%title%', '%content%', '%url%'],
            [$context->getTitle(), $context->getContent(), $context->getUrl()],
            $context->getConfiguration()['syndicationEmailSubject']
        ));
        $body = strip_tags(str_replace(
            ['%title%', '%content%', '%url%'],
            [$context->getTitle(), $context->getContent(), $context->getUrl()],
            $context->getConfiguration()['syndicationEmailBody']
        ));

        $href = $this->generateMailToLink($context->getConfiguration()['syndicationEmailAddress'], [
            'subject' => $subject,
            'body' => $body,
        ]);

        return $this->linkFactory->create(
            ['external', 'application'],
            $href,
            $this->translator->trans('huh.syndication_type.types.feedback_email.title'),
            [
                'class' => 'email mail feedback',
                'title' => $this->translator->trans('huh.syndication_type.types.feedback_email.title'),
            ],
            $this
        );
    }

    public function getPalette(): string
    {
        return 'syndicationEmailAddress,syndicationEmailSubject,syndicationEmailBody';
    }
}
