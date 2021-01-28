<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationType;

class SyndicationTypeCollection
{
    /**
     * @var iterable|SyndicationTypeInterface[]
     */
    protected $typesIterable;
    /**
     * @var SyndicationTypeInterface[]
     */
    protected $types;

    /**
     * SyndicationTypeCollection constructor.
     */
    public function __construct(iterable $types)
    {
        $this->typesIterable = $types;
    }

    /**
     * @return SyndicationTypeInterface[]
     */
    public function getTypes(): array
    {
        if (!$this->types) {
            foreach ($this->typesIterable as $type) {
                $this->types[$type::getType()] = $type;
            }
        }

        return $this->types;
    }

    public function hasType(string $type): bool
    {
        return isset($this->getTypes()[$type]);
    }

    public function getType(string $type): ?SyndicationTypeInterface
    {
        if ($this->hasType($type)) {
            return $this->getTypes()[$type];
        }

        return null;
    }
}
