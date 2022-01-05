<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationLink;

use HeimrichHannot\SyndicationTypeBundle\Event\BeforeRenderSyndicationLinksEvent;
use HeimrichHannot\TwigSupportBundle\Exception\TemplateNotFoundException;
use HeimrichHannot\TwigSupportBundle\Filesystem\TwigTemplateLocator;
use HeimrichHannot\TwigSupportBundle\Renderer\TwigTemplateRenderer;
use HeimrichHannot\TwigSupportBundle\Renderer\TwigTemplateRendererConfiguration;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class SyndicationLinkRenderer
{
    /**
     * @var KernelInterface
     */
    protected $kernel;
    /**
     * @var TwigTemplateLocator
     */
    protected $twigTemplateLocator;
    /**
     * @var TwigTemplateRenderer
     */
    protected $twigTemplateRenderer;
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * SyndicationLinkRenderer constructor.
     */
    public function __construct(KernelInterface $kernel, TwigTemplateLocator $twigTemplateLocator, TwigTemplateRenderer $twigTemplateRenderer, EventDispatcherInterface $eventDispatcher)
    {
        $this->kernel = $kernel;
        $this->twigTemplateLocator = $twigTemplateLocator;
        $this->twigTemplateRenderer = $twigTemplateRenderer;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Render all links in an provider.
     *
     * Options:
     * - template: (string) The template to render the link provider. Default: syndication_provider_default
     * - rel: (string) Only render links with given relationship
     * - disable_dev_comments: (bool) Disable dev html comments in dev mode. Default: false
     * - prepend: (string) Will be prepended before the rendered links. Could be for example a headline or an open tag.
     * - append: (string) Will be appended after the rendered links. Could be for example a clearfix or an closing tag.
     * - linkTemplate: (string) The name of a twig templates that renders a single link. Default: syndication_link_default
     * - render_callback: (callable) A custom callback to render a single link instance. Default null
     */
    public function renderProvider(SyndicationLinkProvider $provider, SyndicationLinkRendererContext $context, array $options = []): string
    {
        $defaults = [
            'render_callback' => null,
            'prepend' => '',
            'append' => '',
            'template' => 'syndication_provider_default',
            'disable_dev_comments' => false,
        ];
        $options = array_merge($defaults, $options);

        $result = '';

        if (isset($options['rel'])) {
            $links = $provider->getLinksByRel($options['rel']);
        } else {
            $links = $provider->getLinks();
        }

        $linkRenderOptions = array_intersect_key($options, ['disable_dev_comments' => false, 'linkTemplate' => null]);

        if (isset($linkRenderOptions['linkTemplate'])) {
            $linkRenderOptions['template'] = $linkRenderOptions['linkTemplate'];
            unset($linkRenderOptions['linkTemplate']);
        }

        /** @var BeforeRenderSyndicationLinksEvent $event */
        $event = $this->eventDispatcher->dispatch(BeforeRenderSyndicationLinksEvent::class, new BeforeRenderSyndicationLinksEvent($links, $provider, $linkRenderOptions, $options, $context));

        try {
            $template = $this->twigTemplateLocator->getTemplatePath($options['template']);
        } catch (TemplateNotFoundException $e) {
            if ($this->kernel->isDebug()) {
                throw $e;
            }

            if ($options['template'] !== $defaults['template']) {
                trigger_error($e->getMessage(), E_USER_WARNING);
                $template = $this->twigTemplateLocator->getTemplatePath($defaults['template']);
            } else {
                throw $e;
            }
        }

        $renderedLinks = [];

        /** @var SyndicationLink $link */
        foreach ($event->getLinks() as $link) {
            if (\is_callable($options['render_callback'])) {
                $renderedLinks[$link->getType()] = \call_user_func($options['render_callback'], $link, $event->getLinkRenderOptions());
            } else {
                $renderedLinks[$link->getType()] = $this->render($link, $event->getLinkRenderOptions());
            }
        }

        $result = $this->twigTemplateRenderer->render($template, [
            'links' => $renderedLinks,
            'prepend' => $options['prepend'],
            'append' => $options['append'],
        ], (new TwigTemplateRendererConfiguration())->setShowTemplateComments(!$options['disable_dev_comments']));

        return $result;
    }

    /**
     * Render a syndication link element.
     *
     * Options:
     * - attributes: (array) Set addition attributes. Existing attributes will be overridden (array_merge used)
     * - content: (string) Override the default content set in link element
     * - disable_dev_comments: (bool) Disable dev html comments in dev mode
     * - template: (string) The name on a twig templates that renders a single link. Default: syndication_link_default
     */
    public function render(SyndicationLink $link, array $options = []): string
    {
        $defaults = [
            'disable_dev_comments' => false,
            'template' => 'syndication_link_default',
        ];
        $options = array_merge($defaults, $options);

        $attributes = $link->getAttributes();

        if (isset($options['attributes']) && \is_array($options['attributes'])) {
            $attributes = array_merge($attributes, $options['attributes']);
        }

        $attributes['href'] = $link->getHref();
        $attributes['rel'] = array_unique($link->getRels());

        $attributes['class'] = trim('syndication-link '.($attributes['class'] ?: ''));

        $renderedAttributes = '';

        foreach ($attributes as $key => $value) {
            $renderedAttributes .= $key.'="'.$value.'" ';
        }

        try {
            $template = $this->twigTemplateLocator->getTemplatePath($options['template']);
        } catch (TemplateNotFoundException $e) {
            if ($this->kernel->isDebug()) {
                throw $e;
            }

            if ($options['template'] !== $defaults['template']) {
                trigger_error($e->getMessage(), E_USER_WARNING);
                $template = $this->twigTemplateLocator->getTemplatePath($defaults['template']);
            } else {
                throw $e;
            }
        }

        $content = isset($options['content']) ? $options['content'] : $link->getContent();

        $result = $this->twigTemplateRenderer->render($template, [
            'attributes' => $attributes,
            'renderedAttributes' => $renderedAttributes,
            'content' => $content,
            'link' => $link,
        ], (new TwigTemplateRendererConfiguration())
            ->setShowTemplateComments(!$options['disable_dev_comments'])
            ->setTemplatePath($template)
        );

        return $result;
    }
}
