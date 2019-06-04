<?php

namespace App\Entity;

use App\Entity\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Statistical
{
    const TYPE_CSSR_ACTIVE = 'cssr_active';
    const TYPE_CSSR_INACTIVE = 'cssr_inactive';
    const TYPE_COURSE_ONLINE = 'course_online';
    const TYPE_COURSE_QUANTITY = 'course_quantity';
    const TYPE_ORDER_CONFIRMED = 'order_confirm';
    const TYPE_ORDER_REGISTERED = 'order_registered';
    const TYPE_ORDER_CANCELLED = 'order_cancelled';

    const TYPES = [
        self::TYPE_CSSR_ACTIVE,
        self::TYPE_CSSR_INACTIVE,
        self::TYPE_COURSE_ONLINE,
        self::TYPE_COURSE_QUANTITY,
        self::TYPE_ORDER_CONFIRMED,
        self::TYPE_ORDER_REGISTERED,
        self::TYPE_ORDER_CANCELLED,
    ];

    use IdentifiableTrait;

    /**
     * @var \DateTime
     * @ORM\Column(type="date")
     */
    protected $date;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $type;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $value;

    /**
     * @return \DateTime
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getValue(): ?int
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function setValue(int $value): void
    {
        $this->value = $value;
    }
}
