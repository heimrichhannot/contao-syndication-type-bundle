<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class AddSyndicationTypeFieldsEvent extends Event
{
    protected $fields = [];

    public function addField(string $fieldName, array $field): void
    {
        $this->fields[$fieldName] = $field;
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}
