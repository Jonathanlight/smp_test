<?php

namespace App\Services;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class PaginatorService
{
    const DEFAULT_LIMIT = 20;
    const DEFAULT_PAGE = 1;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @param PaginatorInterface $paginator
     */
    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @param array $results
     * @param int   $limit
     * @param int   $page
     *
     * @return PaginationInterface
     */
    public function paginate(array $results, int $limit, int $page): PaginationInterface
    {
        return $this->paginator->paginate($results, $page, $limit);
    }
}
