<?php

namespace App\Entity;

use App\Repository\ObservationVariableRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ObservationVariableRepository::class)
 */
class ObservationVariable
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mainAbbreviaition;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=TraitClass::class, inversedBy="observationVariables")
     */
    private $trait;

    /**
     * @ORM\ManyToOne(targetEntity=Scale::class, inversedBy="observationVariables")
     */
    private $scale;

    /**
     * @ORM\ManyToOne(targetEntity=ObservationVariableMethod::class, inversedBy="observationVariables")
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
}