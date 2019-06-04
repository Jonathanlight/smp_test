<?php

namespace App\Twig;

use App\Entity\Snippet;
use App\Manager\SnippetManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SnippetExtension extends AbstractExtension
{
    /**
     * @var SnippetManager
     */
    protected $snippetManager;

    /**
     * @param SnippetManager $snippetManager
     */
    public function __construct(SnippetManager $snippetManager)
    {
        $this->snippetManager = $snippetManager;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('getSnippet', [$this, 'getSnippet']),
        ];
    }

    /**
     * @param string $code
     *
     * @return Snippet
     */
    public function getSnippet(string $code): ?Snippet
    {
        return $this->snippetManager->getSnippetByCode($code);
    }
}
