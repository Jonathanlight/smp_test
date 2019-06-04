<?php

namespace App\Entity;

use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\GeolocableTrait;
use App\Entity\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity()
 */
class Place
{
    use IdentifiableTrait;
    use GeolocableTrait;
    use DeletableTrait;

    const EQUIPMENT_ACCESS_DISABLED = 'access_disabled';
    const EQUIPMENT_ACCESS_TRANSPORT = 'access_transport';
    const EQUIPMENT_PARKING = 'parking';
    const EQUIPMENT_PARKING_FREE = 'parking_free';
    const EQUIPMENT_RESTAURANT = 'restaurant';
    const EQUIPMENT_RESTAURANT_NEARBY = 'restaurant_nearby';

    /**
     * @var Center
     * @ORM\ManyToOne(targetEntity=Center::class)
     */
    protected $center;

    /**
     * @var array
     * @ORM\Column(type="simple_array", nullable=true)
     */
    protected $equipments;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $name;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return Center
     */
    public function getCenter(): ?Center
    {
        return $this->center;
    }

    /**
     * @param Center $center
     */
    public function setCenter(Center $center): void
    {
        $this->center = $center;
    }

    /**
     * @return array
     */
    public function getEquipments(): ?array
    {
        return $this->equipments;
    }

    /**
     * @param array $equipments
     */
    public function setEquipments(array $equipments): void
    {
        $this->equipments = $equipments;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param ExecutionContextInterface $context
     * @param $payload
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (0 === $this->latitude || 0 === $this->longitude || null === $this->postalCode) {
            $context
                ->buildViolation('validator.place.address')
                ->atPath('address')
                ->addViolation()
            ;
        }
    }
}
