<?php

namespace App\Entity;

use App\Repository\MethodClassRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MethodClassRepository::class)
 */
class MethodClass
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
}
