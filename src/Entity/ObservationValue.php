<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ObservationValueRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=ObservationValueRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"observation_value:read"}},
 *      denormalizationContext={"groups"={"observation_value:write"}}
 * )
 */
class ObservationValue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_value:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_value:read"})
     */
    private $value;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=ObservationLevel::class, inversedBy="observationValues")
     * @Groups({"mls_status:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_value:read"})
     */
    private $observationLevel;

    /**
     * @ORM\ManyToOne(targetEntity=ObservationVariable::class, inversedBy="observationValues")
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_value:read"})
     */
    private $observationVariable;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="observationValues")
     */
    private $createdBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

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

    public function getObservationLevel(): ?ObservationLevel
    {
        return $this->observationLevel;
    }

    public function setObservationLevel(?ObservationLevel $observationLevel): self
    {
        $this->observationLevel = $observationLevel;

        return $this;
    }

    public function getObservationVariable(): ?ObservationVariable
    {
        return $this->observationVariable;
    }

    public function setObservationVariable(?ObservationVariable $observationVariable): self
    {
        $this->observationVariable = $observationVariable;

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
        return (string) $this->value;
    }
}
