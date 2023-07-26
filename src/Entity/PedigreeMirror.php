<?php

namespace App\Entity;

use App\Repository\PedigreeMirrorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PedigreeMirrorRepository::class)
 */
class PedigreeMirror
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
    private $pedigreeEntryID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ancestorPedigreeEntryID;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=Cross::class)
     */
    private $pedigreeCross;

    /**
     * @ORM\ManyToOne(targetEntity=Generation::class)
     */
    private $generation;

    /**
     * @ORM\ManyToMany(targetEntity=Pedigree::class, mappedBy="allSiblings")
     */
    private $pedigrees;

    /**
     * @ORM\ManyToMany(targetEntity=Germplasm::class)
     */
    private $germplasm;

    public function __construct()
    {
        $this->pedigrees = new ArrayCollection();
        $this->germplasm = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPedigreeEntryID(): ?string
    {
        return $this->pedigreeEntryID;
    }

    public function setPedigreeEntryID(?string $pedigreeEntryID): self
    {
        $this->pedigreeEntryID = $pedigreeEntryID;

        return $this;
    }

    public function getAncestorPedigreeEntryID(): ?string
    {
        return $this->ancestorPedigreeEntryID;
    }

    public function setAncestorPedigreeEntryID(?string $ancestorPedigreeEntryID): self
    {
        $this->ancestorPedigreeEntryID = $ancestorPedigreeEntryID;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getPedigreeCross(): ?Cross
    {
        return $this->pedigreeCross;
    }

    public function setPedigreeCross(?Cross $pedigreeCross): self
    {
        $this->pedigreeCross = $pedigreeCross;

        return $this;
    }

    public function getGeneration(): ?Generation
    {
        return $this->generation;
    }

    public function setGeneration(?Generation $generation): self
    {
        $this->generation = $generation;

        return $this;
    }

    /**
     * @return Collection<int, Pedigree>
     */
    public function getPedigrees(): Collection
    {
        return $this->pedigrees;
    }

    public function addPedigree(Pedigree $pedigree): self
    {
        if (!$this->pedigrees->contains($pedigree)) {
            $this->pedigrees[] = $pedigree;
            $pedigree->addAllSibling($this);
        }

        return $this;
    }

    public function removePedigree(Pedigree $pedigree): self
    {
        if ($this->pedigrees->removeElement($pedigree)) {
            $pedigree->removeAllSibling($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Germplasm>
     */
    public function getGermplasm(): Collection
    {
        return $this->germplasm;
    }

    public function addGermplasm(Germplasm $germplasm): self
    {
        if (!$this->germplasm->contains($germplasm)) {
            $this->germplasm[] = $germplasm;
        }

        return $this;
    }

    public function removeGermplasm(Germplasm $germplasm): self
    {
        $this->germplasm->removeElement($germplasm);

        return $this;
    }
}
