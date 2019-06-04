<?php

namespace App\Twig;

use App\Entity\Center;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AdminExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('filter_admin_actions', [$this, 'filterActions']),
        ];
    }

    /**
     * @param array $actions
     * @param mixed $item
     *
     * @return array
     */
    public function filterActions(array $actions, $item): array
    {
        if ($item instanceof Center && $item->getCourses()->count() > 0) {
            unset($actions['delete']);
        }

        return $actions;
    }
}
