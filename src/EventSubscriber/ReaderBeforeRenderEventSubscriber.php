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
use HeimrichHannot\SyndicationTypeBundle\SyndicationType\ExportSyndicationHandler;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReaderBeforeRenderEventSubscriber extends AbstractServiceSubscriber implements EventSubscriberInterface
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
     * @var ContainerInterface
     */
    protected $container;

    /**
     * ReaderBeforeRenderEventSubscriber constructor.
     */
    public function __construct(ContainerInterface $container, ExportSyndicationHandler $exportSyndicationHandler)
    {
        $this->exportSyndicationHandler = $exportSyndicationHandler;
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return [
            'huh.reader.event.reader_before_render' => 'onReaderBeforeRender',
        ];
    }

    public function onReaderBeforeRender(ReaderBeforeRenderEvent $event): void
    {
        if (!$this->container->has('HeimrichHannot\ReaderBundle\Registry\ReaderConfigElementRegistry')) {
            return;
        }

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
        $configElementType = $this->container->get(ReaderConfigElementRegistry::class)->getReaderConfigElementType(SyndicationConfigElementType::getType());

        if (!$configElementType) {
            return;
        }

        foreach ($readerConfigElements as $readerConfigElement) {
            $context = $configElementType->getSyndicationContext(
                $event->getTemplateData(),
                $readerConfigElement
            );
            $this->exportSyndicationHandler->exportByContext($context);
        }
    }

    public static function getSubscribedServices()
    {
        return [
            '?HeimrichHannot\ReaderBundle\Registry\ReaderConfigElementRegistry',
        ];
    }
}
