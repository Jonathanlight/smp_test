<?php

namespace App\Entity;

use App\Entity\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Node
{
    use IdentifiableTrait;

    /**
     * @var Tree
     * @ORM\ManyToOne(targetEntity=Tree::class, inversedBy="nodes")
     */
    protected $tree;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $url;

    /**
     * @var Page
     * @ORM\ManyToOne(targetEntity=Page::class)
     */
    protected $page;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return Page
     */
    public function getPage(): ?Page
    {
        return $this->page;
    }

    /**
     * @param Page $page
     */
    public function setPage(Page $page): void
    {
        $this->page = $page;
    }

    /**
     * @return Tree
     */
    public function getTree(): ?Tree
    {
        return $this->tree;
    }

    /**
     * @param Tree $tree
     */
    public function setTree(Tree $tree): void
    {
        $this->tree = $tree;
    }
}
