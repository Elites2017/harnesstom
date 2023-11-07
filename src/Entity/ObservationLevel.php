<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ObservationLevelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=ObservationLevelRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"observation_level:read"}},
 *      denormalizationContext={"groups"={"observation_level:write"}}
 * )
 */
class ObservationLevel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $unitname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     * @SerializedName("levelName")
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $blockNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $subBlockNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $plotNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $plantNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $replicate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $unitPosition;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $unitCoordinateX;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $unitCoordinateY;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $unitCoordinateXType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $unitCoordinateYType;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $lastUpdated;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="observationLevels")
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity=Germplasm::class, inversedBy="observationLevels")
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $germaplasm;

    /**
     * @ORM\ManyToOne(targetEntity=Study::class, inversedBy="observationLevels")
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read",
     * "metabolite:read"})
     */
    private $study;

    /**
     * @ORM\OneToMany(targetEntity=Sample::class, mappedBy="observationLevel")
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $samples;

    /**
     * @ORM\OneToMany(targetEntity=ObservationValue::class, mappedBy="observationLevel")
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    private $observationValues;

    private $obsLevelName;

    /**
     * @ORM\OneToMany(targetEntity=ObservationValueOriginal::class, mappedBy="unitName")
     */
    private $observationValueOriginals;

    public function __construct()
    {
        $this->samples = new ArrayCollection();
        $this->observationValues = new ArrayCollection();
        $this->observationValueOriginals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUnitname(): ?string
    {
        return $this->unitname;
    }

    public function setUnitname(string $unitname): self
    {
        $this->unitname = $unitname;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBlockNumber(): ?int
    {
        return $this->blockNumber;
    }

    public function setBlockNumber(?int $blockNumber): self
    {
        $this->blockNumber = $blockNumber;

        return $this;
    }

    public function getSubBlockNumber(): ?int
    {
        return $this->subBlockNumber;
    }

    public function setSubBlockNumber(?int $subBlockNumber): self
    {
        $this->subBlockNumber = $subBlockNumber;

        return $this;
    }

    public function getPlotNumber(): ?int
    {
        return $this->plotNumber;
    }

    public function setPlotNumber(?int $plotNumber): self
    {
        $this->plotNumber = $plotNumber;

        return $this;
    }

    public function getPlantNumber(): ?int
    {
        return $this->plantNumber;
    }

    public function setPlantNumber(?int $plantNumber): self
    {
        $this->plantNumber = $plantNumber;

        return $this;
    }

    public function getReplicate(): ?int
    {
        return $this->replicate;
    }

    public function setReplicate(?int $replicate): self
    {
        $this->replicate = $replicate;

        return $this;
    }

    public function getUnitPosition(): ?int
    {
        return $this->unitPosition;
    }

    public function setUnitPosition(?int $unitPosition): self
    {
        $this->unitPosition = $unitPosition;

        return $this;
    }

    public function getUnitCoordinateX(): ?string
    {
        return $this->unitCoordinateX;
    }

    public function setUnitCoordinateX(?string $unitCoordinateX): self
    {
        $this->unitCoordinateX = $unitCoordinateX;

        return $this;
    }

    public function getUnitCoordinateY(): ?string
    {
        return $this->unitCoordinateY;
    }

    public function setUnitCoordinateY(?string $unitCoordinateY): self
    {
        $this->unitCoordinateY = $unitCoordinateY;

        return $this;
    }

    public function getUnitCoordinateXType(): ?string
    {
        return $this->unitCoordinateXType;
    }

    public function setUnitCoordinateXType(?string $unitCoordinateXType): self
    {
        $this->unitCoordinateXType = $unitCoordinateXType;

        return $this;
    }

    public function getUnitCoordinateYType(): ?string
    {
        return $this->unitCoordinateYType;
    }

    public function setUnitCoordinateYType(?string $unitCoordinateYType): self
    {
        $this->unitCoordinateYType = $unitCoordinateYType;

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

    public function getLastUpdated(): ?\DateTimeInterface
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(?\DateTimeInterface $lastUpdated): self
    {
        $this->lastUpdated = $lastUpdated;

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

    public function getGermaplasm(): ?Germplasm
    {
        return $this->germaplasm;
    }

    public function setGermaplasm(?Germplasm $germaplasm): self
    {
        $this->germaplasm = $germaplasm;

        return $this;
    }

    public function getStudy(): ?Study
    {
        return $this->study;
    }

    public function setStudy(?Study $study): self
    {
        $this->study = $study;

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
            $sample->setObservationLevel($this);
        }

        return $this;
    }

    public function removeSample(Sample $sample): self
    {
        if ($this->samples->removeElement($sample)) {
            // set the owning side to null (unless already changed)
            if ($sample->getObservationLevel() === $this) {
                $sample->setObservationLevel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ObservationValue>
     */
    public function getObservationValues(): Collection
    {
        return $this->observationValues;
    }

    public function addObservationValue(ObservationValue $observationValue): self
    {
        if (!$this->observationValues->contains($observationValue)) {
            $this->observationValues[] = $observationValue;
            $observationValue->setObservationLevel($this);
        }

        return $this;
    }

    public function removeObservationValue(ObservationValue $observationValue): self
    {
        if ($this->observationValues->removeElement($observationValue)) {
            // set the owning side to null (unless already changed)
            if ($observationValue->getObservationLevel() === $this) {
                $observationValue->setObservationLevel(null);
            }
        }

        return $this;
    }

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->unitname;
    }

    /**
     * @return Collection<int, ObservationValueOriginal>
     */
    public function getObservationValueOriginals(): Collection
    {
        return $this->observationValueOriginals;
    }

    public function addObservationValueOriginal(ObservationValueOriginal $observationValueOriginal): self
    {
        if (!$this->observationValueOriginals->contains($observationValueOriginal)) {
            $this->observationValueOriginals[] = $observationValueOriginal;
            $observationValueOriginal->setUnitName($this);
        }

        return $this;
    }

    public function removeObservationValueOriginal(ObservationValueOriginal $observationValueOriginal): self
    {
        if ($this->observationValueOriginals->removeElement($observationValueOriginal)) {
            // set the owning side to null (unless already changed)
            if ($observationValueOriginal->getUnitName() === $this) {
                $observationValueOriginal->setUnitName(null);
            }
        }

        return $this;
    }

    // API September 2023 . BrAPI 2.1

    /**
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    public function getObservationUnitDbId() {
        return $this->unitname;
    }
    
    /**
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    public function getGermplasmDbId() {
        return $this->germaplasm->getGermplasmID();
    }

    /**
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    public function getGermplasmName() {
        return $this->germaplasm->getAccession()->getAccename();
    }

    /**
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    public function getLocationDbId() {
        return $this->study->getLocation() ? $this->study->getLocation()->getId() : null;
    }

    /**
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    public function getLocationName() {
        return $this->study->getLocation() ? $this->study->getLocation()->getName() : null;
    }

    /**
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    public function getProgramDbId() {
        return $this->germaplasm->getProgram() ? $this->germaplasm->getProgram()->getId() : null;
    }

    /**
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    public function getProgramName() {
        return $this->germaplasm->getProgram() ? $this->germaplasm->getProgram()->getName() : null;
    }

    /**
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    public function getStudyDbId() {
        return $this->study->getId();
    }

    /**
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    public function getStudyName() {
        return $this->study->getName();
    }

    /**
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    public function getTrialDbId() {
        return $this->study->getTrial() ? $this->study->getTrial()->getId() : null;
    }

    /**
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    public function getTrialName() {
        return $this->study->getTrial() ? $this->study->getTrial()->getName() : null;
    }
    
    /**
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    public function getObservationUnitName() {
        return $this->unitname;
    }

    /**
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    public function getObservationUnitPosition() {
        $obsUnitPosition = [
            "entryType" => "",
            "geoCoordinates" => [
                "geometry" => "",
                "type" => ""
                ],
            "observationLevel" => [
                "levelCode" => "",
                "levelName" => $this->name,
                "levelOrder" => ""
                ],
            "observationLevelRelationships" => [
                "levelName" => "",
                "levelOrder" => "",
                "observationUnitDbId" => "",
                ],
            "positionCoordinateX" => $this->unitCoordinateX,
            "positionCoordinateXType" => $this->unitCoordinateXType,
            "positionCoordinateY" => $this->unitCoordinateY,
            "positionCoordinateYType" => $this->unitCoordinateYType,
        ];

        return $obsUnitPosition;
    }

    /**
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    public function getObservations() {
        $obs = [];
        foreach ($this->observationValueOriginals as $key => $obsValueOri) {
            # code...
            $obs [] = [
                "observationDbId" => $obsValueOri->getId(),
                "germplasmDbId" => $obsValueOri->getUnitName()->getGermaplasm()->getGermplasmID(),
                "germplasmName" => $obsValueOri->getUnitName()->getGermaplasm()->getGermplasmName(),
                "observationUnitDbId" => $obsValueOri->getUnitName()->getUnitname(),
                "observationUnitName" => $obsValueOri->getUnitName()->getUnitname(),
                "observationVariableDbId" => $obsValueOri->getObservationVariableOriginal()->getVariable()->getOntologyId(),
                "observationVariableName" => $obsValueOri->getObservationVariableOriginal()->getName(),
                "season" => [
                    "seasonName" => $obsValueOri->getUnitname()->getStudy()->getSeason()->getName()
                    ],
                "studyDbId" => $obsValueOri->getUnitname()->getStudy()->getid(),
                "uploadedBy" => $obsValueOri->getCreatedBy()->getEmail(),
                "value" => $obsValueOri->getValue(),
            ];
        }
        return $obs;
    }

    /**
     * @Groups({"mls_status:read", "observation_level:read", "method_class:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read"})
     */
    public function getTreatments() {
        $fact = [
          "factor" => $this->study->getFactor() ? $this->study->getFactor()->getOntologyId() : null,
          "modality" => $this->study->getFactor() ? $this->study->getFactor()->getDescription() : null,  
        ];
        return $fact;
    }
}
