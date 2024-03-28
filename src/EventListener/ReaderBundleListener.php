<?php

namespace HeimrichHannot\SyndicationTypeBundle\EventListener;

use HeimrichHannot\ReaderBundle\Event\ReaderBeforeRenderEvent;
use HeimrichHannot\ReaderBundle\Model\ReaderConfigElementModel;
use HeimrichHannot\ReaderBundle\Registry\ReaderConfigElementRegistry;
use HeimrichHannot\SyndicationTypeBundle\ConfigElementType\SyndicationConfigElementType;
use HeimrichHannot\SyndicationTypeBundle\SyndicationType\ExportSyndicationHandler;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class ReaderBundleListener implements ServiceSubscriberInterface
{
    public function __construct(
        private ContainerInterface $container,
        private ExportSyndicationHandler $exportSyndicationHandler,
    )
    {
    }

    #[AsEventListener('huh.reader.event.reader_before_render')]
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

    public static function getSubscribedServices(): array
    {
        return [
            '?HeimrichHannot\ReaderBundle\Registry\ReaderConfigElementRegistry',
        ];
    }
}