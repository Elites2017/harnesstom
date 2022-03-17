<?php

namespace App\Entity;

use App\Repository\ParameterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ParameterRepository::class)
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
     */
    private $factorType;

    /**
     * @ORM\ManyToOne(targetEntity=Unit::class, inversedBy="parameters")
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
     * @ORM\OneToMany(targetEntity=Study::class, mappedBy="parameter")
     */
    private $studies;

    /**
     * @ORM\OneToMany(targetEntity=StudyParameterValue::class, mappedBy="parameter")
     */
    private $studyParameterValues;

    public function __construct()
    {
        $this->studies = new ArrayCollection();
        $this->studyParameterValues = new ArrayCollection();
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

    /**
     * @return Collection<int, Study>
     */
    public function getStudies(): Collection
    {
        return $this->studies;
    }

    public function addStudy(Study $study): self
    {
        if (!$this->studies->contains($study)) {
            $this->studies[] = $study;
            $study->setParameter($this);
        }

        return $this;
    }

    public function removeStudy(Study $study): self
    {
        if ($this->studies->removeElement($study)) {
            // set the owning side to null (unless already changed)
            if ($study->getParameter() === $this) {
                $study->setParameter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StudyParameterValue>
     */
    public function getStudyParameterValues(): Collection
    {
        return $this->studyParameterValues;
    }

    public function addStudyParameterValue(StudyParameterValue $studyParameterValue): self
    {
        if (!$this->studyParameterValues->contains($studyParameterValue)) {
            $this->studyParameterValues[] = $studyParameterValue;
            $studyParameterValue->setParameter($this);
        }

        return $this;
    }

    public function removeStudyParameterValue(StudyParameterValue $studyParameterValue): self
    {
        if ($this->studyParameterValues->removeElement($studyParameterValue)) {
            // set the owning side to null (unless already changed)
            if ($studyParameterValue->getParameter() === $this) {
                $studyParameterValue->setParameter(null);
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
