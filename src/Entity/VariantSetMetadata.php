<?php

namespace App\Entity;

use App\Repository\VariantSetMetadataRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VariantSetMetadataRepository::class)
 */
class VariantSetMetadata
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filters;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $variantCount;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $publicationRef = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dataUpload;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fileUrl;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=GenotypingPlatform::class, inversedBy="variantSetMetadata")
     */
    private $genotypingPlatform;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="variantSetMetadata")
     */
    private $createdBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFilters(): ?string
    {
        return $this->filters;
    }

    public function setFilters(?string $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function getVariantCount(): ?int
    {
        return $this->variantCount;
    }

    public function setVariantCount(?int $variantCount): self
    {
        $this->variantCount = $variantCount;

        return $this;
    }

    public function getPublicationRef(): ?array
    {
        return $this->publicationRef;
    }

    public function setPublicationRef(?array $publicationRef): self
    {
        $this->publicationRef = $publicationRef;

        return $this;
    }

    public function getDataUpload(): ?string
    {
        return $this->dataUpload;
    }

    public function setDataUpload(string $dataUpload): self
    {
        $this->dataUpload = $dataUpload;

        return $this;
    }

    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }

    public function setFileUrl(string $fileUrl): self
    {
        $this->fileUrl = $fileUrl;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getGenotypingPlatform(): ?GenotypingPlatform
    {
        return $this->genotypingPlatform;
    }

    public function setGenotypingPlatform(?GenotypingPlatform $genotypingPlatform): self
    {
        $this->genotypingPlatform = $genotypingPlatform;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
