<?php

namespace App\Entity;

use App\Entity\Traits\CodifiableTrait;
use App\Entity\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity()
 * @ORM\Table(name="options")
 * @Serializer\ExclusionPolicy("all")
 */
class Option
{
    use IdentifiableTrait;
    use CodifiableTrait;

    const CODE_APPLICATION_FEE = 'application_fee';
    const CODE_SEND_LETTER = 'send_letter';

    const OPTION_AMOUNT_SEND_LETTER = 4.90;
    const OPTION_AMOUNT_APPLICATION_FEE = 4.95;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2)
     * @Serializer\Expose()
     */
    protected $amount;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $name;

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getName(): string
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
}
