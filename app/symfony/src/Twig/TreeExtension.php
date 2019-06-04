<?php

namespace App\Twig;

use App\Entity\Tree;
use App\Repository\TreeRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TreeExtension extends AbstractExtension
{
    /**
     * @var TreeRepository
     */
    protected $treeRepository;

    /**
     * @param TreeRepository $treeRepository
     */
    public function __construct(TreeRepository $treeRepository)
    {
        $this->treeRepository = $treeRepository;
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getMenu', [$this, 'getMenu']),
        ];
    }

    /**
     * @param string $code
     *
     * @return Tree|null
     */
    public function getMenu(string $code): ?Tree
    {
        return $this->treeRepository->getTreeByCode($code);
    }
}
