<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationType;

use HeimrichHannot\SyndicationTypeBundle\SyndicationContext\SyndicationContext;
use function Symfony\Component\String\u;

abstract class AbstractSyndicationType implements SyndicationTypeInterface
{
    const CATEGORY_SHARE = 'share';
    const CATEGORY_EXPORT = 'export';

    const REL_ALTERNATE = 'alternate';
    const REL_EXTERNAL = 'external';
    const REL_NOFOLLOW = 'nofollow';

    public static function getActivationField(): string
    {
        return u('syn add '.static::getType())->camel();
    }

    public function isEnabledByContext(SyndicationContext $context): bool
    {
        return true === (bool) $context->getConfiguration()[$this->getActivationField()];
    }

    public function getCategory(): string
    {
        return static::CATEGORY_SHARE;
    }

    public function getPalette(): string
    {
        return '';
    }

    protected function getValueByFieldOption(SyndicationContext $context, string $option, ?string $defaultValue = null): ?string
    {
        if (!isset($context->getConfiguration()[$option]) || empty($context->getConfiguration()[$option])) {
            return $defaultValue;
        }

        return $context->getData()[$context->getConfiguration()[$option]] ?? $defaultValue;
    }

    protected function appendGetParameterToUrl(string $url, string $parameter, string $value): string
    {
        $query = parse_url($url, PHP_URL_QUERY);

        if ($query) {
            $url .= '&'.$parameter.'='.$value;
        } else {
            $url .= '?'.$parameter.'='.$value;
        }

        return $url;
    }
}
