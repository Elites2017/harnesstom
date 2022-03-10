<?php

namespace App\Entity;

use App\Repository\QTLEpistatisticEffectRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QTLEpistatisticEffectRepository::class)
 */
class QTLEpistatisticEffect
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $createdBy;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $addEpi;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $r2Add;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $r2Epi;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $epistatisticEpi;

    /**
     * @ORM\ManyToOne(targetEntity=QTLVariant::class, inversedBy="qTLEpistatisticEffects")
     */
    private $qtlVariant1;

    /**
     * @ORM\ManyToOne(targetEntity=QTLVariant::class, inversedBy="qTLEpistatisticEffects")
     */
    private $qtlVariant2;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

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

    public function getAddEpi(): ?float
    {
        return $this->addEpi;
    }

    public function setAddEpi(?float $addEpi): self
    {
        $this->addEpi = $addEpi;

        return $this;
    }

    public function getR2Add(): ?float
    {
        return $this->r2Add;
    }

    public function setR2Add(?float $r2Add): self
    {
        $this->r2Add = $r2Add;

        return $this;
    }

    public function getR2Epi(): ?float
    {
        return $this->r2Epi;
    }

    public function setR2Epi(?float $r2Epi): self
    {
        $this->r2Epi = $r2Epi;

        return $this;
    }

    public function getEpistatisticEpi(): ?float
    {
        return $this->epistatisticEpi;
    }

    public function setEpistatisticEpi(?float $epistatisticEpi): self
    {
        $this->epistatisticEpi = $epistatisticEpi;

        return $this;
    }

    public function getQtlVariant1(): ?QTLVariant
    {
        return $this->qtlVariant1;
    }

    public function setQtlVariant1(?QTLVariant $qtlVariant1): self
    {
        $this->qtlVariant1 = $qtlVariant1;

        return $this;
    }

    public function getQtlVariant2(): ?QTLVariant
    {
        return $this->qtlVariant2;
    }

    public function setQtlVariant2(?QTLVariant $qtlVariant2): self
    {
        $this->qtlVariant2 = $qtlVariant2;

        return $this;
    }
}
