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
     * @var string[]
     */
    protected $exportTypes = [];
    /**
     * @var array
     */
    protected $categories;

    public function addType(SyndicationTypeInterface $type): void
    {
        $this->types[$type::getType()] = $type;

        if ($type instanceof ExportSyndicationTypeInterface) {
            $this->exportTypes[] = $type::getType();
        }
    }

    /**
     * @return SyndicationTypeInterface[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    public function hasType(string $type): bool
    {
        return isset($this->types[$type]);
    }

    public function getType(string $type): ?SyndicationTypeInterface
    {
        if ($this->hasType($type)) {
            return $this->types[$type];
        }

        return null;
    }

    /**
     * Return types by given category.
     *
     * @return SyndicationTypeInterface[]
     */
    public function getTypesByCategory(string $category): array
    {
        if (!$this->categories) {
            $this->categories = [];
            $types = $this->types;

            foreach ($types as $name => $type) {
                if (!isset($this->categories[$type->getCategory()])) {
                    $this->categories[$type->getCategory()] = [];
                }
                $this->categories[$type->getCategory()][] = $type;
            }
        }

        return isset($this->categories[$category]) ? $this->categories[$category] : [];
    }

    /**
     * Return all category types with existing syndication types.
     *
     * @return string[]
     */
    public function getCategories(): array
    {
        $this->getTypesByCategory(AbstractSyndicationType::CATEGORY_SHARE);
        $categories = array_keys($this->categories);
        asort($categories);

        return $categories;
    }

    public function isExportType(string $type): bool
    {
        if ($this->getType($type) && ($this->getType($type) instanceof ExportSyndicationTypeInterface)) {
            return true;
        }

        return false;
    }
}
