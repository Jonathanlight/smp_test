<?php

namespace App\Manager;

use App\Entity\Page;
use App\Repository\PageRepository;

class PageManager
{
    /**
     * @var PageRepository
     */
    protected $repository;

    /**
     * @param PageRepository $pageRepository
     */
    public function __construct(PageRepository $pageRepository)
    {
        $this->repository = $pageRepository;
    }

    /**
     * @param string $code
     *
     * @return Page
     */
    public function getPageByCode(string $code): ?Page
    {
        return $this->repository->findOneByCode($code);
    }

    /**
     * @return array
     */
    public function collect()
    {
        return $this->repository->collect();
    }

    /**
     * @param string $code
     *
     * @return Page
     */
    public function getContentBlockByPageCode(string $code): ?Page
    {
        return $this->repository->getContentBlockByPageCode($code);
    }
}
