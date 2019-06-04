<?php

namespace App\Entity;

use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\CodifiableTrait;
use App\Entity\Traits\GeolocableTrait;
use App\Entity\Traits\IdentifiableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity()
 * @UniqueEntity("code")
 * @Serializer\ExclusionPolicy("all")
 */
class Center
{
    use IdentifiableTrait;
    use CodifiableTrait;
    use GeolocableTrait;
    use DeletableTrait;

    /**
     * @var string
     * @ORM\Column(length=5, nullable=true)
     * @Assert\Length(min="5", max="5", exactMessage="validator.center.ape")
     */
    protected $apeCode;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $approvalNumber;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $bankName;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $bankOwner;

    /**
     * @var string
     * @ORM\Column(length=11, nullable=true)
     * @Assert\Bic()
     * @Assert\Regex("/[A-Z0-9]{8,11}$/", message="validator.center.bic")
     */
    protected $bic;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comment;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity=Course::class, mappedBy="center")
     */
    protected $courses;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * @Assert\Email()
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * @Assert\Regex("/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/")
     */
    protected $fax;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * @Assert\Regex("/^[\p{L}\-' ]+$/u")
     */
    protected $firstName;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $validatedCGU = false;

    /**
     * @var string
     * @ORM\Column(length=27, nullable=true)
     * @Assert\Iban()
     * @Assert\Regex("/[A-Z0-9]{27}$/", message="validator.center.iban")
     */
    protected $iban;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * @Assert\Regex("/^[\p{L}\-' ]+$/u")
     */
    protected $lastName;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * @Assert\Regex("/^(?:(?:\+|00)33|0)\s*[6-7](?:[\s.-]*\d{2}){4}$/")
     */
    protected $mobile;

    /**
     * @var string
     * @ORM\Column()
     * @Serializer\Expose()
     */
    protected $name;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity=Payment::class, mappedBy="center")
     */
    protected $payments;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * @Assert\Regex("/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/")
     */
    protected $phone;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * @Assert\Type(type="alpha")
     */
    protected $prefecture;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $rcs;

    /**
     * @var string
     * @ORM\Column(length=14, nullable=true)
     * @Assert\Length(min="14", max="14")
     * @Assert\Type(type="digit")
     */
    protected $siret;

    /**
     * @var Tarif
     * @ORM\OneToOne(targetEntity=Tarif::class, cascade={"persist", "remove"})
     */
    protected $tarif;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="center", cascade={"persist","remove"})
     */
    protected $users;

    /**
     * @var string
     * @ORM\Column(length=13, nullable=true)
     * @Assert\Length(max="13")
     */
    protected $vatNumber;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * @Assert\Type(type="digit", message="validator.default.invalid")
     */
    protected $capital;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $juridiqueForme;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getApeCode(): ?string
    {
        return $this->apeCode;
    }

    /**
     * @param string $apeCode
     */
    public function setApeCode(?string $apeCode): void
    {
        $this->apeCode = $apeCode;
    }

    /**
     * @return string
     */
    public function getApprovalNumber(): ?string
    {
        return $this->approvalNumber;
    }

    /**
     * @param string $approvalNumber
     */
    public function setApprovalNumber(?string $approvalNumber): void
    {
        $this->approvalNumber = $approvalNumber;
    }

    /**
     * @return string
     */
    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    /**
     * @param string $bankName
     */
    public function setBankName(?string $bankName): void
    {
        $this->bankName = $bankName;
    }

    /**
     * @return string
     */
    public function getBankOwner(): ?string
    {
        return $this->bankOwner;
    }

    /**
     * @param string $bankOwner
     */
    public function setBankOwner(?string $bankOwner): void
    {
        $this->bankOwner = $bankOwner;
    }

    /**
     * @return string
     */
    public function getBic(): ?string
    {
        return $this->bic;
    }

    /**
     * @param string $bic
     */
    public function setBic(?string $bic): void
    {
        $this->bic = $bic;
    }

    /**
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return Collection
     */
    public function getCourses(): ?Collection
    {
        return $this->courses;
    }

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
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getFax(): ?string
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     */
    public function setFax(?string $fax): void
    {
        $this->fax = $fax;
    }

    /**
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return bool
     */
    public function isValidatedCGU(): ?bool
    {
        return $this->validatedCGU;
    }

    /**
     * @param bool $validatedCGU
     */
    public function setValidatedCGU(bool $validatedCGU): void
    {
        $this->validatedCGU = $validatedCGU;
    }

    /**
     * @return string
     */
    public function getIban(): ?string
    {
        return $this->iban;
    }

    /**
     * @param string $iban
     */
    public function setIban(?string $iban): void
    {
        $this->iban = $iban;
    }

    /**
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile(?string $mobile): void
    {
        $this->mobile = $mobile;
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
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Collection
     */
    public function getPayments(): ?Collection
    {
        return $this->payments;
    }

    /**
     * @return string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getPrefecture(): ?string
    {
        return $this->prefecture;
    }

    /**
     * @param string $prefecture
     */
    public function setPrefecture(?string $prefecture): void
    {
        $this->prefecture = $prefecture;
    }

    /**
     * @return string
     */
    public function getRcs(): ?string
    {
        return $this->rcs;
    }

    /**
     * @param string $rcs
     */
    public function setRcs(?string $rcs): void
    {
        $this->rcs = $rcs;
    }

    /**
     * @return string
     */
    public function getSiret(): ?string
    {
        return $this->siret;
    }

    /**
     * @param string $siret
     */
    public function setSiret(?string $siret): void
    {
        $this->siret = $siret;
    }

    /**
     * @return Tarif
     */
    public function getTarif(): ?Tarif
    {
        return $this->tarif;
    }

    /**
     * @param Tarif $tarif
     */
    public function setTarif(Tarif $tarif): void
    {
        $this->tarif = $tarif;
    }

    /**
     * @param User $user
     */
    public function addUser(User $user)
    {
        if ($this->users->contains($user)) {
            return;
        }

        $this->users->add($user);
    }

    /**
     * @return Collection
     */
    public function getUsers(): ?Collection
    {
        return $this->users;
    }

    /**
     * @return string
     */
    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    /**
     * @param string $vatNumber
     */
    public function setVatNumber(?string $vatNumber): void
    {
        $this->vatNumber = $vatNumber;
    }

    /**
     * @return string
     */
    public function getCapital(): ?string
    {
        return $this->capital;
    }

    /**
     * @param string $capital
     */
    public function setCapital(?string $capital): void
    {
        $this->capital = $capital;
    }

    /**
     * @return string
     */
    public function getJuridiqueForme(): ?string
    {
        return $this->juridiqueForme;
    }

    /**
     * @param string $juridiqueForme
     */
    public function setJuridiqueForme(?string $juridiqueForme): void
    {
        $this->juridiqueForme = $juridiqueForme;
    }

    /**
     * @param ExecutionContextInterface $context
     * @param $payload
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (null === $this->postalCode) {
            $context
                ->buildViolation('validator.center.address')
                ->atPath('address')
                ->addViolation()
            ;
        }
    }
}
