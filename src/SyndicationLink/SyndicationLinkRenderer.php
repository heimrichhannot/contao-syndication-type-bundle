<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationLink;

use Symfony\Component\HttpKernel\KernelInterface;

class SyndicationLinkRenderer
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * SyndicationLinkRenderer constructor.
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Render a syndication link element.
     *
     * Options:
     * - attributes: (array) Set addition attributes. Existing attributes will be overridden (array_merge used)
     * - content: (string) Override the default content set in link element
     * - disable_dev_comments: (bool) Disable dev html comments in dev mode
     */
    public function render(SyndicationLink $link, array $options = []): string
    {
        $attributes = $link->getAttributes();

        if (isset($options['attributes']) && \is_array($options['attributes'])) {
            $attributes = array_merge($attributes, $options['attributes']);
        }

        $attributes['href'] = $link->getHref();

        $renderedAttributes = '';

        foreach ($attributes as $key => $value) {
            $renderedAttributes .= $key.'="'.$value.'" ';
        }

        $content = isset($options['content']) ? $options['content'] : $link->getContent();

        $result = sprintf('<a %s>%s</a>',
            $renderedAttributes,
            $content);

        if ($this->kernel->isDebug() && (!isset($options['disable_dev_comments']) || false === $options['disable_dev_comments'])) {
            $result = "\n<!-- SYNDICATION LINK -->\n$result\n<!-- END SYNDICATION LINK -->\n";
        }

        return $result;
    }

    /**
     * Render all links in an provider.
     *
     * Options:
     * - rel: (string) Only render links with given relationship
     * - disable_dev_comments: (bool) Disable dev html comments in dev mode
     * - prepend: (string) Will be prepended before the rendered links. Could be for example a headline or an open tag.
     * - append: (string) Will be appended after the rendered links. Could be for example a clearfix or an closing tag.
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
            $result .= $this->render($link, ['disable_dev_comments' => true]);
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

        return $result;
    }
}
