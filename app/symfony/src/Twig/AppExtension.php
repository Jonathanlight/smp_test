<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('bind', [$this, 'bind']),
            new TwigFilter('shuffle', [$this, 'shuffle']),
            new TwigFilter('encodeXls', [$this, 'encodeXls']),
            new TwigFilter('sum', [$this, 'sum']),
        ];
    }

    /**
     * @param string $str
     * @param array  $bindings
     *
     * @return string
     */
    public function bind(string $str, array $bindings): string
    {
        return \str_replace(
            array_map(function ($binding) {
                return '%'.$binding.'%';
            }, array_keys($bindings)),
            array_values($bindings),
            $str
        );

        return $str;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function shuffle(array $data): array
    {
        shuffle($data);

        return $data;
    }

    /**
     * @param array  $data
     * @param string $property
     *
     * @return float
     */
    public function sum($data, string $property): float
    {
        $sum = 0;
        $method = 'get'.ucfirst($property);

        foreach ($data as $item) {
            if (method_exists($item, $method)) {
                $sum += call_user_func([$item, $method]);
            }
        }

        return $sum;
    }

    /**
     * @param string $str
     *
     * @return string
     */
    public function encodeXls(string $str): string
    {
        return \mb_convert_encoding($str, 'windows-1252', 'utf-8');
    }
}
