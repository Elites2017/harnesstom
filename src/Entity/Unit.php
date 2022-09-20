<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UnitRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=UnitRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"unit:read"}},
 *      denormalizationContext={"groups"={"unit:write"}}
 * )
 */
class Unit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"unit:read", "study:read"})
     * @SerializedName("unitDbId")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"unit:read", "study:read"})
     * @SerializedName("unitName")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"unit:read", "study:read"})
     */
    private $ontology_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"unit:read", "study:read"})
     * @SerializedName("unitDescription")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"unit:read", "study:read"})
     */
    private $parentTerm;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="units")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Parameter::class, mappedBy="unit")
     */
    private $parameters;

    /**
     * @ORM\OneToMany(targetEntity=Scale::class, mappedBy="unit")
     */
    private $scales;

    /**
     * @ORM\OneToMany(targetEntity=QTLStudy::class, mappedBy="genomeMapUnit")
     */
    private $qTLStudies;

    public function __construct()
    {
        $this->parameters = new ArrayCollection();
        $this->scales = new ArrayCollection();
        $this->qTLStudies = new ArrayCollection();
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

    public function getParentTerm(): ?string
    {
        return $this->parentTerm;
    }

    public function setParentTerm(?string $parentTerm): self
    {
        $this->parentTerm = $parentTerm;

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
     * @return Collection<int, Parameter>
     */
    public function getParameters(): Collection
    {
        return $this->parameters;
    }

    public function addParameter(Parameter $parameter): self
    {
        if (!$this->parameters->contains($parameter)) {
            $this->parameters[] = $parameter;
            $parameter->setUnit($this);
        }

        return $this;
    }

    public function removeParameter(Parameter $parameter): self
    {
        if ($this->parameters->removeElement($parameter)) {
            // set the owning side to null (unless already changed)
            if ($parameter->getUnit() === $this) {
                $parameter->setUnit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Scale>
     */
    public function getScales(): Collection
    {
        return $this->scales;
    }

    public function addScale(Scale $scale): self
    {
        if (!$this->scales->contains($scale)) {
            $this->scales[] = $scale;
            $scale->setUnit($this);
        }

        return $this;
    }

    public function removeScale(Scale $scale): self
    {
        if ($this->scales->removeElement($scale)) {
            // set the owning side to null (unless already changed)
            if ($scale->getUnit() === $this) {
                $scale->setUnit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QTLStudy>
     */
    public function getQTLStudies(): Collection
    {
        return $this->qTLStudies;
    }

    public function addQTLStudy(QTLStudy $qTLStudy): self
    {
        if (!$this->qTLStudies->contains($qTLStudy)) {
            $this->qTLStudies[] = $qTLStudy;
            $qTLStudy->setGenomeMapUnit($this);
        }

        return $this;
    }

    public function removeQTLStudy(QTLStudy $qTLStudy): self
    {
        if ($this->qTLStudies->removeElement($qTLStudy)) {
            // set the owning side to null (unless already changed)
            if ($qTLStudy->getGenomeMapUnit() === $this) {
                $qTLStudy->setGenomeMapUnit(null);
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
