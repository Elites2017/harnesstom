<?php

namespace App\Entity;

use App\Repository\SampleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SampleRepository::class)
 */
class Sample
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
    private $replicate;

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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastUpdated;

    /**
     * @ORM\ManyToOne(targetEntity=Study::class, inversedBy="samples")
     */
    private $study;

    /**
     * @ORM\ManyToOne(targetEntity=Germplasm::class, inversedBy="samples")
     */
    private $germplasm;

    /**
     * @ORM\ManyToOne(targetEntity=DevelopmentalStage::class, inversedBy="samples")
     */
    private $developmentalStage;

    /**
     * @ORM\ManyToOne(targetEntity=AnatomicalEntity::class, inversedBy="samples")
     */
    private $anatomicalEntity;

    /**
     * @ORM\ManyToOne(targetEntity=ObservationLevel::class, inversedBy="samples")
     */
    private $observationLevel;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="samples")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=VariantSet::class, mappedBy="sample")
     */
    private $variantSets;

    /**
     * @ORM\OneToMany(targetEntity=MetaboliteValue::class, mappedBy="sample")
     */
    private $metaboliteValues;

    public function __construct()
    {
        $this->variantSets = new ArrayCollection();
        $this->metaboliteValues = new ArrayCollection();
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

    public function getReplicate(): ?string
    {
        return $this->replicate;
    }

    public function setReplicate(?string $replicate): self
    {
        $this->replicate = $replicate;

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

    public function getLastUpdated(): ?\DateTimeInterface
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(?\DateTimeInterface $lastUpdated): self
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }

    public function getStudy(): ?Study
    {
        return $this->study;
    }

    public function setStudy(?Study $study): self
    {
        $this->study = $study;

        return $this;
    }

    public function getGermplasm(): ?Germplasm
    {
        return $this->germplasm;
    }

    public function setGermplasm(?Germplasm $germplasm): self
    {
        $this->germplasm = $germplasm;

        return $this;
    }

    public function getDevelopmentalStage(): ?DevelopmentalStage
    {
        return $this->developmentalStage;
    }

    public function setDevelopmentalStage(?DevelopmentalStage $developmentalStage): self
    {
        $this->developmentalStage = $developmentalStage;

        return $this;
    }

    public function getAnatomicalEntity(): ?AnatomicalEntity
    {
        return $this->anatomicalEntity;
    }

    public function setAnatomicalEntity(?AnatomicalEntity $anatomicalEntity): self
    {
        $this->anatomicalEntity = $anatomicalEntity;

        return $this;
    }

    public function getObservationLevel(): ?ObservationLevel
    {
        return $this->observationLevel;
    }

    public function setObservationLevel(?ObservationLevel $observationLevel): self
    {
        $this->observationLevel = $observationLevel;

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
            $variantSet->setSample($this);
        }

        return $this;
    }

    public function removeVariantSet(VariantSet $variantSet): self
    {
        if ($this->variantSets->removeElement($variantSet)) {
            // set the owning side to null (unless already changed)
            if ($variantSet->getSample() === $this) {
                $variantSet->setSample(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MetaboliteValue>
     */
    public function getMetaboliteValues(): Collection
    {
        return $this->metaboliteValues;
    }

    public function addMetaboliteValue(MetaboliteValue $metaboliteValue): self
    {
        if (!$this->metaboliteValues->contains($metaboliteValue)) {
            $this->metaboliteValues[] = $metaboliteValue;
            $metaboliteValue->setSample($this);
        }

        return $this;
    }

    public function removeMetaboliteValue(MetaboliteValue $metaboliteValue): self
    {
        if ($this->metaboliteValues->removeElement($metaboliteValue)) {
            // set the owning side to null (unless already changed)
            if ($metaboliteValue->getSample() === $this) {
                $metaboliteValue->setSample(null);
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
}
