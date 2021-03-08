<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\EventSubscriber;

use HeimrichHannot\EncoreBundle\Dca\DcaGenerator;
use HeimrichHannot\SyndicationTypeBundle\Event\AddSyndicationTypeFieldsEvent;
use HeimrichHannot\SyndicationTypeBundle\Event\AddSyndicationTypePaletteSelectorsEvent;
use HeimrichHannot\SyndicationTypeBundle\Event\AddSyndicationTypeSubpalettesEvent;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class EncoreBundleSupportEventSubscriber implements EventSubscriberInterface, ServiceSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * EncoreBundleSupportEventSubscriber constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return [
            AddSyndicationTypeFieldsEvent::class => 'onAddFields',
            AddSyndicationTypeSubpalettesEvent::class => 'onAddSubpalettes',
            AddSyndicationTypePaletteSelectorsEvent::class => 'onAddSelector',
        ];
    }

    public static function getSubscribedServices()
    {
        return [
            '?HeimrichHannot\EncoreBundle\Dca\DcaGenerator',
        ];
    }

    public function onAddFields(AddSyndicationTypeFieldsEvent $event)
    {
        if ($this->container->has('HeimrichHannot\EncoreBundle\Dca\DcaGenerator')) {
            $event->addCheckboxField('synPrintUseCustomEncoreEntries', true);
            $event->addField('synPrintCustomEncoreEntries', $this->container->get(DcaGenerator::class)->getEncoreEntriesSelect(false));
        }
    }

    public function onAddSubpalettes(AddSyndicationTypeSubpalettesEvent $event)
    {
        if ($this->container->has('HeimrichHannot\EncoreBundle\Dca\DcaGenerator')) {
            $event->addSubpalettes('synPrintUseCustomEncoreEntries', 'synPrintCustomEncoreEntries');
            $event->addSubpalettes('synUsePrintTemplate', $event->getSubpalettes()['synUsePrintTemplate'].',synPrintUseCustomEncoreEntries');
        }
    }

    public function onAddSelector(AddSyndicationTypePaletteSelectorsEvent $event): void
    {
        if ($this->container->has('HeimrichHannot\EncoreBundle\Dca\DcaGenerator')) {
            $event->addSelector('synPrintUseCustomEncoreEntries');
        }
    }
}
