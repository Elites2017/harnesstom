<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\VariantSetMetadataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=VariantSetMetadataRepository::class)
 * @ApiResource
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
     * @ORM\Column(type="text", nullable=true)
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
     * @ORM\Column(type="text")
     */
    private $dataUpload;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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

    /**
     * @ORM\OneToMany(targetEntity=GWAS::class, mappedBy="variantSetMetada")
     */
    private $gWAS;

    /**
     * @ORM\OneToMany(targetEntity=VariantSet::class, mappedBy="variantSetMetadata")
     */
    private $variantSets;

    /**
     * @ORM\ManyToOne(targetEntity=Software::class, inversedBy="variantSetMetadata")
     */
    private $software;

    public function __construct()
    {
        $this->gWAS = new ArrayCollection();
        $this->variantSets = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, GWAS>
     */
    public function getGWAS(): Collection
    {
        return $this->gWAS;
    }

    public function addGWA(GWAS $gWA): self
    {
        if (!$this->gWAS->contains($gWA)) {
            $this->gWAS[] = $gWA;
            $gWA->setVariantSetMetada($this);
        }

        return $this;
    }

    public function removeGWA(GWAS $gWA): self
    {
        if ($this->gWAS->removeElement($gWA)) {
            // set the owning side to null (unless already changed)
            if ($gWA->getVariantSetMetada() === $this) {
                $gWA->setVariantSetMetada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, VariantSet>
     */
    public function getVariantSets(): Collection
    {
        return $this->variantSets;
    }

    public function addVariantSet(VariantSet $variantSet): self
    {
        if (!$this->variantSets->contains($variantSet)) {
            $this->variantSets[] = $variantSet;
            $variantSet->setVariantSetMetadata($this);
        }

        return $this;
    }

    public function removeVariantSet(VariantSet $variantSet): self
    {
        if ($this->variantSets->removeElement($variantSet)) {
            // set the owning side to null (unless already changed)
            if ($variantSet->getVariantSetMetadata() === $this) {
                $variantSet->setVariantSetMetadata(null);
            }
        }

        return $this;
    }

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->name;
    }

    public function getSoftware(): ?Software
    {
        return $this->software;
    }

    public function setSoftware(?Software $software): self
    {
        $this->software = $software;

        return $this;
    }
}
