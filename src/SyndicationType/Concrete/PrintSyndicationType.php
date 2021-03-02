<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationType\Concrete;

use Contao\Config;
use Contao\Environment;
use Contao\FrontendTemplate;
use HeimrichHannot\SyndicationTypeBundle\SyndicationContext\SyndicationContext;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLink;
use HeimrichHannot\SyndicationTypeBundle\SyndicationLink\SyndicationLinkFactory;
use HeimrichHannot\SyndicationTypeBundle\SyndicationType\AbstractSyndicationType;
use HeimrichHannot\SyndicationTypeBundle\SyndicationType\ExportSyndicationTypeInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class PrintSyndicationType extends AbstractSyndicationType implements ExportSyndicationTypeInterface
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

    public function __construct(SyndicationLinkFactory $linkFactory, TranslatorInterface $translator, RequestStack $requestStack)
    {
        $this->linkFactory = $linkFactory;
        $this->translator = $translator;
        $this->requestStack = $requestStack;
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
            'class' => 'print',
        ];
        $href = '#';

        if ($context->getConfiguration()['synUsePrintTemplate']) {
            $attributes['target'] = '_blank';
            $href = $this->appendGetParameterToUrl($context->getUrl(), static::PARAM, (string) $context->getConfiguration()['id']);
        } else {
            $attributes['onclick'] = 'window.print();return false';
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
        $data['title'] = $context->getTitle();

        $template = new FrontendTemplate($context->getConfiguration()['synPrintTemplate']);
        $template->setData($data);

        echo 'Print';
    }
}
