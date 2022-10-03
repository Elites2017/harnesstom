<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\MetabolicTraitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=MetabolicTraitRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"metabolic_trait:read"}},
 *      denormalizationContext={"groups"={"metabolic_trait:write"}}
 * )
 */
class MetabolicTrait
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"metabolic_trait:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"metabolic_trait:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $name;

    /**
     * 
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private $ontology_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"metabolic_trait:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     * @Groups({"metabolic_trait:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $chebiMonoIsoTopicMass;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     * @Groups({"metabolic_trait:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $chebiMass;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @Groups({"metabolic_trait:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $synonym = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"metabolic_trait:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $chebiLink;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="metabolicTraits")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity=Metabolite::class, mappedBy="metabolicTrait")
     * @Groups({"metabolic_trait:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"
     * })
     */
    private $metabolites;

    /**
     * @ORM\OneToMany(targetEntity=AttributeTraitValue::class, mappedBy="metabolicTrait")
     * @Groups({"metabolic_trait:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $attributeTraitValues;

    /**
     * @ORM\ManyToMany(targetEntity=MetabolicTrait::class, inversedBy="metabolicTraits")
     */
    private $parentTerm;

    /**
     * @ORM\ManyToMany(targetEntity=MetabolicTrait::class, mappedBy="parentTerm")
     */
    private $metabolicTraits;

    public function __construct()
    {
        $this->metabolites = new ArrayCollection();
        $this->attributeTraitValues = new ArrayCollection();
        $this->metabolicTraits = new ArrayCollection();
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

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOntologyId(): ?string
    {
        return $this->ontology_id;
    }

    public function setOntologyId(?string $ontology_id): self
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

    public function getChebiMonoIsoTopicMass(): ?string
    {
        return $this->chebiMonoIsoTopicMass;
    }

    public function setChebiMonoIsoTopicMass(?string $chebiMonoIsoTopicMass): self
    {
        $this->chebiMonoIsoTopicMass = $chebiMonoIsoTopicMass;

        return $this;
    }

    public function getChebiMass(): ?string
    {
        return $this->chebiMass;
    }

    public function setChebiMass(?string $chebiMass): self
    {
        $this->chebiMass = $chebiMass;

        return $this;
    }

    public function getSynonym(): ?array
    {
        return $this->synonym;
    }

    public function setSynonym(?array $synonym): self
    {
        $this->synonym = $synonym;

        return $this;
    }

    public function getChebiLink(): ?string
    {
        return $this->chebiLink;
    }

    public function setChebiLink(?string $chebiLink): self
    {
        $this->chebiLink = $chebiLink;

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
            $metabolite->setMetabolicTrait($this);
        }

        return $this;
    }

    public function removeMetabolite(Metabolite $metabolite): self
    {
        if ($this->metabolites->removeElement($metabolite)) {
            // set the owning side to null (unless already changed)
            if ($metabolite->getMetabolicTrait() === $this) {
                $metabolite->setMetabolicTrait(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AttributeTraitValue>
     */
    public function getAttributeTraitValues(): Collection
    {
        return $this->attributeTraitValues;
    }

    public function addAttributeTraitValue(AttributeTraitValue $attributeTraitValue): self
    {
        if (!$this->attributeTraitValues->contains($attributeTraitValue)) {
            $this->attributeTraitValues[] = $attributeTraitValue;
            $attributeTraitValue->setMetabolicTrait($this);
        }

        return $this;
    }

    public function removeAttributeTraitValue(AttributeTraitValue $attributeTraitValue): self
    {
        if ($this->attributeTraitValues->removeElement($attributeTraitValue)) {
            // set the owning side to null (unless already changed)
            if ($attributeTraitValue->getMetabolicTrait() === $this) {
                $attributeTraitValue->setMetabolicTrait(null);
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
    public function getMetabolicTraits(): Collection
    {
        return $this->metabolicTraits;
    }

    public function addMetabolicTrait(self $metabolicTrait): self
    {
        if (!$this->metabolicTraits->contains($metabolicTrait)) {
            $this->metabolicTraits[] = $metabolicTrait;
            $metabolicTrait->addParentTerm($this);
        }

        return $this;
    }

    public function removeMetabolicTrait(self $metabolicTrait): self
    {
        if ($this->metabolicTraits->removeElement($metabolicTrait)) {
            $metabolicTrait->removeParentTerm($this);
        }

        return $this;
    }

}
