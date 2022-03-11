<?php

namespace App\Entity;

use App\Repository\GermplasmStudyImageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GermplasmStudyImageRepository::class)
 */
class GermplasmStudyImage
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
    private $filename;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=FactorType::class, inversedBy="germplasmStudyImages")
     */
    private $factor;

    /**
     * @ORM\ManyToOne(targetEntity=DevelopmentalStage::class, inversedBy="germplasmStudyImages")
     */
    private $developmentStage;

    /**
     * @ORM\ManyToOne(targetEntity=AnatomicalEntity::class, inversedBy="germplasmStudyImages")
     */
    private $plantAnatomicalEntity;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="germplasmStudyImages")
     */
    private $createdBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

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

    public function getFactor(): ?FactorType
    {
        return $this->factor;
    }

    public function setFactor(?FactorType $factor): self
    {
        $this->factor = $factor;

        return $this;
    }

    public function getDevelopmentStage(): ?DevelopmentalStage
    {
        return $this->developmentStage;
    }

    public function setDevelopmentStage(?DevelopmentalStage $developmentStage): self
    {
        $this->developmentStage = $developmentStage;

        return $this;
    }

    public function getPlantAnatomicalEntity(): ?AnatomicalEntity
    {
        return $this->plantAnatomicalEntity;
    }

    public function setPlantAnatomicalEntity(?AnatomicalEntity $plantAnatomicalEntity): self
    {
        $this->plantAnatomicalEntity = $plantAnatomicalEntity;

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
