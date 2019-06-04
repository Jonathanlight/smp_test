<?php

namespace App\Entity;

use App\Entity\Traits\CodifiableTrait;
use App\Entity\Traits\IdentifiableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PageRepository")
 * @Vich\Uploadable
 */
class Page
{
    use IdentifiableTrait;
    use CodifiableTrait;

    const CODE_HOME = 'home';
    const CODE_ABOUT = 'about';
    const CODE_COURSE = 'course';
    const CODE_LEGISLATION = 'legislation';
    const CODE_LEGAL_NOTICE = 'legal_notice';
    const CODE_CGU = 'cgu';
    const CODE_CGV = 'cgv';
    const CODE_GET_POINT = 'get_point';
    const CODE_WHEN_COURSE = 'when_course';
    const CODE_FLOW_COURSE = 'flow_course';
    const CODE_FAQ = 'faq';
    const CODE_CONTACT = 'contact';
    const CODE_TESTIMONIAL = 'testimonial';
    const CODE_SEARCH = 'search';

    /**
     * @var Block[]
     * @ORM\OneToMany(targetEntity=Block::class, mappedBy="page", cascade={"remove"})
     */
    protected $blocks;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $fileName;

    /**
     * @var File
     * @Vich\UploadableField(mapping="page", fileNameProperty="fileName")
     */
    protected $image;

    /**
     * @var string
     * @ORM\Column(nullable=true)
     * @Gedmo\Slug(fields={"title"})
     */
    protected $slug;

    /**
     * @var string
     * @ORM\Column()
     */
    protected $title;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $updatedAt;

    public function __construct()
    {
        $this->blocks = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->title;
    }

    /**
     * @return Collection
     */
    public function getBlocks(): Collection
    {
        return $this->blocks;
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
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

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
}
