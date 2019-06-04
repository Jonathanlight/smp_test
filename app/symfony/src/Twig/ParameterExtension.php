<?php

namespace App\Twig;

use App\Entity\Parameter;
use App\Manager\ParameterManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ParameterExtension extends AbstractExtension
{
    /**
     * @var ParameterManager
     */
    protected $parameterManager;

    /**
     * @param ParameterManager $parameterManager
     */
    public function __construct(ParameterManager $parameterManager)
    {
        $this->parameterManager = $parameterManager;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getParameter', [$this, 'getParameter']),
        ];
    }

    /**
     * @param string $code
     *
     * @return string|null
     */
    public function getParameter(string $code): ?string
    {
        $parameter = $this->parameterManager->getParameterByCode($code);

        if (!$parameter instanceof Parameter) {
            return null;
        }

        return $parameter->getValue();
    }
}
