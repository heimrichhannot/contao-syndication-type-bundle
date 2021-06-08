<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\Dca;

use HeimrichHannot\SyndicationTypeBundle\EventListener\Dca\FieldOptionsCallbackListener;
use Symfony\Component\Translation\TranslatorInterface;

class ConfigElementTypeDcaProvider extends AbstractDcaProvider
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * ConfigElementTypeDcaProvider constructor.
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFields(): array
    {
        $fields = [];

        $fields['synTitleField'] = [
            'label' => $this->getLabel('synTitleField'),
            'inputType' => 'select',
            'options_callback' => [FieldOptionsCallbackListener::class, '__invoke'],
            'exclude' => true,
            'eval' => ['includeBlankOption' => true, 'mandatory' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ];
        $fields['synContentField'] = [
            'label' => $this->getLabel('synContentField'),
            'inputType' => 'select',
            'options_callback' => [FieldOptionsCallbackListener::class, '__invoke'],
            'exclude' => true,
            'eval' => ['includeBlankOption' => true, 'mandatory' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ];

        return $fields;
    }

    public function getSubpalettes(): array
    {
        return [];
    }

    public function getPalettesSelectors(): array
    {
        return [];
    }
}
