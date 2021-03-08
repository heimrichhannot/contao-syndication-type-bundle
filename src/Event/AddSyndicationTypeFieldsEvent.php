<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\Event;

use HeimrichHannot\SyndicationTypeBundle\Dca\SyndicationTypeDcaProvider;
use Symfony\Component\EventDispatcher\Event;

class AddSyndicationTypeFieldsEvent extends Event
{
    protected $fields = [];
    /**
     * @var SyndicationTypeDcaProvider
     */
    protected $dcaProvider;

    /**
     * AddSyndicationTypeFieldsEvent constructor.
     */
    public function __construct(array $fields, SyndicationTypeDcaProvider $dcaProvider)
    {
        $this->fields = $fields;
        $this->dcaProvider = $dcaProvider;
    }

    public function addField(string $fieldName, array $field): void
    {
        $this->fields[$fieldName] = $field;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function addSelectField(string $fieldName, array $config): void
    {
        $this->dcaProvider->addSelectField($fieldName, $this->fields, $config);
    }

    public function addCheckboxField(string $fieldName, bool $submitOnChange = false): void
    {
        $this->dcaProvider->addCheckboxField($fieldName, $this->fields, $submitOnChange);
    }

    public function addTemplateSelectField(string $fieldName, string $templateGroup): void
    {
        $this->dcaProvider->addTemplateSelectField($fieldName, $this->fields, $templateGroup);
    }

    public function addFieldSelectField(string $fieldName): void
    {
        $this->dcaProvider->addFieldSelectField($fieldName, $this->fields);
    }
}
