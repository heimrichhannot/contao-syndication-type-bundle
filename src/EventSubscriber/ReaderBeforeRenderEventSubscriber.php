<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\EventSubscriber;

use HeimrichHannot\ReaderBundle\Event\ReaderBeforeRenderEvent;
use HeimrichHannot\ReaderBundle\Model\ReaderConfigElementModel;
use HeimrichHannot\ReaderBundle\Registry\ReaderConfigElementRegistry;
use HeimrichHannot\SyndicationTypeBundle\ConfigElementType\SyndicationConfigElementType;
use HeimrichHannot\SyndicationTypeBundle\ExportSyndication\ExportSyndicationHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReaderBeforeRenderEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var ExportSyndicationHandler
     */
    protected $exportSyndicationHandler;
    /**
     * @var ReaderConfigElementRegistry
     */
    protected $configElementRegistry;

    /**
     * ReaderBeforeRenderEventSubscriber constructor.
     */
    public function __construct(ExportSyndicationHandler $exportSyndicationHandler, ReaderConfigElementRegistry $configElementRegistry)
    {
        $this->exportSyndicationHandler = $exportSyndicationHandler;
        $this->configElementRegistry = $configElementRegistry;
    }

    public static function getSubscribedEvents()
    {
        return [
            'huh.reader.event.reader_before_render' => 'onReaderBeforeRender',
        ];
    }

    public function onReaderBeforeRender(ReaderBeforeRenderEvent $event): void
    {
        $readerConfigElements = ReaderConfigElementModel::findBy([
            'tl_reader_config_element.pid=?',
            'tl_reader_config_element.type=?',
        ], [
            $event->getReaderConfig()->rootId,
            SyndicationConfigElementType::getType(),
        ]);

        if (!$readerConfigElements) {
            return;
        }

        /** @var SyndicationConfigElementType $configElementType */
        $configElementType = $this->configElementRegistry->getReaderConfigElementType(SyndicationConfigElementType::getType());

        if (!$configElementType) {
            return;
        }

        foreach ($readerConfigElements as $readerConfigElement) {
            $context = $configElementType->getSyndicationContext(
                array_merge($event->getItem()->getRaw(), ['formatted' => $event->getItem()->getFormatted()]),
                $readerConfigElement
            );
            $this->exportSyndicationHandler->exportByContext($context);
        }
    }
}
