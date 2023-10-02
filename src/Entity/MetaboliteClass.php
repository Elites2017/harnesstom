<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\MetaboliteClassRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=MetaboliteClassRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"metabolite_class:read"}},
 *      denormalizationContext={"groups"={"metabolite_class:write"}}
 * )
 */
class MetaboliteClass
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"metabolite_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"metabolite_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="metaboliteClasses")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private $ontology_id;

    /**
     * @ORM\ManyToOne(targetEntity=MetaboliteClass::class, inversedBy="metaboliteClasses")
     */
    private $parentTerm;

    /**
     * @ORM\OneToMany(targetEntity=MetaboliteClass::class, mappedBy="parentTerm")
     */
    private $metaboliteClasses;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $par_ont;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_poau;

    /**
     * @ORM\OneToMany(targetEntity=Analyte::class, mappedBy="metaboliteClass")
     */
    private $analytes;

    public function __construct()
    {
        $this->metaboliteClasses = new ArrayCollection();
        $this->analytes = new ArrayCollection();
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

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->name;
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
    public function getMetaboliteClasses(): Collection
    {
        return $this->metaboliteClasses;
    }

    public function addMetaboliteClass(self $metaboliteClass): self
    {
        if (!$this->metaboliteClasses->contains($metaboliteClass)) {
            $this->metaboliteClasses[] = $metaboliteClass;
            $metaboliteClass->setParentTerm($this);
        }

        return $this;
    }

    public function removeMetaboliteClass(self $metaboliteClass): self
    {
        if ($this->metaboliteClasses->removeElement($metaboliteClass)) {
            // set the owning side to null (unless already changed)
            if ($metaboliteClass->getParentTerm() === $this) {
                $metaboliteClass->setParentTerm(null);
            }
        }

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
            $analyte->setMetaboliteClass($this);
        }

        return $this;
    }

    public function removeAnalyte(Analyte $analyte): self
    {
        if ($this->analytes->removeElement($analyte)) {
            // set the owning side to null (unless already changed)
            if ($analyte->getMetaboliteClass() === $this) {
                $analyte->setMetaboliteClass(null);
            }
        }

        return $this;
    }
}
