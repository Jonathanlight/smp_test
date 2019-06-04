<?php

namespace App\Entity;

use App\Entity\Traits\IdentifiableTrait;
use App\Entity\Traits\IdentityTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;

/**
 * @ORM\Entity()
 */
class Submission
{
    use IdentifiableTrait;
    use IdentityTrait;

    /**
     * @var string
     * @ORM\Column()
     * @Assert\Email(message = "validator.submission.email")
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $invoiceReference;

    /**
     * @Recaptcha\IsTrue(message="validator.submission.captcha")
     */
    public $recaptcha;

    /**
     * @var string
     * @Assert\Length(min=10, minMessage="validator.submission.message")
     * @ORM\Column(type="text")
     */
    protected $message;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $traineeReference;

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getInvoiceReference(): ?string
    {
        return $this->invoiceReference;
    }

    /**
     * @param string $invoiceReference
     */
    public function setInvoiceReference(string $invoiceReference): void
    {
        $this->invoiceReference = $invoiceReference;
    }

    /**
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getTraineeReference(): ?string
    {
        return $this->traineeReference;
    }

    /**
     * @param string $traineeReference
     */
    public function setTraineeReference(string $traineeReference): void
    {
        $this->traineeReference = $traineeReference;
    }
}
