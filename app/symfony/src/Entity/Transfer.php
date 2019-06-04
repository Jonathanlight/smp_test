<?php

namespace App\Entity;

use App\Entity\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Transfer
{
    use IdentifiableTrait;

    /**
     * @var Course
     * @ORM\ManyToOne(targetEntity=Course::class)
     */
    protected $newCourse;

    /**
     * @var Order
     * @ORM\ManyToOne(targetEntity=Order::class)
     */
    protected $order;

    /**
     * @var Course
     * @ORM\ManyToOne(targetEntity=Course::class)
     */
    protected $previousCourse;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $transferedAt;

    /**
     * @return Course
     */
    public function getNewCourse(): Course
    {
        return $this->newCourse;
    }

    /**
     * @param Course $newCourse
     */
    public function setNewCourse(Course $newCourse): void
    {
        $this->newCourse = $newCourse;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @return Course
     */
    public function getPreviousCourse(): Course
    {
        return $this->previousCourse;
    }

    /**
     * @param Course $previousCourse
     */
    public function setPreviousCourse(Course $previousCourse): void
    {
        $this->previousCourse = $previousCourse;
    }

    /**
     * @return \DateTime
     */
    public function getTransferedAt(): ?\DateTime
    {
        return $this->transferedAt;
    }

    /**
     * @param \DateTime $transferedAt
     */
    public function setTransferedAt(\DateTime $transferedAt): void
    {
        $this->transferedAt = $transferedAt;
    }
}
