<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\StudyRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=StudyRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"study:read"}},
 *      denormalizationContext={"groups"={"study:write"}}
 * )
 */
class Study
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"study:read", "study_image:read"})
     * @SerializedName("studyDbId")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"study:read", "germplasm:read"})
     * @SerializedName("studyName")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"study:read"})
     * @SerializedName("studyCode")
     */
    private $abbreviation;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"study:read"})
     * @SerializedName("studyDescription")
     */
    private $description;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"study:read"})
     */
    private $startDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"study:read"})
     */
    private $endDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"study:read"})
     * @SerializedName("culturalPractices")
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
     * @Groups({"study:read"})
     */
    private $lastUpdated;

    /**
     * @ORM\ManyToOne(targetEntity=Trial::class, inversedBy="studies")
     * @Groups({"study:read"})
     */
    private $trial;

    /**
     * @ORM\ManyToOne(targetEntity=FactorType::class, inversedBy="studies")
     * @Groups({"study:read"})
     */
    private $factor;

    /**
     * @ORM\ManyToOne(targetEntity=Season::class, inversedBy="studies")
     * @Groups({"study:read"})
     */
    private $season;

    /**
     * @ORM\ManyToOne(targetEntity=Institute::class, inversedBy="studies")
     * @Groups({"study:read"})
     */
    private $institute;

    /**
     * @ORM\ManyToOne(targetEntity=Location::class, inversedBy="studies")
     * @Groups({"study:read"})
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity=GrowthFacilityType::class, inversedBy="studies")
     * @Groups({"study:read"})
     */
    private $growthFacility;

    /**
     * @ORM\ManyToOne(targetEntity=Parameter::class, inversedBy="studies")
     * @Groups({"location:read", "study:read"})
     * @SerializedName("parameter")
     */
    private $parameter;

    /**
     * @ORM\ManyToOne(targetEntity=ExperimentalDesignType::class, inversedBy="studies")
     * @Groups({"study:read"})
     */
    private $experimentalDesignType;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="studies")
     */
    private $createdBy;

    /**
     * @ORM\ManyToMany(targetEntity=Germplasm::class, mappedBy="study")
     * @Groups({"location:read"})
     */
    private $germplasms;

    /**
     * @ORM\OneToOne(targetEntity=StudyParameterValue::class, mappedBy="study", cascade={"persist", "remove"})
     * @Groups({"location:read", "study:read"})
     */
    private $studyParameterValue;

    /**
     * @ORM\OneToMany(targetEntity=Cross::class, mappedBy="study")
     * @Groups({"study:read"})
     */
    private $crosses;

    /**
     * @ORM\OneToMany(targetEntity=StudyImage::class, mappedBy="study")
     * @Groups({"location:read", "study:read"})
     */
    private $studyImages;

    /**
     * @ORM\OneToMany(targetEntity=ObservationLevel::class, mappedBy="study")
     * 
     */
    private $observationLevels;

    /**
     * @ORM\OneToMany(targetEntity=Sample::class, mappedBy="study")
     * @Groups({"study:read"})
     */
    private $samples;

    /**
     * @ORM\ManyToMany(targetEntity=GWAS::class, mappedBy="studyList")
     * @Groups({"study:read"})
     */
    private $gwas;

    /**
     * @ORM\OneToMany(targetEntity=GermplasmStudyImage::class, mappedBy="StudyID")
     * @Groups({"study:read"})
     */
    private $germplasmStudyImages;

    /**
     * @ORM\ManyToMany(targetEntity=QTLStudy::class, mappedBy="studyList")
     * @Groups({"study:read"})
     */
    private $qTLStudies;

    // API SECTION

    /**
     * @Groups({"study:read"})
     */
    private $studyPUI;

    /**
     * @Groups({"study:read"})
     */
    private $contacts;

    /**
     * @Groups({"study:read"})
     */
    private $experimentalDesign;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"study:read"})
     */
    private $observationUnitsDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"study:read"})
     */
    private $experimentalDesignDescription;

    public function __construct()
    {
        $this->germplasms = new ArrayCollection();
        $this->crosses = new ArrayCollection();
        $this->studyImages = new ArrayCollection();
        $this->observationLevels = new ArrayCollection();
        $this->samples = new ArrayCollection();
        $this->gwas = new ArrayCollection();
        $this->germplasmStudyImages = new ArrayCollection();
        $this->qTLStudies = new ArrayCollection();
        // API SECTION
        $this->contacts = new ArrayCollection();
        
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
            $observationLevel->setStudy($this);
        }

        return $this;
    }

    public function removeObservationLevel(ObservationLevel $observationLevel): self
    {
        if ($this->observationLevels->removeElement($observationLevel)) {
            // set the owning side to null (unless already changed)
            if ($observationLevel->getStudy() === $this) {
                $observationLevel->setStudy(null);
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
            $sample->setStudy($this);
        }

        return $this;
    }

    public function removeSample(Sample $sample): self
    {
        if ($this->samples->removeElement($sample)) {
            // set the owning side to null (unless already changed)
            if ($sample->getStudy() === $this) {
                $sample->setStudy(null);
            }
        }

        return $this;
    }

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * @return Collection<int, GWAS>
     */
    public function getGwas(): Collection
    {
        return $this->gwas;
    }

    public function addGwa(GWAS $gwa): self
    {
        if (!$this->gwas->contains($gwa)) {
            $this->gwas[] = $gwa;
            $gwa->addStudyList($this);
        }

        return $this;
    }

    public function removeGwa(GWAS $gwa): self
    {
        if ($this->gwas->removeElement($gwa)) {
            $gwa->removeStudyList($this);
        }

        return $this;
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
            $germplasmStudyImage->setStudyID($this);
        }

        return $this;
    }

    public function removeGermplasmStudyImage(GermplasmStudyImage $germplasmStudyImage): self
    {
        if ($this->germplasmStudyImages->removeElement($germplasmStudyImage)) {
            // set the owning side to null (unless already changed)
            if ($germplasmStudyImage->getStudyID() === $this) {
                $germplasmStudyImage->setStudyID(null);
            }
        }

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
            $qTLStudy->addStudyList($this);
        }

        return $this;
    }

    public function removeQTLStudy(QTLStudy $qTLStudy): self
    {
        if ($this->qTLStudies->removeElement($qTLStudy)) {
            $qTLStudy->removeStudyList($this);
        }

        return $this;
    }

    // API SECTION
    /**
     * @Groups({"study:read"})
     */
    public function getTrialDbId(){
        return $this->trial->getId();
    }

    /**
     * @Groups({"study:read"})
     */
    public function getTrialName(){
        return $this->trial->getName();
    }

    /**
     * @Groups({"study:read"})
     */
    public function getStudyPUI(){
        $this->studyPUI = "Study PUI";
        return $this->studyPUI;
    }

    /**
     * @Groups({"study:read"})
     */
    public function getLocationDbId(){
        return $this->location->getId();
    }

    /**
     * @Groups({"study:read"})
     */
    public function getLocationName(){
        return $this->location->getName();
    }

    /**
     * @Groups({"study:read"})
     */
    public function getLicense(){
        return $this->trial->getLicense();
    }

    /**
     * @Groups({"study:read"})
     */
    public function getContacts(): Array
    {
        $this->contacts = [
            "contactDbId" => $this->trial->getProgram()->getContact()->getOrcid(),
            "email" => $this->trial->getProgram()->getContact()->getPerson()->getEmailAddress(),
            "instituteName" => $this->trial->getProgram()->getContact()->getInstitute()->getName(),
            "name" => $this->trial->getProgram()->getContact()->getPerson()->getFirstName() ." ". $this->trial->getProgram()->getContact()->getPerson()->getMiddleName() ." ".$this->trial->getProgram()->getContact()->getPerson()->getLastName(),
            "orcid" => $this->trial->getProgram()->getContact()->getOrcid(),
            "type" => $this->trial->getProgram()->getContact()->getType()
        ];
        return $this->contacts;
    }

    /**
     * @Groups({"study:read"})
     */
    public function getenvironnmentParameters(): Array
    {
        $this->environnmentParameters = [
            "parameterName" => $this->parameter->getFactorType()->getName(),
            "description" => $this->parameter->getFactorType()->getDescription(),
            "parameterPUI" => "",
            "unit" => $this->parameter->getUnit()->getName(),
            "unitPUI" => $this->parameter->getUnit()->getOntologyId(),
            "value" => $this->parameter->getStudyParameterValues()->getValues()[0]->getValue(),
        ];
        return $this->environnmentParameters;
    }

    /**
     * @Groups({"study:read"})
     */
    public function getExperimentalDesign(){
        $this->experimentalDesign = [
            "PUI" => $this->experimentalDesignType->getOntologyId(),
            "description" => $this->experimentalDesignDescription
        ];
        return $this->experimentalDesign;
    }

    /**
     * @Groups({"study:read"})
     * @SerializedName("observationLevels")
     */
    public function getBraApiObservationLevels(){
        $this->brApiObservationLevels = [
            "levelName" => $this->getObservationLevels(),
        ];
        return $this->brApiObservationLevels;
    }

    public function getObservationUnitsDescription(): ?string
    {
        return $this->observationUnitsDescription;
    }

    public function setObservationUnitsDescription(?string $observationUnitsDescription): self
    {
        $this->observationUnitsDescription = $observationUnitsDescription;

        return $this;
    }

    public function getExperimentalDesignDescription(): ?string
    {
        return $this->experimentalDesignDescription;
    }

    public function setExperimentalDesignDescription(?string $experimentalDesignDescription): self
    {
        $this->experimentalDesignDescription = $experimentalDesignDescription;

        return $this;
    }

    /**
     * @Groups({"study:read"})
     */
    public function getDataLinks(){
        $this->datalinks = [
            "description" => ",..",
            "dataFormat" => "Image archives",
            "name" => $this->getStudyImages()
        ];
        return $this->datalinks;
    }
    
}
