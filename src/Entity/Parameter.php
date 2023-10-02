<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ParameterRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=ParameterRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"parameter:read"}},
 *      denormalizationContext={"groups"={"parameter:write"}}
 * )
 */
class Parameter
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
     * @ORM\ManyToOne(targetEntity=FactorType::class, inversedBy="parameters")
     * @Groups({"parameter:read", "study:read"})
     */
    private $factorType;

    /**
     * @ORM\ManyToOne(targetEntity=Unit::class, inversedBy="parameters")
     * @Groups({"parameter:read", "study:read"})
     */
    private $unit;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="parameters")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=ParameterValue::class, mappedBy="paramter")
     */
    private $parameterValues;

    public function __construct()
    {
        $this->parameterValues = new ArrayCollection();
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

    public function getFactorType(): ?FactorType
    {
        return $this->factorType;
    }

    public function setFactorType(?FactorType $factorType): self
    {
        $this->factorType = $factorType;

        return $this;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

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

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * @return Collection<int, ParameterValue>
     */
    public function getParameterValues(): Collection
    {
        return $this->parameterValues;
    }

    public function addParameterValue(ParameterValue $parameterValue): self
    {
        if (!$this->parameterValues->contains($parameterValue)) {
            $this->parameterValues[] = $parameterValue;
            $parameterValue->setParamter($this);
        }

        return $this;
    }

    public function removeParameterValue(ParameterValue $parameterValue): self
    {
        if ($this->parameterValues->removeElement($parameterValue)) {
            // set the owning side to null (unless already changed)
            if ($parameterValue->getParamter() === $this) {
                $parameterValue->setParamter(null);
            }
        }

        return $this;
    }
}
