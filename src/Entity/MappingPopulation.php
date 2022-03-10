<?php

namespace App\Entity;

use App\Repository\MappingPopulationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MappingPopulationRepository::class)
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity=Cross::class, inversedBy="mappingPopulations")
     */
    private $mappingPopulationCross;

    /**
     * @ORM\ManyToOne(targetEntity=Pedigree::class, inversedBy="mappingPopulations")
     */
    private $pedigreeGeneration;

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

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

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
}
