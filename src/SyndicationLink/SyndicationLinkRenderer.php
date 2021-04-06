<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationLink;

use HeimrichHannot\TwigSupportBundle\Exception\TemplateNotFoundException;
use HeimrichHannot\TwigSupportBundle\Filesystem\TwigTemplateLocator;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;

class SyndicationLinkRenderer
{
    /**
     * @var KernelInterface
     */
    protected $kernel;
    /**
     * @var Environment
     */
    protected $twig;
    /**
     * @var TwigTemplateLocator
     */
    protected $twigTemplateLocator;

    /**
     * SyndicationLinkRenderer constructor.
     */
    public function __construct(KernelInterface $kernel, Environment $twig, TwigTemplateLocator $twigTemplateLocator)
    {
        $this->kernel = $kernel;
        $this->twig = $twig;
        $this->twigTemplateLocator = $twigTemplateLocator;
    }

    /**
     * Render all links in an provider.
     *
     * Options:
     * - rel: (string) Only render links with given relationship
     * - disable_dev_comments: (bool) Disable dev html comments in dev mode
     * - prepend: (string) Will be prepended before the rendered links. Could be for example a headline or an open tag.
     * - append: (string) Will be appended after the rendered links. Could be for example a clearfix or an closing tag.
     * - disable_indexer_comments: (bool) Disable indexer::stop and indexer::continue comments
     * - linkTemplate: (string) The name on an twig templates that renders a single link. Default: syndication_link_default
     */
    public function renderProvider(SyndicationLinkProvider $provider, array $options = []): string
    {
        $result = '';

        if (isset($options['rel'])) {
            $links = $provider->getLinksByRel($options['rel']);
        } else {
            $links = $provider->getLinks();
        }

        foreach ($links as $link) {
            $result .= $this->render($link, [
                'disable_dev_comments' => true,
                'template' => $options['linkTemplate'] ?? null,
            ]);
        }

        if (isset($options['prepend']) && \is_string($options['prepend'])) {
            $result = $options['prepend'].$result;
        }

        if (isset($options['append']) && \is_string($options['append'])) {
            $result = $result.$options['append'];
        }

        if ($this->kernel->isDebug() && (!isset($options['disable_dev_comments']) || false === $options['disable_dev_comments'])) {
            $result = "\n<!-- SYNDICATION LINKS -->\n$result\n<!-- END SYNDICATION LINKS -->\n";
        }

        if (!isset($options['disable_indexer_comments']) || true !== $options['disable_indexer_comments']) {
            $result = "\n<!-- indexer::stop -->\n".$result."\n<!-- indexer::continue -->\n";
        }

        return $result;
    }

    /**
     * Render a syndication link element.
     *
     * Options:
     * - attributes: (array) Set addition attributes. Existing attributes will be overridden (array_merge used)
     * - content: (string) Override the default content set in link element
     * - disable_dev_comments: (bool) Disable dev html comments in dev mode
     * - template: (string) The name on an twig templates that renders a single link. Default: syndication_link_default
     */
    public function render(SyndicationLink $link, array $options = []): string
    {
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

        $template = $this->twigTemplateLocator->getTemplatePath('syndication_link_default');

        if (isset($options['template']) && \is_string($options['template'])) {
            try {
                $template = $this->twigTemplateLocator->getTemplatePath($options['template']);
            } catch (TemplateNotFoundException $e) {
                if ($this->kernel->isDebug()) {
                    throw $e;
                }
                trigger_error($e->getMessage(), E_USER_WARNING);
            }
        }

        $content = isset($options['content']) ? $options['content'] : $link->getContent();

        $result = $this->twig->render($template, [
            'attributes' => $attributes,
            'renderedAttributes' => $renderedAttributes,
            'content' => $content,
            'link' => $link,
        ]);

        if ($this->kernel->isDebug() && (!isset($options['disable_dev_comments']) || false === $options['disable_dev_comments'])) {
            $result = "\n<!-- SYNDICATION LINK -->\n$result\n<!-- END SYNDICATION LINK -->\n";
        }

        return $result;
    }
}
