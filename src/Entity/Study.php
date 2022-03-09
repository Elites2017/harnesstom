<?php

namespace App\Entity;

use App\Repository\StudyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StudyRepository::class)
 */
class Study
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
     * @ORM\Column(type="string", length=255)
     */
    private $abbreviation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $startDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $culturalPractice;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastUpdated;

    /**
     * @ORM\ManyToOne(targetEntity=Trial::class, inversedBy="studies")
     */
    private $trial;

    /**
     * @ORM\ManyToOne(targetEntity=FactorType::class, inversedBy="studies")
     */
    private $factor;

    /**
     * @ORM\ManyToOne(targetEntity=Season::class, inversedBy="studies")
     */
    private $season;

    /**
     * @ORM\ManyToOne(targetEntity=Institute::class, inversedBy="studies")
     */
    private $institute;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class, inversedBy="studies")
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity=GrowthFacilityType::class, inversedBy="studies")
     */
    private $growthFacility;

    /**
     * @ORM\ManyToOne(targetEntity=Parameter::class, inversedBy="studies")
     */
    private $parameter;

    /**
     * @ORM\ManyToOne(targetEntity=ExperimentalDesignType::class, inversedBy="studies")
     */
    private $experimentalDesignType;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="studies")
     */
    private $createdBy;

    /**
     * @ORM\ManyToMany(targetEntity=Germplasm::class, mappedBy="study")
     */
    private $germplasms;

    /**
     * @ORM\OneToOne(targetEntity=StudyParameterValue::class, mappedBy="study", cascade={"persist", "remove"})
     */
    private $studyParameterValue;

    /**
     * @ORM\OneToMany(targetEntity=Cross::class, mappedBy="study")
     */
    private $crosses;

    /**
     * @ORM\OneToMany(targetEntity=StudyImage::class, mappedBy="study")
     */
    private $studyImages;

    public function __construct()
    {
        $this->germplasms = new ArrayCollection();
        $this->crosses = new ArrayCollection();
        $this->studyImages = new ArrayCollection();
    }

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

    public function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(string $abbreviation): self
    {
        $this->abbreviation = $abbreviation;

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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCulturalPractice(): ?string
    {
        return $this->culturalPractice;
    }

    public function setCulturalPractice(?string $culturalPractice): self
    {
        $this->culturalPractice = $culturalPractice;

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

    public function getLastUpdated(): ?\DateTimeInterface
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(?\DateTimeInterface $lastUpdated): self
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }

    public function getTrial(): ?Trial
    {
        return $this->trial;
    }

    public function setTrial(?Trial $trial): self
    {
        $this->trial = $trial;

        return $this;
    }

    public function getFactor(): ?FactorType
    {
        return $this->factor;
    }

    public function setFactor(?FactorType $factor): self
    {
        $this->factor = $factor;

        return $this;
    }

    public function getSeason(): ?Season
    {
        return $this->season;
    }

    public function setSeason(?Season $season): self
    {
        $this->season = $season;

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

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getGrowthFacility(): ?GrowthFacilityType
    {
        return $this->growthFacility;
    }

    public function setGrowthFacility(?GrowthFacilityType $growthFacility): self
    {
        $this->growthFacility = $growthFacility;

        return $this;
    }

    public function getParameter(): ?Parameter
    {
        return $this->parameter;
    }

    public function setParameter(?Parameter $parameter): self
    {
        $this->parameter = $parameter;

        return $this;
    }

    public function getExperimentalDesignType(): ?ExperimentalDesignType
    {
        return $this->experimentalDesignType;
    }

    public function setExperimentalDesignType(?ExperimentalDesignType $experimentalDesignType): self
    {
        $this->experimentalDesignType = $experimentalDesignType;

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
     * @return Collection<int, Germplasm>
     */
    public function getGermplasms(): Collection
    {
        return $this->germplasms;
    }

    public function addGermplasm(Germplasm $germplasm): self
    {
        if (!$this->germplasms->contains($germplasm)) {
            $this->germplasms[] = $germplasm;
            $germplasm->addStudy($this);
        }

        return $this;
    }

    public function removeGermplasm(Germplasm $germplasm): self
    {
        if ($this->germplasms->removeElement($germplasm)) {
            $germplasm->removeStudy($this);
        }

        return $this;
    }

    public function getStudyParameterValue(): ?StudyParameterValue
    {
        return $this->studyParameterValue;
    }

    public function setStudyParameterValue(StudyParameterValue $studyParameterValue): self
    {
        // set the owning side of the relation if necessary
        if ($studyParameterValue->getStudy() !== $this) {
            $studyParameterValue->setStudy($this);
        }

        $this->studyParameterValue = $studyParameterValue;

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
            $cross->setStudy($this);
        }

        return $this;
    }

    public function removeCross(Cross $cross): self
    {
        if ($this->crosses->removeElement($cross)) {
            // set the owning side to null (unless already changed)
            if ($cross->getStudy() === $this) {
                $cross->setStudy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StudyImage>
     */
    public function getStudyImages(): Collection
    {
        return $this->studyImages;
    }

    public function addStudyImage(StudyImage $studyImage): self
    {
        if (!$this->studyImages->contains($studyImage)) {
            $this->studyImages[] = $studyImage;
            $studyImage->setStudy($this);
        }

        return $this;
    }

    public function removeStudyImage(StudyImage $studyImage): self
    {
        if ($this->studyImages->removeElement($studyImage)) {
            // set the owning side to null (unless already changed)
            if ($studyImage->getStudy() === $this) {
                $studyImage->setStudy(null);
            }
        }

        return $this;
    }
}