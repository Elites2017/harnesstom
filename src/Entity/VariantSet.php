<?php

namespace App\Entity;

use App\Repository\VariantSetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VariantSetRepository::class)
 */
class VariantSet
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
    private $value;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=Sample::class, inversedBy="variantSets")
     */
    private $sample;

    /**
     * @ORM\ManyToOne(targetEntity=Marker::class, inversedBy="variantSets")
     */
    private $marker;

    /**
     * @ORM\ManyToOne(targetEntity=VariantSetMetadata::class, inversedBy="variantSets")
     */
    private $variantSetMetadata;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=QTLStudy::class, mappedBy="variantSet")
     */
    private $qTLStudies;

    public function __construct()
    {
        $this->qTLStudies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

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

    public function getSample(): ?Sample
    {
        return $this->sample;
    }

    public function setSample(?Sample $sample): self
    {
        $this->sample = $sample;

        return $this;
    }

    public function getMarker(): ?Marker
    {
        return $this->marker;
    }

    public function setMarker(?Marker $marker): self
    {
        $this->marker = $marker;

        return $this;
    }

    public function getVariantSetMetadata(): ?VariantSetMetadata
    {
        return $this->variantSetMetadata;
    }

    public function setVariantSetMetadata(?VariantSetMetadata $variantSetMetadata): self
    {
        $this->variantSetMetadata = $variantSetMetadata;

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
            $qTLStudy->setVariantSet($this);
        }

        return $this;
    }

    public function removeQTLStudy(QTLStudy $qTLStudy): self
    {
        if ($this->qTLStudies->removeElement($qTLStudy)) {
            // set the owning side to null (unless already changed)
            if ($qTLStudy->getVariantSet() === $this) {
                $qTLStudy->setVariantSet(null);
            }
        }

        return $this;
    }
}
