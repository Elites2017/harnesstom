<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\MethodClassRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=MethodClassRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"method_class:read"}},
 *      denormalizationContext={"groups"={"method_class:write"}}
 * )
 */
class MethodClass
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="methodClasses")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=ObservationVariableMethod::class, mappedBy="methodClass")
     * @Groups({"method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $observationVariableMethods;

    public function __construct()
    {
        $this->observationVariableMethods = new ArrayCollection();
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
     * @return Collection<int, ObservationVariableMethod>
     */
    public function getObservationVariableMethods(): Collection
    {
        return $this->observationVariableMethods;
    }

    public function addObservationVariableMethod(ObservationVariableMethod $observationVariableMethod): self
    {
        if (!$this->observationVariableMethods->contains($observationVariableMethod)) {
            $this->observationVariableMethods[] = $observationVariableMethod;
            $observationVariableMethod->setMethodClass($this);
        }

        return $this;
    }

    public function removeObservationVariableMethod(ObservationVariableMethod $observationVariableMethod): self
    {
        if ($this->observationVariableMethods->removeElement($observationVariableMethod)) {
            // set the owning side to null (unless already changed)
            if ($observationVariableMethod->getMethodClass() === $this) {
                $observationVariableMethod->setMethodClass(null);
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
