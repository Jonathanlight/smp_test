<?php

namespace App\Twig;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ArticleExtension extends AbstractExtension
{
    /**
     * @var ArticleRepository
     */
    protected $articleRepository;

    /**
     * @param ArticleRepository $articleRepository
     */
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getArticles', [$this, 'getArticles']),
        ];
    }

    /**
     * @return Article|null
     */
    public function getArticles(): ?array
    {
        return $this->articleRepository->getLatest();
    }
}
