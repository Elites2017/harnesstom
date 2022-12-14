<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ScaleRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=ScaleRepository::class)
 * @ApiResource
 */
class Scale
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=DataType::class, inversedBy="scales")
     */
    private $dataType;

    /**
     * @ORM\ManyToOne(targetEntity=Unit::class, inversedBy="scales")
     */
    private $unit;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="scales")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=ObservationVariable::class, mappedBy="scale")
     */
    private $observationVariables;

    /**
     * @ORM\OneToMany(targetEntity=Metabolite::class, mappedBy="scale")
     */
    private $metabolites;

    /**
     * @ORM\OneToMany(targetEntity=ScaleCategory::class, mappedBy="scale")
     */
    private $scaleCategories;

    public function __construct()
    {
        $this->observationVariables = new ArrayCollection();
        $this->metabolites = new ArrayCollection();
        $this->scaleCategories = new ArrayCollection();
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

    public function getDataType(): ?DataType
    {
        return $this->dataType;
    }

    public function setDataType(?DataType $dataType): self
    {
        $this->dataType = $dataType;

        return $this;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

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
     * @return Collection<int, ObservationVariable>
     */
    public function getObservationVariables(): Collection
    {
        return $this->observationVariables;
    }

    public function addObservationVariable(ObservationVariable $observationVariable): self
    {
        if (!$this->observationVariables->contains($observationVariable)) {
            $this->observationVariables[] = $observationVariable;
            $observationVariable->setScale($this);
        }

        return $this;
    }

    public function removeObservationVariable(ObservationVariable $observationVariable): self
    {
        if ($this->observationVariables->removeElement($observationVariable)) {
            // set the owning side to null (unless already changed)
            if ($observationVariable->getScale() === $this) {
                $observationVariable->setScale(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Metabolite>
     */
    public function getMetabolites(): Collection
    {
        return $this->metabolites;
    }

    public function addMetabolite(Metabolite $metabolite): self
    {
        if (!$this->metabolites->contains($metabolite)) {
            $this->metabolites[] = $metabolite;
            $metabolite->setScale($this);
        }

        return $this;
    }

    public function removeMetabolite(Metabolite $metabolite): self
    {
        if ($this->metabolites->removeElement($metabolite)) {
            // set the owning side to null (unless already changed)
            if ($metabolite->getScale() === $this) {
                $metabolite->setScale(null);
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

    /**
     * @return Collection<int, ScaleCategory>
     */
    public function getScaleCategories(): Collection
    {
        return $this->scaleCategories;
    }

    public function addScaleCategory(ScaleCategory $scaleCategory): self
    {
        if (!$this->scaleCategories->contains($scaleCategory)) {
            $this->scaleCategories[] = $scaleCategory;
            $scaleCategory->setScale($this);
        }

        return $this;
    }

    public function removeScaleCategory(ScaleCategory $scaleCategory): self
    {
        if ($this->scaleCategories->removeElement($scaleCategory)) {
            // set the owning side to null (unless already changed)
            if ($scaleCategory->getScale() === $this) {
                $scaleCategory->setScale(null);
            }
        }

        return $this;
    }
}
