<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\GermplasmRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=GermplasmRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"germplasm:read"}},
 *      denormalizationContext={"groups"={"germplasm:write"}}
 * )
 */
class Germplasm
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"study:read", "germplasm:read", "accession:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"study:read", "germplasm:read", "accession:read"})
     * @SerializedName("germplasmDbId")
     */
    private $germplasmID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"study:read", "germplasm:read", "accession:read"})
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
     * @Groups({"study:read", "germplasm:read", "accession:read"})
     */
    private $program;

    /**
     * @ORM\ManyToOne(targetEntity=Accession::class, inversedBy="germplasms")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"study:read", "germplasm:read"})
     */
    private $accession;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"study:read", "germplasm:read", "accession:read"})
     */
    private $instcode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"study:read", "germplasm:read", "accession:read"})
     */
    private $maintainerNumb;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="germplasms")
     */
    private $createdBy;

    /**
     * @ORM\ManyToMany(targetEntity=Study::class, inversedBy="germplasms")
     * @Groups({"germplasm:read", "accession:read"})
     */
    private $study;

    /**
     * @ORM\OneToMany(targetEntity=Cross::class, mappedBy="parent1")
     * @Groups({"study:read", "germplasm:read"})
     */
    private $crosses;

    /**
     * @ORM\OneToMany(targetEntity=ObservationLevel::class, mappedBy="germaplasm")
     * @Groups({"study:read", "germplasm:read"})
     */
    private $observationLevels;

    /**
     * @ORM\OneToMany(targetEntity=Sample::class, mappedBy="germplasm")
     * @Groups({"study:read", "germplasm:read"})
     */
    private $samples;

    /**
     * @ORM\ManyToMany(targetEntity=Pedigree::class, mappedBy="germplasm")
     * @Groups({"study:read", "germplasm:read"})
     */
    private $pedigrees;

    /**
     * @ORM\OneToMany(targetEntity=QTLVariant::class, mappedBy="positiveAlleleParent")
     * @Groups({"study:read", "germplasm:read"})
     */
    private $qTLVariants;

    /**
     * @ORM\OneToMany(targetEntity=GermplasmStudyImage::class, mappedBy="GermplasmID")
     * @Groups({"germplasm:read"})
     */
    private $germplasmStudyImages;

    public function __construct()
    {
        $this->study = new ArrayCollection();
        $this->crosses = new ArrayCollection();
        $this->observationLevels = new ArrayCollection();
        $this->samples = new ArrayCollection();
        $this->pedigrees = new ArrayCollection();
        $this->qTLVariants = new ArrayCollection();
        $this->germplasmStudyImages = new ArrayCollection();
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

    /**
     * @return Collection<int, Sample>
     */
    public function getSamples(): Collection
    {
        return $this->samples;
    }

    public function addSample(Sample $sample): self
    {
        if (!$this->samples->contains($sample)) {
            $this->samples[] = $sample;
            $sample->setGermplasm($this);
        }

        return $this;
    }

    public function removeSample(Sample $sample): self
    {
        if ($this->samples->removeElement($sample)) {
            // set the owning side to null (unless already changed)
            if ($sample->getGermplasm() === $this) {
                $sample->setGermplasm(null);
            }
        }

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
            $pedigree->addGermplasm($this);
        }

        return $this;
    }

    public function removePedigree(Pedigree $pedigree): self
    {
        if ($this->pedigrees->removeElement($pedigree)) {
            $pedigree->removeGermplasm($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, QTLVariant>
     */
    public function getQTLVariants(): Collection
    {
        return $this->qTLVariants;
    }

    public function addQTLVariant(QTLVariant $qTLVariant): self
    {
        if (!$this->qTLVariants->contains($qTLVariant)) {
            $this->qTLVariants[] = $qTLVariant;
            $qTLVariant->setPositiveAlleleParent($this);
        }

        return $this;
    }

    public function removeQTLVariant(QTLVariant $qTLVariant): self
    {
        if ($this->qTLVariants->removeElement($qTLVariant)) {
            // set the owning side to null (unless already changed)
            if ($qTLVariant->getPositiveAlleleParent() === $this) {
                $qTLVariant->setPositiveAlleleParent(null);
            }
        }

        return $this;
    }

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->germplasmID;
    }

    /**
     * @return Collection<int, GermplasmStudyImage>
     */
    public function getGermplasmStudyImages(): Collection
    {
        return $this->germplasmStudyImages;
    }

    public function addGermplasmStudyImage(GermplasmStudyImage $germplasmStudyImage): self
    {
        if (!$this->germplasmStudyImages->contains($germplasmStudyImage)) {
            $this->germplasmStudyImages[] = $germplasmStudyImage;
            $germplasmStudyImage->setGermplasmID($this);
        }

        return $this;
    }

    public function removeGermplasmStudyImage(GermplasmStudyImage $germplasmStudyImage): self
    {
        if ($this->germplasmStudyImages->removeElement($germplasmStudyImage)) {
            // set the owning side to null (unless already changed)
            if ($germplasmStudyImage->getGermplasmID() === $this) {
                $germplasmStudyImage->setGermplasmID(null);
            }
        }

        return $this;
    }
}
