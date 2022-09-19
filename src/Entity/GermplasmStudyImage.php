<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GermplasmStudyImageRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=GermplasmStudyImageRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"study_image:read"}},
 *      denormalizationContext={"groups"={"study_image:write"}}
 * )
 */
class GermplasmStudyImage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"study_image:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"study_image:read"})
     */
    private $filename;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"study_image:read"})
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

    /**
     * @ORM\ManyToOne(targetEntity=Germplasm::class, inversedBy="germplasmStudyImages")
     */
    private $GermplasmID;

    /**
     * @ORM\ManyToOne(targetEntity=Study::class, inversedBy="germplasmStudyImages")
     */
    private $StudyID;

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

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->filename;
    }

    public function getGermplasmID(): ?Germplasm
    {
        return $this->GermplasmID;
    }

    public function setGermplasmID(?Germplasm $GermplasmID): self
    {
        $this->GermplasmID = $GermplasmID;

        return $this;
    }

    public function getStudyID(): ?Study
    {
        return $this->StudyID;
    }

    public function setStudyID(?Study $StudyID): self
    {
        $this->StudyID = $StudyID;

        return $this;
    }
}
