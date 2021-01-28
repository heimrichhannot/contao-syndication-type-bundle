<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationLink;

class SyndicationLinkRenderer
{
    /**
     * Render a syndication link element.
     *
     * Options:
     * - attributes: (array) Set addition attributes. Existing attributes will be overridden (array_merge used)
     * - content: (string) Override the default content set in link element.
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

        return sprintf('<a %s>%s</a>',
            $renderedAttributes,
            $content);
    }
}
