<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ObservationValueOriginalRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=ObservationValueOriginalRepository::class)
 */
class ObservationValueOriginal
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ObservationLevel::class, inversedBy="observationValueOriginals")
     */
    private $unitName;

    /**
     * @ORM\ManyToOne(targetEntity=ObservationVariable::class, inversedBy="observationValueOriginals")
     */
    private $observationVariableOriginal;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="observationValueOriginals")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUnitName(): ?ObservationLevel
    {
        return $this->unitName;
    }

    public function setUnitName(?ObservationLevel $unitName): self
    {
        $this->unitName = $unitName;

        return $this;
    }

    public function getObservationVariableOriginal(): ?ObservationVariable
    {
        return $this->observationVariableOriginal;
    }

    public function setObservationVariableOriginal(?ObservationVariable $observationVariableOriginal): self
    {
        $this->observationVariableOriginal = $observationVariableOriginal;

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

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->value;
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
}
