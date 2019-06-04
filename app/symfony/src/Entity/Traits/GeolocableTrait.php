<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait GeolocableTrait
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(nullable=true)
     */
    protected $address;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $city;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $country;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $department;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    protected $latitude = 0;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    protected $longitude = 0;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $postalCode;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $region;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $streetName;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $streetNumber;

    /**
     * @return string
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    /**
     * @return string|null
     */
    public function getDepartment(): ?string
    {
        return $this->department;
    }

    /**
     * @param string $department
     */
    public function setDepartment(string $department): void
    {
        $this->department = $department;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return string|null
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string|null
     */
    public function getRegion(): ?string
    {
        return $this->region;
    }

    /**
     * @param string $region
     */
    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    /**
     * @return string|null
     */
    public function getStreetName(): ?string
    {
        return $this->streetName;
    }

    /**
     * @param string $streetName
     */
    public function setStreetName(string $streetName): void
    {
        $this->streetName = $streetName;
    }

    /**
     * @return string|null
     */
    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    /**
     * @param string $streetNumber
     */
    public function setStreetNumber(string $streetNumber): void
    {
        $this->streetNumber = $streetNumber;
    }

    /**
     * @return string
     */
    public function getAddressWithoutStreetNumber(): string
    {
        return $this->streetName.', '.$this->postalCode.' '.$this->city;
    }

    /**
     * @return string
     */
    public function getAddressWithStreetNumber(): string
    {
        return $this->streetNumber.' '.$this->streetName.', '.$this->postalCode.' '.$this->city;
    }

    /**
     * @return string
     */
    public function getShortAddress(): string
    {
        return $this->streetNumber.', '.$this->streetName;
    }
}
