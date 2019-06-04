<?php

namespace App\Entity;

use App\Entity\Traits\CodifiableTrait;
use App\Entity\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SnippetRepository")
 * @Vich\Uploadable
 */
class Snippet
{
    const CODE_RECOVERY_STAGE = 'recovery_stage';
    const CODE_WICH_CASE_STAGE = 'wich_case_stage';
    const CODE_PROGRESS_STAGE = 'progress_stage';
    const CODE_TEMOIGNAGE = 'testimonials';
    const CODE_FORM_CONTACT = 'form_contact';
    const CODE_COURSE_PROXIMITY = 'course_proximity';
    const CODE_SECURE_PAYMENT = 'secure_payment';
    const CODE_CENTER_APPROVED = 'center_approved';
    const CODE_BEST_PRICE = 'best_price';
    const CODE_CITY_1 = 'city_1';
    const CODE_CITY_2 = 'city_2';
    const CODE_CITY_3 = 'city_3';
    const CODE_CITY_4 = 'city_4';

    use IdentifiableTrait;
    use CodifiableTrait;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $url;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $fileName;

    /**
     * @var File
     * @Vich\UploadableField(mapping="snippet", fileNameProperty="fileName")
     */
    protected $image;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName(?string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * @return File
     */
    public function getImage(): ?File
    {
        return $this->image;
    }

    /**
     * @param File $image
     *
     * @throws \Exception
     */
    public function setImage(?File $image): void
    {
        $this->image = $image;

        if (null !== $image) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }
}
