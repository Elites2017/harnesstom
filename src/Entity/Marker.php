<?php

namespace App\Entity;

use App\Repository\MarkerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MarkerRepository::class)
 */
class Marker
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
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $linkageGroupName;

    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $start;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $end;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $refAllele;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $altAllele = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $primerName1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $primerSeq1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $primerName2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $primerSeq2;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=GenotypingPlatform::class, inversedBy="markers")
     */
    private $genotypingPlatform;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="markers")
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLinkageGroupName(): ?string
    {
        return $this->linkageGroupName;
    }

    public function setLinkageGroupName(string $linkageGroupName): self
    {
        $this->linkageGroupName = $linkageGroupName;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getStart(): ?int
    {
        return $this->start;
    }

    public function setStart(?int $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?int
    {
        return $this->end;
    }

    public function setEnd(?int $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getRefAllele(): ?string
    {
        return $this->refAllele;
    }

    public function setRefAllele(?string $refAllele): self
    {
        $this->refAllele = $refAllele;

        return $this;
    }

    public function getAltAllele(): ?array
    {
        return $this->altAllele;
    }

    public function setAltAllele(?array $altAllele): self
    {
        $this->altAllele = $altAllele;

        return $this;
    }

    public function getPrimerName1(): ?string
    {
        return $this->primerName1;
    }

    public function setPrimerName1(?string $primerName1): self
    {
        $this->primerName1 = $primerName1;

        return $this;
    }

    public function getPrimerSeq1(): ?string
    {
        return $this->primerSeq1;
    }

    public function setPrimerSeq1(?string $primerSeq1): self
    {
        $this->primerSeq1 = $primerSeq1;

        return $this;
    }

    public function getPrimerName2(): ?string
    {
        return $this->primerName2;
    }

    public function setPrimerName2(?string $primerName2): self
    {
        $this->primerName2 = $primerName2;

        return $this;
    }

    public function getPrimerSeq2(): ?string
    {
        return $this->primerSeq2;
    }

    public function setPrimerSeq2(?string $primerSeq2): self
    {
        $this->primerSeq2 = $primerSeq2;

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

    public function getGenotypingPlatform(): ?GenotypingPlatform
    {
        return $this->genotypingPlatform;
    }

    public function setGenotypingPlatform(?GenotypingPlatform $genotypingPlatform): self
    {
        $this->genotypingPlatform = $genotypingPlatform;

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
