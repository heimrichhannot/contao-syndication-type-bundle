<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\Dca;

abstract class AbstractDcaProvider
{
    public function prepareDca(string $table): void
    {
        $dca = &$GLOBALS['TL_DCA'][$table];

        $dca['fields'] = array_merge($dca['fields'] ?: [], $this->getFields());
        $dca['subpalettes'] = array_merge($dca['subpalettes'] ?: [], $this->getSubpalettes());
        $dca['palettes']['__selector__'] = array_merge($dca['palettes']['__selector__'] ?: [], $this->getPalettesSelectors());
    }

    /**
     * Return the palettes fields.
     *
     * @return array[]
     */
    abstract public function getFields(): array;

    /**
     * Return subpalettes.
     *
     * @return string[]
     */
    abstract public function getSubpalettes(): array;

    /**
     * Return palette selectors.
     *
     * @return string[]
     */
    abstract public function getPalettesSelectors(): array;

    public function getLabel(string $field): array
    {
        return [
            $this->translator->trans('huh.syndication_type.fields.'.$field.'.name'),
            $this->translator->trans('huh.syndication_type.fields.'.$field.'.description'),
        ];
    }
}
