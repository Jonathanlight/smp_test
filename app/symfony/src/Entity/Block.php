<?php

namespace App\Entity;

use App\Entity\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Block
{
    use IdentifiableTrait;

    /**
     * @var Page
     * @ORM\ManyToOne(targetEntity=Page::class, inversedBy="blocks")
     */
    protected $page;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->title;
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
     * @return string
     */
    public function getTitle(): ?string
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
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
