<?php

namespace App\Entity;

use App\Entity\Traits\CodifiableTrait;
use App\Entity\Traits\IdentifiableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TreeRepository")
 */
class Tree
{
    use IdentifiableTrait;
    use CodifiableTrait;

    const CODE_HEADER = 'header';
    const CODE_FOOTER = 'footer';

    /**
     * @var Node[]
     * @ORM\OneToMany(targetEntity=Node::class, mappedBy="tree", cascade={"remove"})
     */
    protected $nodes;

    public function __construct()
    {
        $this->nodes = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getNodes(): Collection
    {
        return $this->nodes;
    }
}
