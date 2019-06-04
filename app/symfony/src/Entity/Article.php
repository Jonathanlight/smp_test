<?php

namespace App\Entity;

use App\Entity\Traits\IdentifiableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 * @Vich\Uploadable
 */
class Article
{
    use IdentifiableTrait;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $url;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $fileName;

    /**
     * @var File
     * @Vich\UploadableField(mapping="article", fileNameProperty="fileName")
     */
    protected $image;

    /**
     * @var \DateTime
     * @ORM\Column(type="date")
     */
    protected $date;

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
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
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
    public function setUrl(string $url): void
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
    public function setImage(File $image): void
    {
        $this->image = $image;

        if (null !== $image) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

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
}
