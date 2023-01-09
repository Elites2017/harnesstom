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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $color_value;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shape_value;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fruit_weight_value;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fruit_fasciation_value;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fruit_shoulder_value;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $green_shoulder_value;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $puffiness_value;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pericarp_thickness;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fruit_firmness_value;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $brix_value;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fruit_load_value;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getColorValue(): ?string
    {
        return $this->color_value;
    }

    public function setColorValue(?string $color_value): self
    {
        $this->color_value = $color_value;

        return $this;
    }

    public function getShapeValue(): ?string
    {
        return $this->shape_value;
    }

    public function setShapeValue(?string $shape_value): self
    {
        $this->shape_value = $shape_value;

        return $this;
    }

    public function getFruitWeightValue(): ?string
    {
        return $this->fruit_weight_value;
    }

    public function setFruitWeightValue(?string $fruit_weight_value): self
    {
        $this->fruit_weight_value = $fruit_weight_value;

        return $this;
    }

    public function getFruitFasciationValue(): ?string
    {
        return $this->fruit_fasciation_value;
    }

    public function setFruitFasciationValue(?string $fruit_fasciation_value): self
    {
        $this->fruit_fasciation_value = $fruit_fasciation_value;

        return $this;
    }

    public function getFruitShoulderValue(): ?string
    {
        return $this->fruit_shoulder_value;
    }

    public function setFruitShoulderValue(?string $fruit_shoulder_value): self
    {
        $this->fruit_shoulder_value = $fruit_shoulder_value;

        return $this;
    }

    public function getGreenShoulderValue(): ?string
    {
        return $this->green_shoulder_value;
    }

    public function setGreenShoulderValue(?string $green_shoulder_value): self
    {
        $this->green_shoulder_value = $green_shoulder_value;

        return $this;
    }

    public function getPuffinessValue(): ?string
    {
        return $this->puffiness_value;
    }

    public function setPuffinessValue(?string $puffiness_value): self
    {
        $this->puffiness_value = $puffiness_value;

        return $this;
    }

    public function getPericarpThickness(): ?string
    {
        return $this->pericarp_thickness;
    }

    public function setPericarpThickness(?string $pericarp_thickness): self
    {
        $this->pericarp_thickness = $pericarp_thickness;

        return $this;
    }

    public function getFruitFirmnessValue(): ?string
    {
        return $this->fruit_firmness_value;
    }

    public function setFruitFirmnessValue(?string $fruit_firmness_value): self
    {
        $this->fruit_firmness_value = $fruit_firmness_value;

        return $this;
    }

    public function getBrixValue(): ?string
    {
        return $this->brix_value;
    }

    public function setBrixValue(?string $brix_value): self
    {
        $this->brix_value = $brix_value;

        return $this;
    }

    public function getFruitLoadValue(): ?string
    {
        return $this->fruit_load_value;
    }

    public function setFruitLoadValue(?string $fruit_load_value): self
    {
        $this->fruit_load_value = $fruit_load_value;

        return $this;
    }
}
