<?php

namespace App\Entity;

use App\Repository\CrossRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CrossRepository::class)
 * @ORM\Table(name="`cross`")
 */
class Cross
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
     * @ORM\Column(type="string", length=255)
     */
    private $parent1Type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $parent2Type;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $year;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $publicationReference = [];

    /**
     * @ORM\ManyToOne(targetEntity=Study::class, inversedBy="crosses")
     */
    private $study;

    /**
     * @ORM\ManyToOne(targetEntity=Institute::class, inversedBy="crosses")
     */
    private $institute;

    /**
     * @ORM\ManyToOne(targetEntity=BreedingMethod::class, inversedBy="crosses")
     */
    private $breedingMethod;

    /**
     * @ORM\ManyToOne(targetEntity=Germplasm::class, inversedBy="crosses")
     */
    private $parent1;

    /**
     * @ORM\ManyToOne(targetEntity=Germplasm::class, inversedBy="crosses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $parent2;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="crosses")
     */
    private $createdBy;

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

    public function getParent1Type(): ?string
    {
        return $this->parent1Type;
    }

    public function setParent1Type(string $parent1Type): self
    {
        $this->parent1Type = $parent1Type;

        return $this;
    }

    public function getParent2Type(): ?string
    {
        return $this->parent2Type;
    }

    public function setParent2Type(string $parent2Type): self
    {
        $this->parent2Type = $parent2Type;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
    {
        $this->year = $year;

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

    public function getPublicationReference(): ?array
    {
        return $this->publicationReference;
    }

    public function setPublicationReference(?array $publicationReference): self
    {
        $this->publicationReference = $publicationReference;

        return $this;
    }

    public function getStudy(): ?Study
    {
        return $this->study;
    }

    public function setStudy(?Study $study): self
    {
        $this->study = $study;

        return $this;
    }

    public function getInstitute(): ?Institute
    {
        return $this->institute;
    }

    public function setInstitute(?Institute $institute): self
    {
        $this->institute = $institute;

        return $this;
    }

    public function getBreedingMethod(): ?BreedingMethod
    {
        return $this->breedingMethod;
    }

    public function setBreedingMethod(?BreedingMethod $breedingMethod): self
    {
        $this->breedingMethod = $breedingMethod;

        return $this;
    }

    public function getParent1(): ?Germplasm
    {
        return $this->parent1;
    }

    public function setParent1(?Germplasm $parent1): self
    {
        $this->parent1 = $parent1;

        return $this;
    }

    public function getParent2(): ?Germplasm
    {
        return $this->parent2;
    }

    public function setParent2(?Germplasm $parent2): self
    {
        $this->parent2 = $parent2;

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
}