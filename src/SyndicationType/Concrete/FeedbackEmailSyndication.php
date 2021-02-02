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
    }
}
