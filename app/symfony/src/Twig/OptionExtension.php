<?php

namespace App\Twig;

use App\Entity\Option;
use App\Manager\OptionManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OptionExtension extends AbstractExtension
{
    /**
     * @var OptionManager
     */
    protected $optionManager;

    /**
     * @param OptionManager $optionManager
     */
    public function __construct(OptionManager $optionManager)
    {
        $this->optionManager = $optionManager;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getOption', [$this, 'getOption']),
        ];
    }

    /**
     * @param string $code
     *
     * @return float
     */
    public function getOption(string $code): ?float
    {
        $option = $this->optionManager->getOptionByCode($code);

        if (!$option instanceof Option) {
            return null;
        }

        return number_format($option->getAmount(), 2);
    }
}
