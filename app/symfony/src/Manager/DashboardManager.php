<?php

namespace App\Manager;

use App\Entity\Order;
use App\Entity\Statistical;
use App\Repository\CenterRepository;
use App\Repository\CourseRepository;
use App\Repository\OrderRepository;

class DashboardManager
{
    /**
     * @var CenterRepository
     */
    protected $centerRepository;

    /**
     * @var CourseRepository
     */
    protected $courseRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var StatisticalManager
     */
    protected $statisticalManager;

    /**
     * @param CenterRepository   $centerRepository
     * @param CourseRepository   $courseRepository
     * @param OrderRepository    $orderRepository
     * @param StatisticalManager $statisticalManager
     */
    public function __construct(
        CenterRepository $centerRepository,
        CourseRepository $courseRepository,
        OrderRepository $orderRepository,
        StatisticalManager $statisticalManager
    ) {
        $this->centerRepository = $centerRepository;
        $this->courseRepository = $courseRepository;
        $this->orderRepository = $orderRepository;
        $this->statisticalManager = $statisticalManager;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function generate(): void
    {
        $this->countActiveCssr();
        $this->countInactiveCssr();
        $this->countActiveCourse();
        $this->countAvailableQuantity();
        $this->countConfirmedOrder();
        $this->countRegisteredOrder();
        $this->countCancelledOrder();
    }

    /**
     * @return int|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountActiveCourse()
    {
        return $this->courseRepository->getCurrentActiveCount();
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function countActiveCourse(): void
    {
        $this->statisticalManager->create(
            Statistical::TYPE_COURSE_ONLINE,
            $this->getCountActiveCourse()
        );
    }

    /**
     * @return int|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountRegisteredOrder()
    {
        return $this->orderRepository->getOrderCountByStatus(Order::STATUS_REGISTERED);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function countRegisteredOrder(): void
    {
        $this->statisticalManager->create(
            Statistical::TYPE_ORDER_REGISTERED,
            $this->getCountRegisteredOrder()
        );
    }

    /**
     * @return int|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountCancelledOrder()
    {
        return $this->orderRepository->getOrderCountByStatus(Order::STATUS_CANCELLED);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function countCancelledOrder(): void
    {
        $this->statisticalManager->create(
            Statistical::TYPE_ORDER_CANCELLED,
            $this->getCountCancelledOrder()
        );
    }

    /**
     * @return int|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountConfirmedOrder()
    {
        return $this->orderRepository->getOrderCountByStatus(Order::STATUS_CONFIRMED);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function countConfirmedOrder(): void
    {
        $this->statisticalManager->create(
            Statistical::TYPE_ORDER_CONFIRMED,
            $this->getCountConfirmedOrder()
        );
    }

    /**
     * @return int|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountAvailableQuantity()
    {
        return $this->courseRepository->getCurrentQuantity() - $this->orderRepository->getCurrentConfirmedCount();
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function countAvailableQuantity(): void
    {
        $this->statisticalManager->create(
            Statistical::TYPE_COURSE_QUANTITY,
            $this->getCountAvailableQuantity()
        );
    }

    /**
     * @return int|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountActiveCSSR()
    {
        return $this->courseRepository->getCurrentActiveCSSR();
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function countActiveCssr(): void
    {
        $this->statisticalManager->create(
            Statistical::TYPE_CSSR_ACTIVE,
            $this->getCountActiveCSSR()
        );
    }

    /**
     * @return int|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountInactiveCSSR()
    {
        return count($this->centerRepository->findAll()) - $this->courseRepository->getCurrentActiveCSSR();
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function countInactiveCssr(): void
    {
        $this->statisticalManager->create(
            Statistical::TYPE_CSSR_INACTIVE,
            $this->getCountInactiveCSSR()
        );
    }
}
