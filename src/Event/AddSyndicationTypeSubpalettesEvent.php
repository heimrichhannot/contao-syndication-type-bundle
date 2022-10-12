<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class AddSyndicationTypeSubpalettesEvent extends Event
{
    /**
     * @var array
     */
    protected $subpalettes;

    /**
     * AddSyndicationTypeSubpalettesEvent constructor.
     */
    public function __construct(array $subpalettes)
    {
        $this->subpalettes = $subpalettes;
    }

    public function getSubpalettes(): array
    {
        return $this->subpalettes;
    }

    public function addSubpalettes(string $name, string $palette): void
    {
        $this->subpalettes[$name] = $palette;
    }
}
