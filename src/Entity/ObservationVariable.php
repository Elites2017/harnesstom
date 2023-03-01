<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\ObservationVariableRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=ObservationVariableRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"observation_variable:read"}},
 *      denormalizationContext={"groups"={"observation_variable:write"}}
 * )
 */
class ObservationVariable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"mls_status:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_value:read", "observation_variable:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"mls_status:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_value:read", "observation_variable:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"mls_status:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_value:read", "observation_variable:read"})
     */
    private $mainAbbreviaition;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"mls_status:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_value:read", "observation_variable:read"})
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=TraitClass::class, inversedBy="observationVariables")
     * @Groups({"mls_status:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_value:read", "observation_variable:read"})
     */
    private $trait;

    /**
     * @ORM\ManyToOne(targetEntity=Scale::class, inversedBy="observationVariables")
     * @Groups({"mls_status:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_value:read", "observation_variable:read"})
     */
    private $scale;

    /**
     * @ORM\ManyToOne(targetEntity=ObservationVariableMethod::class, inversedBy="observationVariables")
     * @Groups({"mls_status:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_value:read", "observation_variable:read"})
     */
    private $observationVariableMethod;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="observationVariables")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=ObservationValue::class, mappedBy="observationVariable")
     * @Groups({"mls_status:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_variable:read"})
     */
    private $observationValues;

    /**
     * @ORM\OneToMany(targetEntity=GWASVariant::class, mappedBy="observationVariable")
     * @Groups({"mls_status:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_value:read", "observation_variable:read"})
     */
    private $gWASVariants;

    /**
     * @ORM\OneToMany(targetEntity=QTLVariant::class, mappedBy="observationVariable")
     * @Groups({"mls_status:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_value:read", "observation_variable:read"})
     */
    private $qTLVariants;

    /**
     * @ORM\OneToOne(targetEntity=TraitClass::class, inversedBy="observationVariable", cascade={"persist", "remove"})
     */
    private $variable;

    /**
     * @ORM\OneToMany(targetEntity=ObservationValueOriginal::class, mappedBy="observationVariableOriginal")
     */
    private $observationValueOriginals;

    public function __construct()
    {
        $this->observationValues = new ArrayCollection();
        $this->gWASVariants = new ArrayCollection();
        $this->qTLVariants = new ArrayCollection();
        $this->observationValueOriginals = new ArrayCollection();
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

    public function getMainAbbreviaition(): ?string
    {
        return $this->mainAbbreviaition;
    }

    public function setMainAbbreviaition(?string $mainAbbreviaition): self
    {
        $this->mainAbbreviaition = $mainAbbreviaition;

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

    public function getTrait(): ?TraitClass
    {
        return $this->trait;
    }

    public function setTrait(?TraitClass $trait): self
    {
        $this->trait = $trait;

        return $this;
    }

    public function getScale(): ?Scale
    {
        return $this->scale;
    }

    public function setScale(?Scale $scale): self
    {
        $this->scale = $scale;

        return $this;
    }

    public function getObservationVariableMethod(): ?ObservationVariableMethod
    {
        return $this->observationVariableMethod;
    }

    public function setObservationVariableMethod(?ObservationVariableMethod $observationVariableMethod): self
    {
        $this->observationVariableMethod = $observationVariableMethod;

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
     * @return Collection<int, ObservationValue>
     */
    public function getObservationValues(): Collection
    {
        return $this->observationValues;
    }

    public function addObservationValue(ObservationValue $observationValue): self
    {
        if (!$this->observationValues->contains($observationValue)) {
            $this->observationValues[] = $observationValue;
            $observationValue->setObservationVariable($this);
        }

        return $this;
    }

    public function removeObservationValue(ObservationValue $observationValue): self
    {
        if ($this->observationValues->removeElement($observationValue)) {
            // set the owning side to null (unless already changed)
            if ($observationValue->getObservationVariable() === $this) {
                $observationValue->setObservationVariable(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GWASVariant>
     */
    public function getGWASVariants(): Collection
    {
        return $this->gWASVariants;
    }

    public function addGWASVariant(GWASVariant $gWASVariant): self
    {
        if (!$this->gWASVariants->contains($gWASVariant)) {
            $this->gWASVariants[] = $gWASVariant;
            $gWASVariant->setObservationVariable($this);
        }

        return $this;
    }

    public function removeGWASVariant(GWASVariant $gWASVariant): self
    {
        if ($this->gWASVariants->removeElement($gWASVariant)) {
            // set the owning side to null (unless already changed)
            if ($gWASVariant->getObservationVariable() === $this) {
                $gWASVariant->setObservationVariable(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QTLVariant>
     */
    public function getQTLVariants(): Collection
    {
        return $this->qTLVariants;
    }

    public function addQTLVariant(QTLVariant $qTLVariant): self
    {
        if (!$this->qTLVariants->contains($qTLVariant)) {
            $this->qTLVariants[] = $qTLVariant;
            $qTLVariant->setObservationVariable($this);
        }

        return $this;
    }

    public function removeQTLVariant(QTLVariant $qTLVariant): self
    {
        if ($this->qTLVariants->removeElement($qTLVariant)) {
            // set the owning side to null (unless already changed)
            if ($qTLVariant->getObservationVariable() === $this) {
                $qTLVariant->setObservationVariable(null);
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

    public function getVariable(): ?TraitClass
    {
        return $this->variable;
    }

    public function setVariable(?TraitClass $variable): self
    {
        $this->variable = $variable;

        return $this;
    }

    /**
     * @return Collection<int, ObservationValueOriginal>
     */
    public function getObservationValueOriginals(): Collection
    {
        return $this->observationValueOriginals;
    }

    public function addObservationValueOriginal(ObservationValueOriginal $observationValueOriginal): self
    {
        if (!$this->observationValueOriginals->contains($observationValueOriginal)) {
            $this->observationValueOriginals[] = $observationValueOriginal;
            $observationValueOriginal->setObservationVariableOriginal($this);
        }

        return $this;
    }

    public function removeObservationValueOriginal(ObservationValueOriginal $observationValueOriginal): self
    {
        if ($this->observationValueOriginals->removeElement($observationValueOriginal)) {
            // set the owning side to null (unless already changed)
            if ($observationValueOriginal->getObservationVariableOriginal() === $this) {
                $observationValueOriginal->setObservationVariableOriginal(null);
            }
        }

        return $this;
    }
}
