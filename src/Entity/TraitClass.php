<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TraitClassRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=TraitClassRepository::class)
 * @ApiResource
 */
class TraitClass
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * 
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private $ontology_id;

    /**
     * @ORM\Column(type="text", nullable=true)
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="traitClasses")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=ObservationVariable::class, mappedBy="trait")
     */
    private $observationVariables;

    /**
     * @ORM\OneToMany(targetEntity=AttributeTraitValue::class, mappedBy="trait")
     */
    private $attributeTraitValues;

   /**
     * @ORM\ManyToMany(targetEntity=TraitClass::class, inversedBy="traitClasses")
     */
    private $parentTerm;

    /**
     * @ORM\ManyToMany(targetEntity=TraitClass::class, mappedBy="parentTerm")
     */
    private $traitClasses;

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
     * @ORM\ManyToMany(targetEntity=TraitClass::class, inversedBy="varOfTraitclasses")
     * @ORM\JoinTable(name="trait_class_variable_of")
     */
    private $varOf;

    /**
     * @ORM\ManyToMany(targetEntity=TraitClass::class, mappedBy="varOf")
     */
    private $varOfTraitclasses;

    /**
     * @ORM\OneToOne(targetEntity=ObservationVariable::class, mappedBy="variable", cascade={"persist", "remove"})
     */
    private $observationVariable;

    public function __construct()
    {
        $this->observationVariables = new ArrayCollection();
        $this->attributeTraitValues = new ArrayCollection();
        $this->traitClasses = new ArrayCollection();
        $this->parentTerm = new ArrayCollection();
        $this->varOf = new ArrayCollection();
        $this->varOfTraitclasses = new ArrayCollection();
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
            $observationVariable->setTrait($this);
        }

        return $this;
    }

    public function removeObservationVariable(ObservationVariable $observationVariable): self
    {
        if ($this->observationVariables->removeElement($observationVariable)) {
            // set the owning side to null (unless already changed)
            if ($observationVariable->getTrait() === $this) {
                $observationVariable->setTrait(null);
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
            $attributeTraitValue->setTrait($this);
        }

        return $this;
    }

    public function removeAttributeTraitValue(AttributeTraitValue $attributeTraitValue): self
    {
        if ($this->attributeTraitValues->removeElement($attributeTraitValue)) {
            // set the owning side to null (unless already changed)
            if ($attributeTraitValue->getTrait() === $this) {
                $attributeTraitValue->setTrait(null);
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
    public function getTraitClasses(): Collection
    {
        return $this->traitClasses;
    }

    public function addTraitClass(self $traitClass): self
    {
        if (!$this->traitClasses->contains($traitClass)) {
            $this->traitClasses[] = $traitClass;
            $traitClass->addParentTerm($this);
        }

        return $this;
    }

    public function removeTraitClass(self $traitClass): self
    {
        if ($this->traitClasses->removeElement($traitClass)) {
            $traitClass->removeParentTerm($this);
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

    /**
     * @return Collection<int, self>
     */
    public function getVarOf(): Collection
    {
        return $this->varOf;
    }

    public function addVarOf(self $varOf): self
    {
        if (!$this->varOf->contains($varOf)) {
            $this->varOf[] = $varOf;
        }

        return $this;
    }

    public function removeVarOf(self $varOf): self
    {
        $this->varOf->removeElement($varOf);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getVarOfTraitclasses(): Collection
    {
        return $this->varOfTraitclasses;
    }

    public function addVarOfTraitclass(self $varOfTraitclass): self
    {
        if (!$this->varOfTraitclasses->contains($varOfTraitclass)) {
            $this->varOfTraitclasses[] = $varOfTraitclass;
            $varOfTraitclass->addVarOf($this);
        }

        return $this;
    }

    public function removeVarOfTraitclass(self $varOfTraitclass): self
    {
        if ($this->varOfTraitclasses->removeElement($varOfTraitclass)) {
            $varOfTraitclass->removeVarOf($this);
        }

        return $this;
    }

    public function getObservationVariable(): ?ObservationVariable
    {
        return $this->observationVariable;
    }

    public function setObservationVariable(?ObservationVariable $observationVariable): self
    {
        // unset the owning side of the relation if necessary
        if ($observationVariable === null && $this->observationVariable !== null) {
            $this->observationVariable->setVariableId(null);
        }

        // set the owning side of the relation if necessary
        if ($observationVariable !== null && $observationVariable->getVariableId() !== $this) {
            $observationVariable->setVariableId($this);
        }

        $this->observationVariable = $observationVariable;

        return $this;
    }
}
