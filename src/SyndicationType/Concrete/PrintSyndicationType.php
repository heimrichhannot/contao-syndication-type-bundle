<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationType\Concrete;

use Contao\Config;
use Contao\CoreBundle\Exception\ResponseException;
use Contao\Environment;
use Contao\StringUtil;
use HeimrichHannot\EncoreBundle\Asset\EntrypointCollectionFactory;
use HeimrichHannot\EncoreBundle\Asset\TemplateAssetGenerator;
use HeimrichHannot\SyndicationTypeBundle\SyndicationContext\SyndicationContext;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLink;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkFactory;
use HeimrichHannot\SyndicationTypeBundle\SyndicationType\AbstractExportSyndicationType;
use HeimrichHannot\TwigSupportBundle\Template\TwigFrontendTemplate;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PrintSyndicationType extends AbstractExportSyndicationType implements ServiceSubscriberInterface
{
    const PARAM = 'synPrint';
    const PARAM_DEBUG = 'synPrintDebug';

    /**
     * @var SyndicationLinkFactory
     */
    protected $linkFactory;
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    /**
     * @var RequestStack
     */
    protected $requestStack;
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container, SyndicationLinkFactory $linkFactory, TranslatorInterface $translator, RequestStack $requestStack)
    {
        $this->linkFactory = $linkFactory;
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->container = $container;
    }

    public static function getType(): string
    {
        return 'print';
    }

    public function getPalette(): string
    {
        return 'synUsePrintTemplate';
    }

    public function generate(SyndicationContext $context): SyndicationLink
    {
        $attributes = [
            'class' => '',
        ];
        $href = 'javascript:void(0);';

        if ($context->getConfiguration()['synUsePrintTemplate']) {
            $attributes['target'] = '_blank';
            $href = $this->appendGetParameterToUrl($context->getUrl(), static::PARAM, (string) $context->getData()['id']);
        } else {
            $attributes['onclick'] = 'window.print();return false;';
        }

        return $this->linkFactory->create(
            [static::REL_ALTERNATE, static::REL_NOFOLLOW],
            $href,
            $this->translator->trans('huh.syndication_type.types.print.title'),
            $attributes,
            $this
        );
    }

    public function shouldExport(SyndicationContext $context): bool
    {
        return $context->getData()['id'] == $this->requestStack->getMasterRequest()->get(static::PARAM);
    }

    public function export(SyndicationContext $context): void
    {
        $data = $context->getData();

        $data['isRTL'] = 'rtl' === $GLOBALS['TL_LANG']['MSC']['textDirection'];
        $data['language'] = $GLOBALS['TL_LANGUAGE'];
        $data['charset'] = Config::get('characterSet');
        $data['base'] = Environment::get('base');
        $data['onload'] = sprintf(
            'window.print();%s',
            (bool) $this->requestStack->getMasterRequest()->get(static::PARAM_DEBUG) ? '' : 'setTimeout(window.close, 0);'
        );
        $data['title'] = $data['title'] ?: $context->getTitle();
        $data['content'] = $data['content'] ?: $context->getContent();

        $template = new TwigFrontendTemplate($context->getConfiguration()['synPrintTemplate']);
        $template->setData($data);
        $template->isSyndicationExportTemplate = true;

        if ($this->container->has('HeimrichHannot\EncoreBundle\Asset\EntrypointCollectionFactory')) {
            $useEncore = (bool) $context->getConfiguration()['synPrintUseCustomEncoreEntries'] ?? false;

            if ($useEncore && !empty(($entrypoints = array_filter(StringUtil::deserialize($context->getConfiguration()['synPrintCustomEncoreEntries'], true))))) {
                $collection = $this->container->get(EntrypointCollectionFactory::class)->createCollection($entrypoints);
                $template->stylesheets = $this->container->get(TemplateAssetGenerator::class)->linkTags($collection);
                $template->headJavaScript = $this->container->get(TemplateAssetGenerator::class)->headScriptTags($collection);
                $template->javaScript = $this->container->get(TemplateAssetGenerator::class)->scriptTags($collection);
            }
        }

        throw new ResponseException($template->getResponse());
    }

    public static function getSubscribedServices()
    {
        return [
            '?HeimrichHannot\EncoreBundle\Asset\EntrypointCollectionFactory',
            '?HeimrichHannot\EncoreBundle\Asset\TemplateAssetGenerator',
        ];
    }
}
