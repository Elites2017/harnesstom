<?php

namespace App\Entity;

use App\Repository\GermplasmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GermplasmRepository::class)
 */
class Germplasm
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
    private $germplasmID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $preprocessing;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=Program::class, inversedBy="germplasms")
     */
    private $program;

    /**
     * @ORM\ManyToOne(targetEntity=Accession::class, inversedBy="germplasms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $accession;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $instcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $maintainerNumb;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="germplasms")
     */
    private $createdBy;

    /**
     * @ORM\ManyToMany(targetEntity=Study::class, inversedBy="germplasms")
     */
    private $study;

    /**
     * @ORM\OneToMany(targetEntity=Cross::class, mappedBy="parent1")
     */
    private $crosses;

    /**
     * @ORM\OneToMany(targetEntity=ObservationLevel::class, mappedBy="germaplasm")
     */
    private $observationLevels;

    public function __construct()
    {
        $this->study = new ArrayCollection();
        $this->crosses = new ArrayCollection();
        $this->observationLevels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGermplasmID(): ?string
    {
        return $this->germplasmID;
    }

    public function setGermplasmID(string $germplasmID): self
    {
        $this->germplasmID = $germplasmID;

        return $this;
    }

    public function getPreprocessing(): ?string
    {
        return $this->preprocessing;
    }

    public function setPreprocessing(?string $preprocessing): self
    {
        $this->preprocessing = $preprocessing;

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

    public function getProgram(): ?Program
    {
        return $this->program;
    }

    public function setProgram(?Program $program): self
    {
        $this->program = $program;

        return $this;
    }

    public function getAccession(): ?Accession
    {
        return $this->accession;
    }

    public function setAccession(?Accession $accession): self
    {
        $this->accession = $accession;

        return $this;
    }

    public function getInstcode(): ?string
    {
        return $this->instcode;
    }

    public function setInstcode(string $instcode): self
    {
        $this->instcode = $instcode;

        return $this;
    }

    public function getMaintainerNumb(): ?string
    {
        return $this->maintainerNumb;
    }

    public function setMaintainerNumb(string $maintainerNumb): self
    {
        $this->maintainerNumb = $maintainerNumb;

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
     * @return Collection<int, Study>
     */
    public function getStudy(): Collection
    {
        return $this->study;
    }

    public function addStudy(Study $study): self
    {
        if (!$this->study->contains($study)) {
            $this->study[] = $study;
        }

        return $this;
    }

    public function removeStudy(Study $study): self
    {
        $this->study->removeElement($study);

        return $this;
    }

    /**
     * @return Collection<int, Cross>
     */
    public function getCrosses(): Collection
    {
        return $this->crosses;
    }

    public function addCross(Cross $cross): self
    {
        if (!$this->crosses->contains($cross)) {
            $this->crosses[] = $cross;
            $cross->setParent1($this);
        }

        return $this;
    }

    public function removeCross(Cross $cross): self
    {
        if ($this->crosses->removeElement($cross)) {
            // set the owning side to null (unless already changed)
            if ($cross->getParent1() === $this) {
                $cross->setParent1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ObservationLevel>
     */
    public function getObservationLevels(): Collection
    {
        return $this->observationLevels;
    }

    public function addObservationLevel(ObservationLevel $observationLevel): self
    {
        if (!$this->observationLevels->contains($observationLevel)) {
            $this->observationLevels[] = $observationLevel;
            $observationLevel->setGermaplasm($this);
        }

        return $this;
    }

    public function removeObservationLevel(ObservationLevel $observationLevel): self
    {
        if ($this->observationLevels->removeElement($observationLevel)) {
            // set the owning side to null (unless already changed)
            if ($observationLevel->getGermaplasm() === $this) {
                $observationLevel->setGermaplasm(null);
            }
        }

        return $this;
    }
}
