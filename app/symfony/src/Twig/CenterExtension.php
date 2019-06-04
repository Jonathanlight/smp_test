<?php

namespace App\Twig;

use App\Repository\CenterRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CenterExtension extends AbstractExtension
{
    /**
     * @var CenterRepository
     */
    protected $centerRepository;

    /**
     * @param CenterRepository $centerRepository
     */
    public function __construct(
        CenterRepository $centerRepository
    ) {
        $this->centerRepository = $centerRepository;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getCenters', [$this, 'getCenters']),
        ];
    }

    /**
     * @return array
     */
    public function getCenters(): array
    {
        return $this->centerRepository->getEnabled();
    }
}
