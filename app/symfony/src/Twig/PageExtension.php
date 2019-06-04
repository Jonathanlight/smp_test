<?php

namespace App\Twig;

use App\Entity\Page;
use App\Manager\PageManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PageExtension extends AbstractExtension
{
    /**
     * @var PageManager
     */
    protected $pageManager;

    /**
     * @param PageManager $pageManager
     */
    public function __construct(PageManager $pageManager)
    {
        $this->pageManager = $pageManager;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('getPage', [$this, 'getPage']),
        ];
    }

    /**
     * @param string $code
     *
     * @return Page
     */
    public function getPage(string $code): ?Page
    {
        return $this->pageManager->getPageByCode($code);
    }
}
