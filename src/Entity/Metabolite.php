<?php

namespace App\Entity;

use App\Repository\MetaboliteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MetaboliteRepository::class)
 */
class Metabolite
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Analyte::class, inversedBy="metabolites")
     */
    private $analyte;

    /**
     * @ORM\ManyToOne(targetEntity=MetabolicTrait::class, inversedBy="metabolites")
     */
    private $metabolicTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Scale::class, inversedBy="metabolites")
     */
    private $scale;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="metabolites")
     */
    private $createdBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnalyte(): ?Analyte
    {
        return $this->analyte;
    }

    public function setAnalyte(?Analyte $analyte): self
    {
        $this->analyte = $analyte;

        return $this;
    }

    public function getMetabolicTrait(): ?MetabolicTrait
    {
        return $this->metabolicTrait;
    }

    public function setMetabolicTrait(?MetabolicTrait $metabolicTrait): self
    {
        $this->metabolicTrait = $metabolicTrait;

        return $this;
    }

    public function getScale(): ?Scale
    {
        return $this->scale;
    }

    public function setScale(?Scale $scale): self
    {
        $this->scale = $scale;

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
}
