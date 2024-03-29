<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class AddSyndicationTypePaletteSelectorsEvent extends Event
{
    /**
     * @var array
     */
    protected $selectors = [];

    public function addSelector(string $selector): void
    {
        $this->selectors[] = $selector;
    }

    public function getSelectors(): array
    {
        return $this->selectors;
    }
}
