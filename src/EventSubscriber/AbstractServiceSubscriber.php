<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\EventSubscriber;

use Symfony\Contracts\Service\ServiceSubscriberInterface;

if (class_exists('Symfony\Contracts\Service\ServiceSubscriberInterface')) {
    abstract class AbstractServiceSubscriber implements ServiceSubscriberInterface
    {
    }
} else {
    abstract class AbstractServiceSubscriber implements \Symfony\Component\DependencyInjection\ServiceSubscriberInterface
    {
    }
}
