<?php

namespace App\Entity;

use App\Repository\AnnotationLevelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AnnotationLevelRepository::class)
 */
class AnnotationLevel
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
    private $label;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="annotationLevels")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Analyte::class, mappedBy="annotationLevel")
     */
    private $analytes;

    public function __construct()
    {
        $this->analytes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

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
            $analyte->setAnnotationLevel($this);
        }

        return $this;
    }

    public function removeAnalyte(Analyte $analyte): self
    {
        if ($this->analytes->removeElement($analyte)) {
            // set the owning side to null (unless already changed)
            if ($analyte->getAnnotationLevel() === $this) {
                $analyte->setAnnotationLevel(null);
            }
        }

        return $this;
    }

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->label;
    }
}
