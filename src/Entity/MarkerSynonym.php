<?php

namespace App\Entity;

use App\Repository\MarkerSynonymRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MarkerSynonymRepository::class)
 */
class MarkerSynonym
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Marker::class, inversedBy="markerSynonyms")
     */
    private $markerName;

    /**
     * @ORM\Column(type="text")
     */
    private $synonymSource;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $markerSynonymId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="markerSynonyms")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarkerName(): ?Marker
    {
        return $this->markerName;
    }

    public function setMarkerName(?Marker $markerName): self
    {
        $this->markerName = $markerName;

        return $this;
    }

    public function getSynonymSource(): ?string
    {
        return $this->synonymSource;
    }

    public function setSynonymSource(string $synonymSource): self
    {
        $this->synonymSource = $synonymSource;

        return $this;
    }

    public function getMarkerSynonymId(): ?string
    {
        return $this->markerSynonymId;
    }

    public function setMarkerSynonymId(string $markerSynonymId): self
    {
        $this->markerSynonymId = $markerSynonymId;

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

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

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

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->markerSynonymId;
    }
}
