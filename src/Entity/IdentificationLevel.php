<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\IdentificationLevelRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=IdentificationLevelRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"identification_level"}},
 *      denormalizationContext={"groups"={"identification_level:write"}}
 * )
 */
class IdentificationLevel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"identification_level"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="identificationLevels")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Analyte::class, mappedBy="identificationLevel")
     */
    private $analytes;

    /**
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private $ontology_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=IdentificationLevel::class, inversedBy="identificationLevels")
     */
    private $parentTerm;

    /**
     * @ORM\OneToMany(targetEntity=IdentificationLevel::class, mappedBy="parentTerm")
     */
    private $identificationLevels;

    public function __construct()
    {
        $this->analytes = new ArrayCollection();
        $this->identificationLevels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, Analyte>
     */
    public function getAnalytes(): Collection
    {
        return $this->analytes;
    }

    public function addAnalyte(Analyte $analyte): self
    {
        if (!$this->analytes->contains($analyte)) {
            $this->analytes[] = $analyte;
            $analyte->setIdentificationLevel($this);
        }

        return $this;
    }

    public function removeAnalyte(Analyte $analyte): self
    {
        if ($this->analytes->removeElement($analyte)) {
            // set the owning side to null (unless already changed)
            if ($analyte->getIdentificationLevel() === $this) {
                $analyte->setIdentificationLevel(null);
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOntologyId(): ?string
    {
        return $this->ontology_id;
    }

    public function setOntologyId(string $ontology_id): self
    {
        $this->ontology_id = $ontology_id;

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

    public function getParentTerm(): ?self
    {
        return $this->parentTerm;
    }

    public function setParentTerm(?self $parentTerm): self
    {
        $this->parentTerm = $parentTerm;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getIdentificationLevels(): Collection
    {
        return $this->identificationLevels;
    }

    public function addIdentificationLevel(self $identificationLevel): self
    {
        if (!$this->identificationLevels->contains($identificationLevel)) {
            $this->identificationLevels[] = $identificationLevel;
            $identificationLevel->setParentTerm($this);
        }

        return $this;
    }

    public function removeIdentificationLevel(self $identificationLevel): self
    {
        if ($this->identificationLevels->removeElement($identificationLevel)) {
            // set the owning side to null (unless already changed)
            if ($identificationLevel->getParentTerm() === $this) {
                $identificationLevel->setParentTerm(null);
            }
        }

        return $this;
    }
}
