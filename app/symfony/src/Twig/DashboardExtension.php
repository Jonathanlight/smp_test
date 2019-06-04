<?php

namespace App\Twig;

use App\Entity\Statistical;
use App\Manager\DashboardManager;
use App\Manager\StatisticalManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DashboardExtension extends AbstractExtension
{
    /**
     * @var DashboardManager
     */
    protected $dashboardManager;

    /**
     * @var StatisticalManager
     */
    protected $statisticalManager;

    /**
     * @param DashboardManager   $dashboardManager
     * @param StatisticalManager $statisticalManager
     */
    public function __construct(
        DashboardManager $dashboardManager,
        StatisticalManager $statisticalManager
    ) {
        $this->dashboardManager = $dashboardManager;
        $this->statisticalManager = $statisticalManager;
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getStats', [$this, 'getStats']),
        ];
    }

    /**
     * @param string $type
     * @param string $date
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getStats(string $type, ?string $date)
    {
        if (null === $date) {
            switch ($type) {
                case Statistical::TYPE_CSSR_ACTIVE:
                    return $this->dashboardManager->getCountActiveCSSR();
                case Statistical::TYPE_CSSR_INACTIVE:
                    return $this->dashboardManager->getCountInactiveCSSR();
                case Statistical::TYPE_COURSE_ONLINE:
                    return $this->dashboardManager->getCountActiveCourse();
                case Statistical::TYPE_COURSE_QUANTITY:
                    return $this->dashboardManager->getCountAvailableQuantity();
                case Statistical::TYPE_ORDER_CONFIRMED:
                    return $this->dashboardManager->getCountConfirmedOrder();
                case Statistical::TYPE_ORDER_REGISTERED:
                    return $this->dashboardManager->getCountRegisteredOrder();
                case Statistical::TYPE_ORDER_CANCELLED:
                    return $this->dashboardManager->getCountCancelledOrder();
            }
        } else {
            $statistical = $this->statisticalManager->find($type, $date);

            if (!isset($statistical['value'])) {
                return null;
            }

            return $statistical['value'];
        }
    }
}
