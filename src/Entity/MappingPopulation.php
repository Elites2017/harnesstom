<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\MappingPopulationRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=MappingPopulationRepository::class)
 * @ApiResource
 */
class MappingPopulation
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
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=Cross::class, inversedBy="mappingPopulations")
     */
    private $mappingPopulationCross;

    /**
     * @ORM\ManyToOne(targetEntity=Pedigree::class, inversedBy="mappingPopulations")
     */
    private $pedigreeGeneration;

    /**
     * @ORM\OneToMany(targetEntity=QTLStudy::class, mappedBy="mappingPopulation")
     */
    private $qTLStudies;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="mappingPopulations")
     */
    private $createdBy;

    public function __construct()
    {
        $this->qTLStudies = new ArrayCollection();
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

    public function getMappingPopulationCross(): ?Cross
    {
        return $this->mappingPopulationCross;
    }

    public function setMappingPopulationCross(?Cross $mappingPopulationCross): self
    {
        $this->mappingPopulationCross = $mappingPopulationCross;

        return $this;
    }

    public function getPedigreeGeneration(): ?Pedigree
    {
        return $this->pedigreeGeneration;
    }

    public function setPedigreeGeneration(?Pedigree $pedigreeGeneration): self
    {
        $this->pedigreeGeneration = $pedigreeGeneration;

        return $this;
    }

    /**
     * @return Collection<int, QTLStudy>
     */
    public function getQTLStudies(): Collection
    {
        return $this->qTLStudies;
    }

    public function addQTLStudy(QTLStudy $qTLStudy): self
    {
        if (!$this->qTLStudies->contains($qTLStudy)) {
            $this->qTLStudies[] = $qTLStudy;
            $qTLStudy->setMappingPopulation($this);
        }

        return $this;
    }

    public function removeQTLStudy(QTLStudy $qTLStudy): self
    {
        if ($this->qTLStudies->removeElement($qTLStudy)) {
            // set the owning side to null (unless already changed)
            if ($qTLStudy->getMappingPopulation() === $this) {
                $qTLStudy->setMappingPopulation(null);
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
