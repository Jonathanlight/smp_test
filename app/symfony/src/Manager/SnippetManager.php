<?php

namespace App\Manager;

use App\Entity\Snippet;
use App\Repository\SnippetRepository;

class SnippetManager
{
    /**
     * @var SnippetRepository
     */
    protected $repository;

    /**
     * @param SnippetRepository $snippetRepository
     */
    public function __construct(SnippetRepository $snippetRepository)
    {
        $this->repository = $snippetRepository;
    }

    /**
     * @param string $code
     *
     * @return Snippet
     */
    public function getSnippetByCode(string $code): ?Snippet
    {
        return $this->repository->findOneByCode($code);
    }
}
