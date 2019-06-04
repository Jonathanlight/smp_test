<?php

namespace App\Entity;

use App\Entity\Traits\IdentifiableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 */
class Payment
{
    use IdentifiableTrait;

    const STATUS_PAID = 'paid';

    /**
     * @var Center
     * @ORM\ManyToOne(targetEntity=Center::class, inversedBy="payments")
     */
    protected $center;

    /**
     * @var Course
     * @ORM\OneToMany(targetEntity=Course::class, mappedBy="payment")
     */
    protected $courses;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $generatedAt;

    /**
     * @var string
     * @ORM\Column()
     */
    private $reference;

    /**
     * @var string
     * @ORM\Column()
     */
    private $status;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=2, nullable=false)
     */
    private $amount;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
    }

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
     * @return Collection
     */
    public function getCourses(): ?Collection
    {
        return $this->courses;
    }

    /**
     * @return \DateTime
     */
    public function getGeneratedAt(): \DateTime
    {
        return $this->generatedAt;
    }

    /**
     * @param \DateTime $generatedAt
     */
    public function setGeneratedAt(\DateTime $generatedAt): void
    {
        $this->generatedAt = $generatedAt;
    }

    /**
     * @return string
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     */
    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}
