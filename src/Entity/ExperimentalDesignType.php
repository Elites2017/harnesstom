<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\ExperimentalDesignTypeRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=ExperimentalDesignTypeRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"experimental_d_t:read"}},
 *      denormalizationContext={"groups"={"experimental_d_t:write"}}
 * )
 */
class ExperimentalDesignType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"experimental_d_t:read", "study:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"experimental_d_t:read", "study:read"})
     */
    private $name;

    /**
     * 
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     * @Groups({"experimental_d_t:read", "study:read"})
     */
    private $ontology_id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="experimentalDesignTypes")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Study::class, mappedBy="experimentalDesignType")
     */
    private $studies;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity=ExperimentalDesignType::class, inversedBy="experimentalDesignTypes")
     */
    private $parentTerm;

    /**
     * @ORM\ManyToMany(targetEntity=ExperimentalDesignType::class, mappedBy="parentTerm")
     */
    private $experimentalDesignTypes;

    /**
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $par_ont;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_poau;

    public function __construct()
    {
        $this->studies = new ArrayCollection();
        $this->experimentalDesignTypes = new ArrayCollection();
        $this->parentTerm = new ArrayCollection();
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
     * @return Collection<int, Study>
     */
    public function getStudies(): Collection
    {
        return $this->studies;
    }

    public function addStudy(Study $study): self
    {
        if (!$this->studies->contains($study)) {
            $this->studies[] = $study;
            $study->setExperimentalDesignType($this);
        }

        return $this;
    }

    public function removeStudy(Study $study): self
    {
        if ($this->studies->removeElement($study)) {
            // set the owning side to null (unless already changed)
            if ($study->getExperimentalDesignType() === $this) {
                $study->setExperimentalDesignType(null);
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getParentTerm(): Collection
    {
        return $this->parentTerm;
    }

    public function addParentTerm(self $parentTerm): self
    {
        if (!$this->parentTerm->contains($parentTerm)) {
            $this->parentTerm[] = $parentTerm;
        }

        return $this;
    }

    public function removeParentTerm(self $parentTerm): self
    {
        $this->parentTerm->removeElement($parentTerm);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getExperimentalDesignTypes(): Collection
    {
        return $this->experimentalDesignTypes;
    }

    public function addExperimentalDesignType(self $experimentalDesignType): self
    {
        if (!$this->experimentalDesignTypes->contains($experimentalDesignType)) {
            $this->experimentalDesignTypes[] = $experimentalDesignType;
            $experimentalDesignType->addParentTerm($this);
        }

        return $this;
    }

    public function removeExperimentalDesignType(self $experimentalDesignType): self
    {
        if ($this->experimentalDesignTypes->removeElement($experimentalDesignType)) {
            $experimentalDesignType->removeParentTerm($this);
        }

        return $this;
    }

    public function getParOnt(): ?string
    {
        return $this->par_ont;
    }

    public function setParOnt(string $par_ont): self
    {
        $this->par_ont = $par_ont;

        return $this;
    }

    public function getIsPoau(): ?bool
    {
        return $this->is_poau;
    }

    public function setIsPoau(?bool $is_poau): self
    {
        $this->is_poau = $is_poau;

        return $this;
    }

}
