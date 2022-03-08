<?php

namespace App\Entity;

use App\Repository\MetabolicTraitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MetabolicTraitRepository::class)
 */
class MetabolicTrait
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ontology_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $chebiMonoIsoTopicMass;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $chebiMass;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $parentTerm;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $synonym = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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
     */
    private $metabolites;

    /**
     * @ORM\OneToMany(targetEntity=AttributeTraitValue::class, mappedBy="metabolicTrait")
     */
    private $attributeTraitValues;

    public function __construct()
    {
        $this->metabolites = new ArrayCollection();
        $this->attributeTraitValues = new ArrayCollection();
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

    public function getParentTerm(): ?string
    {
        return $this->parentTerm;
    }

    public function setParentTerm(?string $parentTerm): self
    {
        $this->parentTerm = $parentTerm;

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
}
