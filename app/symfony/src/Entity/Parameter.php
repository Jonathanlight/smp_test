<?php

namespace App\Entity;

use App\Entity\Traits\CodifiableTrait;
use App\Entity\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Parameter
{
    const CODE_EMAIL = 'smp.default.email';
    const CODE_PHONE = 'smp.default.phone';
    const CODE_COMMISSION = 'smp.default.commission';

    const EMAIL_DEFAULT = 'courrier@plusdepoints.fr';

    use IdentifiableTrait;
    use CodifiableTrait;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $value;

    /**
     * @return string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
