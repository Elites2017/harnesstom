<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\ObservationVariableMethodRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=ObservationVariableMethodRepository::class)
 * @ApiResource
 */
class ObservationVariableMethod
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $instrument;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $software;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $publicationReference = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="observationVariableMethods")
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity=MethodClass::class, inversedBy="observationVariableMethods")
     */
    private $methodClass;

    /**
     * @ORM\OneToMany(targetEntity=ObservationVariable::class, mappedBy="observationVariableMethod")
     */
    private $observationVariables;

    /**
     * @ORM\OneToMany(targetEntity=Analyte::class, mappedBy="observationVariableMethod")
     */
    private $analytes;

    public function __construct()
    {
        $this->observationVariables = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getInstrument(): ?string
    {
        return $this->instrument;
    }

    public function setInstrument(?string $instrument): self
    {
        $this->instrument = $instrument;

        return $this;
    }

    public function getSoftware(): ?string
    {
        return $this->software;
    }

    public function setSoftware(?string $software): self
    {
        $this->software = $software;

        return $this;
    }

    public function getPublicationReference(): ?array
    {
        return $this->publicationReference;
    }

    public function setPublicationReference(?array $publicationReference): self
    {
        $this->publicationReference = $publicationReference;

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

    public function getMethodClass(): ?MethodClass
    {
        return $this->methodClass;
    }

    public function setMethodClass(?MethodClass $methodClass): self
    {
        $this->methodClass = $methodClass;

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
            $observationVariable->setObservationVariableMethod($this);
        }

        return $this;
    }

    public function removeObservationVariable(ObservationVariable $observationVariable): self
    {
        if ($this->observationVariables->removeElement($observationVariable)) {
            // set the owning side to null (unless already changed)
            if ($observationVariable->getObservationVariableMethod() === $this) {
                $observationVariable->setObservationVariableMethod(null);
            }
        }

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
            $analyte->setObservationVariableMethod($this);
        }

        return $this;
    }

    public function removeAnalyte(Analyte $analyte): self
    {
        if ($this->analytes->removeElement($analyte)) {
            // set the owning side to null (unless already changed)
            if ($analyte->getObservationVariableMethod() === $this) {
                $analyte->setObservationVariableMethod(null);
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
