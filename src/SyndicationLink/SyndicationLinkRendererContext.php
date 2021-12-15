<?php

namespace HeimrichHannot\SyndicationTypeBundle\SyndicationLink;

class SyndicationLinkRendererContext
{
    const TYPE_CONTENT_ELEMENT = 'content_element';
    const TYPE_ARTICLE = 'article';
    const TYPE_READER_CONFIG_ELEMENT = 'reader_config_element';
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $parentTable;
    /**
     * @var int
     */
    private $parentId;
    /**
     * @var array
     */
    private $context;

    public function __construct(string $type, string $parentTable, int $parentId, array $context = [])
    {
        $this->type = $type;
        $this->parentTable = $parentTable;
        $this->parentId = $parentId;
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getParentTable(): string
    {
        return $this->parentTable;
    }

    /**
     * @return int
     */
    public function getParentId(): int
    {
        return $this->parentId;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }
}