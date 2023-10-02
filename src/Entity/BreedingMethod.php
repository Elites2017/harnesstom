<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BreedingMethodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=BreedingMethodRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"breeding_method:read"}},
 *      denormalizationContext={"groups"={"breeding_method:write"}}
 * )
 */
class BreedingMethod
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"breeding_method:read"})
     * @SerializedName("breedingMethodDbId")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"breeding_method:read"})
     * @SerializedName("breedingMethodName")
     */
    private $name;

    /**
     * 
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private $ontology_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"breeding_method:read"})
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="breedingMethods")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Cross::class, mappedBy="breedingMethod")
     */
    private $crosses;

    /**
     * @ORM\ManyToOne(targetEntity=BreedingMethod::class, inversedBy="breedingMethods")
     */
    private $parentTerm;

    /**
     * @ORM\OneToMany(targetEntity=BreedingMethod::class, mappedBy="parentTerm")
     */
    private $breedingMethods;

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
        $this->crosses = new ArrayCollection();
        $this->breedingMethods = new ArrayCollection();
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
     * @return Collection<int, Cross>
     */
    public function getCrosses(): Collection
    {
        return $this->crosses;
    }

    public function addCross(Cross $cross): self
    {
        if (!$this->crosses->contains($cross)) {
            $this->crosses[] = $cross;
            $cross->setBreedingMethod($this);
        }

        return $this;
    }

    public function removeCross(Cross $cross): self
    {
        if ($this->crosses->removeElement($cross)) {
            // set the owning side to null (unless already changed)
            if ($cross->getBreedingMethod() === $this) {
                $cross->setBreedingMethod(null);
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
    public function getBreedingMethods(): Collection
    {
        return $this->breedingMethods;
    }

    public function addBreedingMethod(self $breedingMethod): self
    {
        if (!$this->breedingMethods->contains($breedingMethod)) {
            $this->breedingMethods[] = $breedingMethod;
            $breedingMethod->setParentTerm($this);
        }

        return $this;
    }

    public function removeBreedingMethod(self $breedingMethod): self
    {
        if ($this->breedingMethods->removeElement($breedingMethod)) {
            // set the owning side to null (unless already changed)
            if ($breedingMethod->getParentTerm() === $this) {
                $breedingMethod->setParentTerm(null);
            }
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

    // API SECTION BRAPI V2.1 - Last Code Update July 2023
}
