<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GrowthFacilityTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=GrowthFacilityTypeRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"growth_f_t:read"}},
 *      denormalizationContext={"groups"={"growth_f_t:write"}}
 * )
 */
class GrowthFacilityType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"growth_f_t:read", "study:read"})
     */
    private $id;

    /**
     * 
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     * @Groups({"growth_f_t:read", "study:read"})
     */
    private $ontology_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"growth_f_t:read", "study:read"})
     * @SerializedName("description")
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"growth_f_t:read", "study:read"})
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="growthFacilityTypes")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Study::class, mappedBy="growthFacility")
     */
    private $studies;

    /**
     * @ORM\ManyToOne(targetEntity=GrowthFacilityType::class, inversedBy="growthFacilityTypes")
     */
    private $parentTerm;

    /**
     * @ORM\OneToMany(targetEntity=GrowthFacilityType::class, mappedBy="parentTerm")
     */
    private $growthFacilityTypes;

    public function __construct()
    {
        $this->studies = new ArrayCollection();
        $this->growthFacilityTypes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
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
            $study->setGrowthFacility($this);
        }

        return $this;
    }

    public function removeStudy(Study $study): self
    {
        if ($this->studies->removeElement($study)) {
            // set the owning side to null (unless already changed)
            if ($study->getGrowthFacility() === $this) {
                $study->setGrowthFacility(null);
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
     * @Groups({"growth_f_t:read", "study:read"})
     * @SerializedName("PUI")
     */
    public function getPUI(){
        return $this->ontology_id;
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
    public function getGrowthFacilityTypes(): Collection
    {
        return $this->growthFacilityTypes;
    }

    public function addGrowthFacilityType(self $growthFacilityType): self
    {
        if (!$this->growthFacilityTypes->contains($growthFacilityType)) {
            $this->growthFacilityTypes[] = $growthFacilityType;
            $growthFacilityType->setParentTerm($this);
        }

        return $this;
    }

    public function removeGrowthFacilityType(self $growthFacilityType): self
    {
        if ($this->growthFacilityTypes->removeElement($growthFacilityType)) {
            // set the owning side to null (unless already changed)
            if ($growthFacilityType->getParentTerm() === $this) {
                $growthFacilityType->setParentTerm(null);
            }
        }

        return $this;
    }
}
