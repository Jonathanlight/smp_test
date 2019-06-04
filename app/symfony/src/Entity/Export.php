<?php

namespace App\Entity;

use App\Entity\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExportRepository")
 */
class Export
{
    use IdentifiableTrait;

    const EXPORT_BANK = 'bank';
    const EXPORT_REFUND = 'refund';
    const EXPORT_FEE = 'fee';

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $exportedAt;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @return \DateTime
     */
    public function getExportedAt(): \DateTime
    {
        return $this->exportedAt;
    }

    /**
     * @param \DateTime $exportedAt
     */
    public function setExportedAt(\DateTime $exportedAt): void
    {
        $this->exportedAt = $exportedAt;
    }

    /**
     * @return string
     */
    public function getType(): string
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
}
