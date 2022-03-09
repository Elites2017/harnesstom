<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=Country::class, mappedBy="createdBy")
     */
    private $countries;

    /**
     * @ORM\OneToMany(targetEntity=Crop::class, mappedBy="createdBy")
     */
    private $crops;

    /**
     * @ORM\OneToMany(targetEntity=Season::class, mappedBy="createdBy")
     */
    private $seasons;

    /**
     * @ORM\OneToMany(targetEntity=FactorType::class, mappedBy="createdBy")
     */
    private $factorTypes;

    /**
     * @ORM\OneToMany(targetEntity=GrowthFacilityType::class, mappedBy="createdBy")
     */
    private $growthFacilityTypes;

    /**
     * @ORM\OneToMany(targetEntity=ExperimentalDesignType::class, mappedBy="createdBy")
     */
    private $experimentalDesignTypes;

    /**
     * @ORM\OneToMany(targetEntity=Unit::class, mappedBy="createdBy")
     */
    private $units;

    /**
     * @ORM\OneToMany(targetEntity=TrialType::class, mappedBy="createdBy")
     */
    private $trialTypes;

    /**
     * @ORM\OneToMany(targetEntity=DataType::class, mappedBy="createdBy")
     */
    private $dataTypes;

    /**
     * @ORM\OneToMany(targetEntity=MetaboliteClass::class, mappedBy="createdBy")
     */
    private $metaboliteClasses;

    /**
     * @ORM\OneToMany(targetEntity=IdentificationLevel::class, mappedBy="createdBy")
     */
    private $identificationLevels;

    /**
     * @ORM\OneToMany(targetEntity=AnnotationLevel::class, mappedBy="createdBy")
     */
    private $annotationLevels;

    /**
     * @ORM\OneToMany(targetEntity=MetabolicTrait::class, mappedBy="createdBy")
     */
    private $metabolicTraits;

    /**
     * @ORM\OneToMany(targetEntity=MethodClass::class, mappedBy="createdBy")
     */
    private $methodClasses;

    /**
     * @ORM\OneToMany(targetEntity=ScaleCategory::class, mappedBy="createdBy")
     */
    private $scaleCategories;

    /**
     * @ORM\OneToMany(targetEntity=Taxonomy::class, mappedBy="createdBy")
     */
    private $taxonomies;

    /**
     * @ORM\OneToMany(targetEntity=MLSStatus::class, mappedBy="createdBy")
     */
    private $mLSStatuses;

    /**
     * @ORM\OneToMany(targetEntity=StorageType::class, mappedBy="createdBy")
     */
    private $storageTypes;

    /**
     * @ORM\OneToMany(targetEntity=AttributeCategory::class, mappedBy="createdBy")
     */
    private $attributeCategories;

    /**
     * @ORM\OneToMany(targetEntity=BreedingMethod::class, mappedBy="createdBy")
     */
    private $breedingMethods;

    /**
     * @ORM\OneToMany(targetEntity=TraitClass::class, mappedBy="createdBy")
     */
    private $traitClasses;

    /**
     * @ORM\OneToMany(targetEntity=CollectingSource::class, mappedBy="createdBy")
     */
    private $collectingSources;

    /**
     * @ORM\OneToMany(targetEntity=BiologicalStatus::class, mappedBy="createdBy")
     */
    private $biologicalStatuses;

    /**
     * @ORM\OneToMany(targetEntity=QTLMethod::class, mappedBy="createdBy")
     */
    private $qTLMethods;

    /**
     * @ORM\OneToMany(targetEntity=ThresholdMethod::class, mappedBy="createdBy")
     */
    private $thresholdMethods;

    /**
     * @ORM\OneToMany(targetEntity=Software::class, mappedBy="createdBy")
     */
    private $software;

    /**
     * @ORM\OneToMany(targetEntity=CiCriteria::class, mappedBy="createdBy")
     */
    private $ciCriterias;

    /**
     * @ORM\OneToMany(targetEntity=QTLStatistic::class, mappedBy="createdBy")
     */
    private $qTLStatistics;

    /**
     * @ORM\OneToMany(targetEntity=GWASModel::class, mappedBy="createdBy")
     */
    private $gWASModels;

    /**
     * @ORM\OneToMany(targetEntity=GeneticTestingModel::class, mappedBy="createdBy")
     */
    private $geneticTestingModels;

    /**
     * @ORM\OneToMany(targetEntity=GWASStatTest::class, mappedBy="createdBy")
     */
    private $gWASStatTests;

    /**
     * @ORM\OneToMany(targetEntity=AllelicEffectEstimator::class, mappedBy="createdBy")
     */
    private $allelicEffectEstimators;

    /**
     * @ORM\OneToMany(targetEntity=StructureMethod::class, mappedBy="createdBy")
     */
    private $structureMethods;

    /**
     * @ORM\OneToMany(targetEntity=KinshipAlgorithm::class, mappedBy="createdBy")
     */
    private $kinshipAlgorithms;

    /**
     * @ORM\OneToMany(targetEntity=TraitProcessing::class, mappedBy="createdBy")
     */
    private $traitProcessings;

    /**
     * @ORM\OneToMany(targetEntity=SequencingType::class, mappedBy="createdBy")
     */
    private $sequencingTypes;

    /**
     * @ORM\OneToMany(targetEntity=SequencingInstrument::class, mappedBy="createdBy")
     */
    private $sequencingInstruments;

    /**
     * @ORM\OneToMany(targetEntity=VarCallSoftware::class, mappedBy="createdBy")
     */
    private $varCallSoftware;

    /**
     * @ORM\OneToMany(targetEntity=AnatomicalEntity::class, mappedBy="createdBy")
     */
    private $anatomicalEntities;

    /**
     * @ORM\OneToMany(targetEntity=DevelopmentalStage::class, mappedBy="createdBy")
     */
    private $developmentalStages;

    /**
     * @ORM\OneToMany(targetEntity=Location::class, mappedBy="createdBy")
     */
    private $locations;

    /**
     * @ORM\OneToMany(targetEntity=Attribute::class, mappedBy="createdBy")
     */
    private $attributes;

    /**
     * @ORM\OneToOne(targetEntity=Person::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $person;

    /**
     * @ORM\OneToMany(targetEntity=Person::class, mappedBy="createdBy")
     */
    private $people;

    /**
     * @ORM\OneToMany(targetEntity=Parameter::class, mappedBy="createdBy")
     */
    private $parameters;

    /**
     * @ORM\OneToMany(targetEntity=Scale::class, mappedBy="createdBy")
     */
    private $scales;

    /**
     * @ORM\OneToMany(targetEntity=ObservationVariableMethod::class, mappedBy="createdBy")
     */
    private $observationVariableMethods;

    /**
     * @ORM\OneToMany(targetEntity=Institute::class, mappedBy="createdBy")
     */
    private $institutes;

    /**
     * @ORM\OneToMany(targetEntity=Contact::class, mappedBy="createdBy")
     */
    private $contacts;

    /**
     * @ORM\OneToMany(targetEntity=CollectingMission::class, mappedBy="createdBy")
     */
    private $collectingMissions;

    /**
     * @ORM\OneToMany(targetEntity=ObservationVariable::class, mappedBy="createdBy")
     */
    private $observationVariables;

    /**
     * @ORM\OneToMany(targetEntity=AnalyteClass::class, mappedBy="createdBy")
     */
    private $analyteClasses;

    /**
     * @ORM\OneToMany(targetEntity=Enzyme::class, mappedBy="createdBy")
     */
    private $enzymes;

    /**
     * @ORM\OneToMany(targetEntity=GenotypingPlatform::class, mappedBy="createdBy")
     */
    private $genotypingPlatforms;

    /**
     * @ORM\OneToMany(targetEntity=Marker::class, mappedBy="createdBy")
     */
    private $markers;

    /**
     * @ORM\OneToMany(targetEntity=VariantSetMetadata::class, mappedBy="createdBy")
     */
    private $variantSetMetadata;

    /**
     * @ORM\OneToMany(targetEntity=AnalyteFlavorHealth::class, mappedBy="createdBy")
     */
    private $analyteFlavorHealths;

    /**
     * @ORM\OneToMany(targetEntity=Analyte::class, mappedBy="createdBy")
     */
    private $analytes;

    /**
     * @ORM\OneToMany(targetEntity=Program::class, mappedBy="createdBy")
     */
    private $programs;

    /**
     * @ORM\OneToMany(targetEntity=Metabolite::class, mappedBy="createdBy")
     */
    private $metabolites;

    /**
     * @ORM\OneToMany(targetEntity=Trial::class, mappedBy="createdBy")
     */
    private $trials;

    /**
     * @ORM\OneToMany(targetEntity=Accession::class, mappedBy="createdBy")
     */
    private $accessions;

    /**
     * @ORM\OneToMany(targetEntity=AttributeTraitValue::class, mappedBy="createdBy")
     */
    private $attributeTraitValues;

    /**
     * @ORM\OneToMany(targetEntity=Synonym::class, mappedBy="createdBy")
     */
    private $synonyms;

    /**
     * @ORM\OneToMany(targetEntity=SharedWith::class, mappedBy="user")
     */
    private $sharedWiths;

    /**
     * @ORM\OneToMany(targetEntity=Study::class, mappedBy="createdBy")
     */
    private $studies;

    /**
     * @ORM\OneToMany(targetEntity=Germplasm::class, mappedBy="createdBy")
     */
    private $germplasms;

    /**
     * @ORM\OneToMany(targetEntity=StudyParameterValue::class, mappedBy="createdBy")
     */
    private $studyParameterValues;

    /**
     * @ORM\OneToMany(targetEntity=Collection::class, mappedBy="createdBy")
     */
    private $collections;

    /**
     * @ORM\OneToMany(targetEntity=Cross::class, mappedBy="createdBy")
     */
    private $crosses;

    /**
     * @ORM\OneToMany(targetEntity=StudyImage::class, mappedBy="createdBy")
     */
    private $studyImages;

    /**
     * @ORM\OneToMany(targetEntity=ObservationLevel::class, mappedBy="createdBy")
     */
    private $observationLevels;

    /**
     * @ORM\OneToMany(targetEntity=GWAS::class, mappedBy="createdBy")
     */
    private $gWAS;

    public function __construct()
    {
        $this->countries = new ArrayCollection();
        $this->crops = new ArrayCollection();
        $this->seasons = new ArrayCollection();
        $this->factorTypes = new ArrayCollection();
        $this->growthFacilityTypes = new ArrayCollection();
        $this->experimentalDesignTypes = new ArrayCollection();
        $this->units = new ArrayCollection();
        $this->trialTypes = new ArrayCollection();
        $this->dataTypes = new ArrayCollection();
        $this->metaboliteClasses = new ArrayCollection();
        $this->identificationLevels = new ArrayCollection();
        $this->annotationLevels = new ArrayCollection();
        $this->metabolicTraits = new ArrayCollection();
        $this->methodClasses = new ArrayCollection();
        $this->scaleCategories = new ArrayCollection();
        $this->taxonomies = new ArrayCollection();
        $this->mLSStatuses = new ArrayCollection();
        $this->storageTypes = new ArrayCollection();
        $this->attributeCategories = new ArrayCollection();
        $this->breedingMethods = new ArrayCollection();
        $this->traitClasses = new ArrayCollection();
        $this->collectingSources = new ArrayCollection();
        $this->biologicalStatuses = new ArrayCollection();
        $this->qTLMethods = new ArrayCollection();
        $this->thresholdMethods = new ArrayCollection();
        $this->software = new ArrayCollection();
        $this->ciCriterias = new ArrayCollection();
        $this->qTLStatistics = new ArrayCollection();
        $this->gWASModels = new ArrayCollection();
        $this->geneticTestingModels = new ArrayCollection();
        $this->gWASStatTests = new ArrayCollection();
        $this->allelicEffectEstimators = new ArrayCollection();
        $this->structureMethods = new ArrayCollection();
        $this->kinshipAlgorithms = new ArrayCollection();
        $this->traitProcessings = new ArrayCollection();
        $this->sequencingTypes = new ArrayCollection();
        $this->sequencingInstruments = new ArrayCollection();
        $this->varCallSoftware = new ArrayCollection();
        $this->anatomicalEntities = new ArrayCollection();
        $this->developmentalStages = new ArrayCollection();
        $this->locations = new ArrayCollection();
        $this->attributes = new ArrayCollection();
        $this->people = new ArrayCollection();
        $this->parameters = new ArrayCollection();
        $this->scales = new ArrayCollection();
        $this->observationVariableMethods = new ArrayCollection();
        $this->institutes = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->collectingMissions = new ArrayCollection();
        $this->observationVariables = new ArrayCollection();
        $this->analyteClasses = new ArrayCollection();
        $this->enzymes = new ArrayCollection();
        $this->genotypingPlatforms = new ArrayCollection();
        $this->markers = new ArrayCollection();
        $this->variantSetMetadata = new ArrayCollection();
        $this->analyteFlavorHealths = new ArrayCollection();
        $this->analytes = new ArrayCollection();
        $this->programs = new ArrayCollection();
        $this->metabolites = new ArrayCollection();
        $this->trials = new ArrayCollection();
        $this->accessions = new ArrayCollection();
        $this->attributeTraitValues = new ArrayCollection();
        $this->synonyms = new ArrayCollection();
        $this->sharedWiths = new ArrayCollection();
        $this->studies = new ArrayCollection();
        $this->germplasms = new ArrayCollection();
        $this->studyParameterValues = new ArrayCollection();
        $this->collections = new ArrayCollection();
        $this->crosses = new ArrayCollection();
        $this->studyImages = new ArrayCollection();
        $this->observationLevels = new ArrayCollection();
        $this->gWAS = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Country>
     */
    public function getCountries(): Collection
    {
        return $this->countries;
    }

    public function addCountry(Country $country): self
    {
        if (!$this->countries->contains($country)) {
            $this->countries[] = $country;
            $country->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCountry(Country $country): self
    {
        if ($this->countries->removeElement($country)) {
            // set the owning side to null (unless already changed)
            if ($country->getCreatedBy() === $this) {
                $country->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Crop>
     */
    public function getCrops(): Collection
    {
        return $this->crops;
    }

    public function addCrop(Crop $crop): self
    {
        if (!$this->crops->contains($crop)) {
            $this->crops[] = $crop;
            $crop->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCrop(Crop $crop): self
    {
        if ($this->crops->removeElement($crop)) {
            // set the owning side to null (unless already changed)
            if ($crop->getCreatedBy() === $this) {
                $crop->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Season>
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(Season $season): self
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons[] = $season;
            $season->setCreatedBy($this);
        }

        return $this;
    }

    public function removeSeason(Season $season): self
    {
        if ($this->seasons->removeElement($season)) {
            // set the owning side to null (unless already changed)
            if ($season->getCreatedBy() === $this) {
                $season->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FactorType>
     */
    public function getFactorTypes(): Collection
    {
        return $this->factorTypes;
    }

    public function addFactorType(FactorType $factorType): self
    {
        if (!$this->factorTypes->contains($factorType)) {
            $this->factorTypes[] = $factorType;
            $factorType->setCreatedBy($this);
        }

        return $this;
    }

    public function removeFactorType(FactorType $factorType): self
    {
        if ($this->factorTypes->removeElement($factorType)) {
            // set the owning side to null (unless already changed)
            if ($factorType->getCreatedBy() === $this) {
                $factorType->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GrowthFacilityType>
     */
    public function getGrowthFacilityTypes(): Collection
    {
        return $this->growthFacilityTypes;
    }

    public function addGrowthFacilityType(GrowthFacilityType $growthFacilityType): self
    {
        if (!$this->growthFacilityTypes->contains($growthFacilityType)) {
            $this->growthFacilityTypes[] = $growthFacilityType;
            $growthFacilityType->setCreatedBy($this);
        }

        return $this;
    }

    public function removeGrowthFacilityType(GrowthFacilityType $growthFacilityType): self
    {
        if ($this->growthFacilityTypes->removeElement($growthFacilityType)) {
            // set the owning side to null (unless already changed)
            if ($growthFacilityType->getCreatedBy() === $this) {
                $growthFacilityType->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ExperimentalDesignType>
     */
    public function getExperimentalDesignTypes(): Collection
    {
        return $this->experimentalDesignTypes;
    }

    public function addExperimentalDesignType(ExperimentalDesignType $experimentalDesignType): self
    {
        if (!$this->experimentalDesignTypes->contains($experimentalDesignType)) {
            $this->experimentalDesignTypes[] = $experimentalDesignType;
            $experimentalDesignType->setCreatedBy($this);
        }

        return $this;
    }

    public function removeExperimentalDesignType(ExperimentalDesignType $experimentalDesignType): self
    {
        if ($this->experimentalDesignTypes->removeElement($experimentalDesignType)) {
            // set the owning side to null (unless already changed)
            if ($experimentalDesignType->getCreatedBy() === $this) {
                $experimentalDesignType->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Unit>
     */
    public function getUnits(): Collection
    {
        return $this->units;
    }

    public function addUnit(Unit $unit): self
    {
        if (!$this->units->contains($unit)) {
            $this->units[] = $unit;
            $unit->setCreatedBy($this);
        }

        return $this;
    }

    public function removeUnit(Unit $unit): self
    {
        if ($this->units->removeElement($unit)) {
            // set the owning side to null (unless already changed)
            if ($unit->getCreatedBy() === $this) {
                $unit->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TrialType>
     */
    public function getTrialTypes(): Collection
    {
        return $this->trialTypes;
    }

    public function addTrialType(TrialType $trialType): self
    {
        if (!$this->trialTypes->contains($trialType)) {
            $this->trialTypes[] = $trialType;
            $trialType->setCreatedBy($this);
        }

        return $this;
    }

    public function removeTrialType(TrialType $trialType): self
    {
        if ($this->trialTypes->removeElement($trialType)) {
            // set the owning side to null (unless already changed)
            if ($trialType->getCreatedBy() === $this) {
                $trialType->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DataType>
     */
    public function getDataTypes(): Collection
    {
        return $this->dataTypes;
    }

    public function addDataType(DataType $dataType): self
    {
        if (!$this->dataTypes->contains($dataType)) {
            $this->dataTypes[] = $dataType;
            $dataType->setCreatedBy($this);
        }

        return $this;
    }

    public function removeDataType(DataType $dataType): self
    {
        if ($this->dataTypes->removeElement($dataType)) {
            // set the owning side to null (unless already changed)
            if ($dataType->getCreatedBy() === $this) {
                $dataType->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MetaboliteClass>
     */
    public function getMetaboliteClasses(): Collection
    {
        return $this->metaboliteClasses;
    }

    public function addMetaboliteClass(MetaboliteClass $metaboliteClass): self
    {
        if (!$this->metaboliteClasses->contains($metaboliteClass)) {
            $this->metaboliteClasses[] = $metaboliteClass;
            $metaboliteClass->setCreatedBy($this);
        }

        return $this;
    }

    public function removeMetaboliteClass(MetaboliteClass $metaboliteClass): self
    {
        if ($this->metaboliteClasses->removeElement($metaboliteClass)) {
            // set the owning side to null (unless already changed)
            if ($metaboliteClass->getCreatedBy() === $this) {
                $metaboliteClass->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, IdentificationLevel>
     */
    public function getIdentificationLevels(): Collection
    {
        return $this->identificationLevels;
    }

    public function addIdentificationLevel(IdentificationLevel $identificationLevel): self
    {
        if (!$this->identificationLevels->contains($identificationLevel)) {
            $this->identificationLevels[] = $identificationLevel;
            $identificationLevel->setCreatedBy($this);
        }

        return $this;
    }

    public function removeIdentificationLevel(IdentificationLevel $identificationLevel): self
    {
        if ($this->identificationLevels->removeElement($identificationLevel)) {
            // set the owning side to null (unless already changed)
            if ($identificationLevel->getCreatedBy() === $this) {
                $identificationLevel->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AnnotationLevel>
     */
    public function getAnnotationLevels(): Collection
    {
        return $this->annotationLevels;
    }

    public function addAnnotationLevel(AnnotationLevel $annotationLevel): self
    {
        if (!$this->annotationLevels->contains($annotationLevel)) {
            $this->annotationLevels[] = $annotationLevel;
            $annotationLevel->setCreatedBy($this);
        }

        return $this;
    }

    public function removeAnnotationLevel(AnnotationLevel $annotationLevel): self
    {
        if ($this->annotationLevels->removeElement($annotationLevel)) {
            // set the owning side to null (unless already changed)
            if ($annotationLevel->getCreatedBy() === $this) {
                $annotationLevel->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MetabolicTrait>
     */
    public function getMetabolicTraits(): Collection
    {
        return $this->metabolicTraits;
    }

    public function addMetabolicTrait(MetabolicTrait $metabolicTrait): self
    {
        if (!$this->metabolicTraits->contains($metabolicTrait)) {
            $this->metabolicTraits[] = $metabolicTrait;
            $metabolicTrait->setCreatedBy($this);
        }

        return $this;
    }

    public function removeMetabolicTrait(MetabolicTrait $metabolicTrait): self
    {
        if ($this->metabolicTraits->removeElement($metabolicTrait)) {
            // set the owning side to null (unless already changed)
            if ($metabolicTrait->getCreatedBy() === $this) {
                $metabolicTrait->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MethodClass>
     */
    public function getMethodClasses(): Collection
    {
        return $this->methodClasses;
    }

    public function addMethodClass(MethodClass $methodClass): self
    {
        if (!$this->methodClasses->contains($methodClass)) {
            $this->methodClasses[] = $methodClass;
            $methodClass->setCreatedBy($this);
        }

        return $this;
    }

    public function removeMethodClass(MethodClass $methodClass): self
    {
        if ($this->methodClasses->removeElement($methodClass)) {
            // set the owning side to null (unless already changed)
            if ($methodClass->getCreatedBy() === $this) {
                $methodClass->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ScaleCategory>
     */
    public function getScaleCategories(): Collection
    {
        return $this->scaleCategories;
    }

    public function addScaleCategory(ScaleCategory $scaleCategory): self
    {
        if (!$this->scaleCategories->contains($scaleCategory)) {
            $this->scaleCategories[] = $scaleCategory;
            $scaleCategory->setCreatedBy($this);
        }

        return $this;
    }

    public function removeScaleCategory(ScaleCategory $scaleCategory): self
    {
        if ($this->scaleCategories->removeElement($scaleCategory)) {
            // set the owning side to null (unless already changed)
            if ($scaleCategory->getCreatedBy() === $this) {
                $scaleCategory->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Taxonomy>
     */
    public function getTaxonomies(): Collection
    {
        return $this->taxonomies;
    }

    public function addTaxonomy(Taxonomy $taxonomy): self
    {
        if (!$this->taxonomies->contains($taxonomy)) {
            $this->taxonomies[] = $taxonomy;
            $taxonomy->setCreatedBy($this);
        }

        return $this;
    }

    public function removeTaxonomy(Taxonomy $taxonomy): self
    {
        if ($this->taxonomies->removeElement($taxonomy)) {
            // set the owning side to null (unless already changed)
            if ($taxonomy->getCreatedBy() === $this) {
                $taxonomy->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MLSStatus>
     */
    public function getMLSStatuses(): Collection
    {
        return $this->mLSStatuses;
    }

    public function addMLSStatus(MLSStatus $mLSStatus): self
    {
        if (!$this->mLSStatuses->contains($mLSStatus)) {
            $this->mLSStatuses[] = $mLSStatus;
            $mLSStatus->setCreatedBy($this);
        }

        return $this;
    }

    public function removeMLSStatus(MLSStatus $mLSStatus): self
    {
        if ($this->mLSStatuses->removeElement($mLSStatus)) {
            // set the owning side to null (unless already changed)
            if ($mLSStatus->getCreatedBy() === $this) {
                $mLSStatus->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StorageType>
     */
    public function getStorageTypes(): Collection
    {
        return $this->storageTypes;
    }

    public function addStorageType(StorageType $storageType): self
    {
        if (!$this->storageTypes->contains($storageType)) {
            $this->storageTypes[] = $storageType;
            $storageType->setCreatedBy($this);
        }

        return $this;
    }

    public function removeStorageType(StorageType $storageType): self
    {
        if ($this->storageTypes->removeElement($storageType)) {
            // set the owning side to null (unless already changed)
            if ($storageType->getCreatedBy() === $this) {
                $storageType->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AttributeCategory>
     */
    public function getAttributeCategories(): Collection
    {
        return $this->attributeCategories;
    }

    public function addAttributeCategory(AttributeCategory $attributeCategory): self
    {
        if (!$this->attributeCategories->contains($attributeCategory)) {
            $this->attributeCategories[] = $attributeCategory;
            $attributeCategory->setCreatedBy($this);
        }

        return $this;
    }

    public function removeAttributeCategory(AttributeCategory $attributeCategory): self
    {
        if ($this->attributeCategories->removeElement($attributeCategory)) {
            // set the owning side to null (unless already changed)
            if ($attributeCategory->getCreatedBy() === $this) {
                $attributeCategory->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BreedingMethod>
     */
    public function getBreedingMethods(): Collection
    {
        return $this->breedingMethods;
    }

    public function addBreedingMethod(BreedingMethod $breedingMethod): self
    {
        if (!$this->breedingMethods->contains($breedingMethod)) {
            $this->breedingMethods[] = $breedingMethod;
            $breedingMethod->setCreatedBy($this);
        }

        return $this;
    }

    public function removeBreedingMethod(BreedingMethod $breedingMethod): self
    {
        if ($this->breedingMethods->removeElement($breedingMethod)) {
            // set the owning side to null (unless already changed)
            if ($breedingMethod->getCreatedBy() === $this) {
                $breedingMethod->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TraitClass>
     */
    public function getTraitClasses(): Collection
    {
        return $this->traitClasses;
    }

    public function addTraitClass(TraitClass $traitClass): self
    {
        if (!$this->traitClasses->contains($traitClass)) {
            $this->traitClasses[] = $traitClass;
            $traitClass->setCreatedBy($this);
        }

        return $this;
    }

    public function removeTraitClass(TraitClass $traitClass): self
    {
        if ($this->traitClasses->removeElement($traitClass)) {
            // set the owning side to null (unless already changed)
            if ($traitClass->getCreatedBy() === $this) {
                $traitClass->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CollectingSource>
     */
    public function getCollectingSources(): Collection
    {
        return $this->collectingSources;
    }

    public function addCollectingSource(CollectingSource $collectingSource): self
    {
        if (!$this->collectingSources->contains($collectingSource)) {
            $this->collectingSources[] = $collectingSource;
            $collectingSource->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCollectingSource(CollectingSource $collectingSource): self
    {
        if ($this->collectingSources->removeElement($collectingSource)) {
            // set the owning side to null (unless already changed)
            if ($collectingSource->getCreatedBy() === $this) {
                $collectingSource->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BiologicalStatus>
     */
    public function getBiologicalStatuses(): Collection
    {
        return $this->biologicalStatuses;
    }

    public function addBiologicalStatus(BiologicalStatus $biologicalStatus): self
    {
        if (!$this->biologicalStatuses->contains($biologicalStatus)) {
            $this->biologicalStatuses[] = $biologicalStatus;
            $biologicalStatus->setCreatedBy($this);
        }

        return $this;
    }

    public function removeBiologicalStatus(BiologicalStatus $biologicalStatus): self
    {
        if ($this->biologicalStatuses->removeElement($biologicalStatus)) {
            // set the owning side to null (unless already changed)
            if ($biologicalStatus->getCreatedBy() === $this) {
                $biologicalStatus->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QTLMethod>
     */
    public function getQTLMethods(): Collection
    {
        return $this->qTLMethods;
    }

    public function addQTLMethod(QTLMethod $qTLMethod): self
    {
        if (!$this->qTLMethods->contains($qTLMethod)) {
            $this->qTLMethods[] = $qTLMethod;
            $qTLMethod->setCreatedBy($this);
        }

        return $this;
    }

    public function removeQTLMethod(QTLMethod $qTLMethod): self
    {
        if ($this->qTLMethods->removeElement($qTLMethod)) {
            // set the owning side to null (unless already changed)
            if ($qTLMethod->getCreatedBy() === $this) {
                $qTLMethod->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ThresholdMethod>
     */
    public function getThresholdMethods(): Collection
    {
        return $this->thresholdMethods;
    }

    public function addThresholdMethod(ThresholdMethod $thresholdMethod): self
    {
        if (!$this->thresholdMethods->contains($thresholdMethod)) {
            $this->thresholdMethods[] = $thresholdMethod;
            $thresholdMethod->setCreatedBy($this);
        }

        return $this;
    }

    public function removeThresholdMethod(ThresholdMethod $thresholdMethod): self
    {
        if ($this->thresholdMethods->removeElement($thresholdMethod)) {
            // set the owning side to null (unless already changed)
            if ($thresholdMethod->getCreatedBy() === $this) {
                $thresholdMethod->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Software>
     */
    public function getSoftware(): Collection
    {
        return $this->software;
    }

    public function addSoftware(Software $software): self
    {
        if (!$this->software->contains($software)) {
            $this->software[] = $software;
            $software->setCreatedBy($this);
        }

        return $this;
    }

    public function removeSoftware(Software $software): self
    {
        if ($this->software->removeElement($software)) {
            // set the owning side to null (unless already changed)
            if ($software->getCreatedBy() === $this) {
                $software->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CiCriteria>
     */
    public function getCiCriterias(): Collection
    {
        return $this->ciCriterias;
    }

    public function addCiCriteria(CiCriteria $ciCriteria): self
    {
        if (!$this->ciCriterias->contains($ciCriteria)) {
            $this->ciCriterias[] = $ciCriteria;
            $ciCriteria->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCiCriteria(CiCriteria $ciCriteria): self
    {
        if ($this->ciCriterias->removeElement($ciCriteria)) {
            // set the owning side to null (unless already changed)
            if ($ciCriteria->getCreatedBy() === $this) {
                $ciCriteria->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QTLStatistic>
     */
    public function getQTLStatistics(): Collection
    {
        return $this->qTLStatistics;
    }

    public function addQTLStatistic(QTLStatistic $qTLStatistic): self
    {
        if (!$this->qTLStatistics->contains($qTLStatistic)) {
            $this->qTLStatistics[] = $qTLStatistic;
            $qTLStatistic->setCreatedBy($this);
        }

        return $this;
    }

    public function removeQTLStatistic(QTLStatistic $qTLStatistic): self
    {
        if ($this->qTLStatistics->removeElement($qTLStatistic)) {
            // set the owning side to null (unless already changed)
            if ($qTLStatistic->getCreatedBy() === $this) {
                $qTLStatistic->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GWASModel>
     */
    public function getGWASModels(): Collection
    {
        return $this->gWASModels;
    }

    public function addGWASModel(GWASModel $gWASModel): self
    {
        if (!$this->gWASModels->contains($gWASModel)) {
            $this->gWASModels[] = $gWASModel;
            $gWASModel->setCreatedBy($this);
        }

        return $this;
    }

    public function removeGWASModel(GWASModel $gWASModel): self
    {
        if ($this->gWASModels->removeElement($gWASModel)) {
            // set the owning side to null (unless already changed)
            if ($gWASModel->getCreatedBy() === $this) {
                $gWASModel->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GeneticTestingModel>
     */
    public function getGeneticTestingModels(): Collection
    {
        return $this->geneticTestingModels;
    }

    public function addGeneticTestingModel(GeneticTestingModel $geneticTestingModel): self
    {
        if (!$this->geneticTestingModels->contains($geneticTestingModel)) {
            $this->geneticTestingModels[] = $geneticTestingModel;
            $geneticTestingModel->setCreatedBy($this);
        }

        return $this;
    }

    public function removeGeneticTestingModel(GeneticTestingModel $geneticTestingModel): self
    {
        if ($this->geneticTestingModels->removeElement($geneticTestingModel)) {
            // set the owning side to null (unless already changed)
            if ($geneticTestingModel->getCreatedBy() === $this) {
                $geneticTestingModel->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GWASStatTest>
     */
    public function getGWASStatTests(): Collection
    {
        return $this->gWASStatTests;
    }

    public function addGWASStatTest(GWASStatTest $gWASStatTest): self
    {
        if (!$this->gWASStatTests->contains($gWASStatTest)) {
            $this->gWASStatTests[] = $gWASStatTest;
            $gWASStatTest->setCreatedBy($this);
        }

        return $this;
    }

    public function removeGWASStatTest(GWASStatTest $gWASStatTest): self
    {
        if ($this->gWASStatTests->removeElement($gWASStatTest)) {
            // set the owning side to null (unless already changed)
            if ($gWASStatTest->getCreatedBy() === $this) {
                $gWASStatTest->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AllelicEffectEstimator>
     */
    public function getAllelicEffectEstimators(): Collection
    {
        return $this->allelicEffectEstimators;
    }

    public function addAllelicEffectEstimator(AllelicEffectEstimator $allelicEffectEstimator): self
    {
        if (!$this->allelicEffectEstimators->contains($allelicEffectEstimator)) {
            $this->allelicEffectEstimators[] = $allelicEffectEstimator;
            $allelicEffectEstimator->setCreatedBy($this);
        }

        return $this;
    }

    public function removeAllelicEffectEstimator(AllelicEffectEstimator $allelicEffectEstimator): self
    {
        if ($this->allelicEffectEstimators->removeElement($allelicEffectEstimator)) {
            // set the owning side to null (unless already changed)
            if ($allelicEffectEstimator->getCreatedBy() === $this) {
                $allelicEffectEstimator->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StructureMethod>
     */
    public function getStructureMethods(): Collection
    {
        return $this->structureMethods;
    }

    public function addStructureMethod(StructureMethod $structureMethod): self
    {
        if (!$this->structureMethods->contains($structureMethod)) {
            $this->structureMethods[] = $structureMethod;
            $structureMethod->setCreatedBy($this);
        }

        return $this;
    }

    public function removeStructureMethod(StructureMethod $structureMethod): self
    {
        if ($this->structureMethods->removeElement($structureMethod)) {
            // set the owning side to null (unless already changed)
            if ($structureMethod->getCreatedBy() === $this) {
                $structureMethod->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, KinshipAlgorithm>
     */
    public function getKinshipAlgorithms(): Collection
    {
        return $this->kinshipAlgorithms;
    }

    public function addKinshipAlgorithm(KinshipAlgorithm $kinshipAlgorithm): self
    {
        if (!$this->kinshipAlgorithms->contains($kinshipAlgorithm)) {
            $this->kinshipAlgorithms[] = $kinshipAlgorithm;
            $kinshipAlgorithm->setCreatedBy($this);
        }

        return $this;
    }

    public function removeKinshipAlgorithm(KinshipAlgorithm $kinshipAlgorithm): self
    {
        if ($this->kinshipAlgorithms->removeElement($kinshipAlgorithm)) {
            // set the owning side to null (unless already changed)
            if ($kinshipAlgorithm->getCreatedBy() === $this) {
                $kinshipAlgorithm->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TraitProcessing>
     */
    public function getTraitProcessings(): Collection
    {
        return $this->traitProcessings;
    }

    public function addTraitProcessing(TraitProcessing $traitProcessing): self
    {
        if (!$this->traitProcessings->contains($traitProcessing)) {
            $this->traitProcessings[] = $traitProcessing;
            $traitProcessing->setCreatedBy($this);
        }

        return $this;
    }

    public function removeTraitProcessing(TraitProcessing $traitProcessing): self
    {
        if ($this->traitProcessings->removeElement($traitProcessing)) {
            // set the owning side to null (unless already changed)
            if ($traitProcessing->getCreatedBy() === $this) {
                $traitProcessing->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SequencingType>
     */
    public function getSequencingTypes(): Collection
    {
        return $this->sequencingTypes;
    }

    public function addSequencingType(SequencingType $sequencingType): self
    {
        if (!$this->sequencingTypes->contains($sequencingType)) {
            $this->sequencingTypes[] = $sequencingType;
            $sequencingType->setCreatedBy($this);
        }

        return $this;
    }

    public function removeSequencingType(SequencingType $sequencingType): self
    {
        if ($this->sequencingTypes->removeElement($sequencingType)) {
            // set the owning side to null (unless already changed)
            if ($sequencingType->getCreatedBy() === $this) {
                $sequencingType->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SequencingInstrument>
     */
    public function getSequencingInstruments(): Collection
    {
        return $this->sequencingInstruments;
    }

    public function addSequencingInstrument(SequencingInstrument $sequencingInstrument): self
    {
        if (!$this->sequencingInstruments->contains($sequencingInstrument)) {
            $this->sequencingInstruments[] = $sequencingInstrument;
            $sequencingInstrument->setCreatedBy($this);
        }

        return $this;
    }

    public function removeSequencingInstrument(SequencingInstrument $sequencingInstrument): self
    {
        if ($this->sequencingInstruments->removeElement($sequencingInstrument)) {
            // set the owning side to null (unless already changed)
            if ($sequencingInstrument->getCreatedBy() === $this) {
                $sequencingInstrument->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, VarCallSoftware>
     */
    public function getVarCallSoftware(): Collection
    {
        return $this->varCallSoftware;
    }

    public function addVarCallSoftware(VarCallSoftware $varCallSoftware): self
    {
        if (!$this->varCallSoftware->contains($varCallSoftware)) {
            $this->varCallSoftware[] = $varCallSoftware;
            $varCallSoftware->setCreatedBy($this);
        }

        return $this;
    }

    public function removeVarCallSoftware(VarCallSoftware $varCallSoftware): self
    {
        if ($this->varCallSoftware->removeElement($varCallSoftware)) {
            // set the owning side to null (unless already changed)
            if ($varCallSoftware->getCreatedBy() === $this) {
                $varCallSoftware->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AnatomicalEntity>
     */
    public function getAnatomicalEntities(): Collection
    {
        return $this->anatomicalEntities;
    }

    public function addAnatomicalEntity(AnatomicalEntity $anatomicalEntity): self
    {
        if (!$this->anatomicalEntities->contains($anatomicalEntity)) {
            $this->anatomicalEntities[] = $anatomicalEntity;
            $anatomicalEntity->setCreatedBy($this);
        }

        return $this;
    }

    public function removeAnatomicalEntity(AnatomicalEntity $anatomicalEntity): self
    {
        if ($this->anatomicalEntities->removeElement($anatomicalEntity)) {
            // set the owning side to null (unless already changed)
            if ($anatomicalEntity->getCreatedBy() === $this) {
                $anatomicalEntity->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DevelopmentalStage>
     */
    public function getDevelopmentalStages(): Collection
    {
        return $this->developmentalStages;
    }

    public function addDevelopmentalStage(DevelopmentalStage $developmentalStage): self
    {
        if (!$this->developmentalStages->contains($developmentalStage)) {
            $this->developmentalStages[] = $developmentalStage;
            $developmentalStage->setCreatedBy($this);
        }

        return $this;
    }

    public function removeDevelopmentalStage(DevelopmentalStage $developmentalStage): self
    {
        if ($this->developmentalStages->removeElement($developmentalStage)) {
            // set the owning side to null (unless already changed)
            if ($developmentalStage->getCreatedBy() === $this) {
                $developmentalStage->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(Location $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations[] = $location;
            $location->setCreatedBy($this);
        }

        return $this;
    }

    public function removeLocation(Location $location): self
    {
        if ($this->locations->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->getCreatedBy() === $this) {
                $location->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Attribute>
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    public function addAttribute(Attribute $attribute): self
    {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes[] = $attribute;
            $attribute->setCreatedBy($this);
        }

        return $this;
    }

    public function removeAttribute(Attribute $attribute): self
    {
        if ($this->attributes->removeElement($attribute)) {
            // set the owning side to null (unless already changed)
            if ($attribute->getCreatedBy() === $this) {
                $attribute->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        // unset the owning side of the relation if necessary
        if ($person === null && $this->person !== null) {
            $this->person->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($person !== null && $person->getUser() !== $this) {
            $person->setUser($this);
        }

        $this->person = $person;

        return $this;
    }

    /**
     * @return Collection<int, Person>
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    public function addPerson(Person $person): self
    {
        if (!$this->people->contains($person)) {
            $this->people[] = $person;
            $person->setCreatedBy($this);
        }

        return $this;
    }

    public function removePerson(Person $person): self
    {
        if ($this->people->removeElement($person)) {
            // set the owning side to null (unless already changed)
            if ($person->getCreatedBy() === $this) {
                $person->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Parameter>
     */
    public function getParameters(): Collection
    {
        return $this->parameters;
    }

    public function addParameter(Parameter $parameter): self
    {
        if (!$this->parameters->contains($parameter)) {
            $this->parameters[] = $parameter;
            $parameter->setCreatedBy($this);
        }

        return $this;
    }

    public function removeParameter(Parameter $parameter): self
    {
        if ($this->parameters->removeElement($parameter)) {
            // set the owning side to null (unless already changed)
            if ($parameter->getCreatedBy() === $this) {
                $parameter->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Scale>
     */
    public function getScales(): Collection
    {
        return $this->scales;
    }

    public function addScale(Scale $scale): self
    {
        if (!$this->scales->contains($scale)) {
            $this->scales[] = $scale;
            $scale->setCreatedBy($this);
        }

        return $this;
    }

    public function removeScale(Scale $scale): self
    {
        if ($this->scales->removeElement($scale)) {
            // set the owning side to null (unless already changed)
            if ($scale->getCreatedBy() === $this) {
                $scale->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ObservationVariableMethod>
     */
    public function getObservationVariableMethods(): Collection
    {
        return $this->observationVariableMethods;
    }

    public function addObservationVariableMethod(ObservationVariableMethod $observationVariableMethod): self
    {
        if (!$this->observationVariableMethods->contains($observationVariableMethod)) {
            $this->observationVariableMethods[] = $observationVariableMethod;
            $observationVariableMethod->setCreatedBy($this);
        }

        return $this;
    }

    public function removeObservationVariableMethod(ObservationVariableMethod $observationVariableMethod): self
    {
        if ($this->observationVariableMethods->removeElement($observationVariableMethod)) {
            // set the owning side to null (unless already changed)
            if ($observationVariableMethod->getCreatedBy() === $this) {
                $observationVariableMethod->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Institute>
     */
    public function getInstitutes(): Collection
    {
        return $this->institutes;
    }

    public function addInstitute(Institute $institute): self
    {
        if (!$this->institutes->contains($institute)) {
            $this->institutes[] = $institute;
            $institute->setCreatedBy($this);
        }

        return $this;
    }

    public function removeInstitute(Institute $institute): self
    {
        if ($this->institutes->removeElement($institute)) {
            // set the owning side to null (unless already changed)
            if ($institute->getCreatedBy() === $this) {
                $institute->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setCreatedBy($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getCreatedBy() === $this) {
                $contact->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CollectingMission>
     */
    public function getCollectingMissions(): Collection
    {
        return $this->collectingMissions;
    }

    public function addCollectingMission(CollectingMission $collectingMission): self
    {
        if (!$this->collectingMissions->contains($collectingMission)) {
            $this->collectingMissions[] = $collectingMission;
            $collectingMission->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCollectingMission(CollectingMission $collectingMission): self
    {
        if ($this->collectingMissions->removeElement($collectingMission)) {
            // set the owning side to null (unless already changed)
            if ($collectingMission->getCreatedBy() === $this) {
                $collectingMission->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ObservationVariable>
     */
    public function getObservationVariables(): Collection
    {
        return $this->observationVariables;
    }

    public function addObservationVariable(ObservationVariable $observationVariable): self
    {
        if (!$this->observationVariables->contains($observationVariable)) {
            $this->observationVariables[] = $observationVariable;
            $observationVariable->setCreatedBy($this);
        }

        return $this;
    }

    public function removeObservationVariable(ObservationVariable $observationVariable): self
    {
        if ($this->observationVariables->removeElement($observationVariable)) {
            // set the owning side to null (unless already changed)
            if ($observationVariable->getCreatedBy() === $this) {
                $observationVariable->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AnalyteClass>
     */
    public function getAnalyteClasses(): Collection
    {
        return $this->analyteClasses;
    }

    public function addAnalyteClass(AnalyteClass $analyteClass): self
    {
        if (!$this->analyteClasses->contains($analyteClass)) {
            $this->analyteClasses[] = $analyteClass;
            $analyteClass->setCreatedBy($this);
        }

        return $this;
    }

    public function removeAnalyteClass(AnalyteClass $analyteClass): self
    {
        if ($this->analyteClasses->removeElement($analyteClass)) {
            // set the owning side to null (unless already changed)
            if ($analyteClass->getCreatedBy() === $this) {
                $analyteClass->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Enzyme>
     */
    public function getEnzymes(): Collection
    {
        return $this->enzymes;
    }

    public function addEnzyme(Enzyme $enzyme): self
    {
        if (!$this->enzymes->contains($enzyme)) {
            $this->enzymes[] = $enzyme;
            $enzyme->setCreatedBy($this);
        }

        return $this;
    }

    public function removeEnzyme(Enzyme $enzyme): self
    {
        if ($this->enzymes->removeElement($enzyme)) {
            // set the owning side to null (unless already changed)
            if ($enzyme->getCreatedBy() === $this) {
                $enzyme->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GenotypingPlatform>
     */
    public function getGenotypingPlatforms(): Collection
    {
        return $this->genotypingPlatforms;
    }

    public function addGenotypingPlatform(GenotypingPlatform $genotypingPlatform): self
    {
        if (!$this->genotypingPlatforms->contains($genotypingPlatform)) {
            $this->genotypingPlatforms[] = $genotypingPlatform;
            $genotypingPlatform->setCreatedBy($this);
        }

        return $this;
    }

    public function removeGenotypingPlatform(GenotypingPlatform $genotypingPlatform): self
    {
        if ($this->genotypingPlatforms->removeElement($genotypingPlatform)) {
            // set the owning side to null (unless already changed)
            if ($genotypingPlatform->getCreatedBy() === $this) {
                $genotypingPlatform->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Marker>
     */
    public function getMarkers(): Collection
    {
        return $this->markers;
    }

    public function addMarker(Marker $marker): self
    {
        if (!$this->markers->contains($marker)) {
            $this->markers[] = $marker;
            $marker->setCreatedBy($this);
        }

        return $this;
    }

    public function removeMarker(Marker $marker): self
    {
        if ($this->markers->removeElement($marker)) {
            // set the owning side to null (unless already changed)
            if ($marker->getCreatedBy() === $this) {
                $marker->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, VariantSetMetadata>
     */
    public function getVariantSetMetadata(): Collection
    {
        return $this->variantSetMetadata;
    }

    public function addVariantSetMetadata(VariantSetMetadata $variantSetMetadata): self
    {
        if (!$this->variantSetMetadata->contains($variantSetMetadata)) {
            $this->variantSetMetadata[] = $variantSetMetadata;
            $variantSetMetadata->setCreatedBy($this);
        }

        return $this;
    }

    public function removeVariantSetMetadata(VariantSetMetadata $variantSetMetadata): self
    {
        if ($this->variantSetMetadata->removeElement($variantSetMetadata)) {
            // set the owning side to null (unless already changed)
            if ($variantSetMetadata->getCreatedBy() === $this) {
                $variantSetMetadata->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AnalyteFlavorHealth>
     */
    public function getAnalyteFlavorHealths(): Collection
    {
        return $this->analyteFlavorHealths;
    }

    public function addAnalyteFlavorHealth(AnalyteFlavorHealth $analyteFlavorHealth): self
    {
        if (!$this->analyteFlavorHealths->contains($analyteFlavorHealth)) {
            $this->analyteFlavorHealths[] = $analyteFlavorHealth;
            $analyteFlavorHealth->setCreatedBy($this);
        }

        return $this;
    }

    public function removeAnalyteFlavorHealth(AnalyteFlavorHealth $analyteFlavorHealth): self
    {
        if ($this->analyteFlavorHealths->removeElement($analyteFlavorHealth)) {
            // set the owning side to null (unless already changed)
            if ($analyteFlavorHealth->getCreatedBy() === $this) {
                $analyteFlavorHealth->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Analyte>
     */
    public function getAnalytes(): Collection
    {
        return $this->analytes;
    }

    public function addAnalyte(Analyte $analyte): self
    {
        if (!$this->analytes->contains($analyte)) {
            $this->analytes[] = $analyte;
            $analyte->setCreatedBy($this);
        }

        return $this;
    }

    public function removeAnalyte(Analyte $analyte): self
    {
        if ($this->analytes->removeElement($analyte)) {
            // set the owning side to null (unless already changed)
            if ($analyte->getCreatedBy() === $this) {
                $analyte->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Program>
     */
    public function getPrograms(): Collection
    {
        return $this->programs;
    }

    public function addProgram(Program $program): self
    {
        if (!$this->programs->contains($program)) {
            $this->programs[] = $program;
            $program->setCreatedBy($this);
        }

        return $this;
    }

    public function removeProgram(Program $program): self
    {
        if ($this->programs->removeElement($program)) {
            // set the owning side to null (unless already changed)
            if ($program->getCreatedBy() === $this) {
                $program->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Metabolite>
     */
    public function getMetabolites(): Collection
    {
        return $this->metabolites;
    }

    public function addMetabolite(Metabolite $metabolite): self
    {
        if (!$this->metabolites->contains($metabolite)) {
            $this->metabolites[] = $metabolite;
            $metabolite->setCreatedBy($this);
        }

        return $this;
    }

    public function removeMetabolite(Metabolite $metabolite): self
    {
        if ($this->metabolites->removeElement($metabolite)) {
            // set the owning side to null (unless already changed)
            if ($metabolite->getCreatedBy() === $this) {
                $metabolite->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Trial>
     */
    public function getTrials(): Collection
    {
        return $this->trials;
    }

    public function addTrial(Trial $trial): self
    {
        if (!$this->trials->contains($trial)) {
            $this->trials[] = $trial;
            $trial->setCreatedBy($this);
        }

        return $this;
    }

    public function removeTrial(Trial $trial): self
    {
        if ($this->trials->removeElement($trial)) {
            // set the owning side to null (unless already changed)
            if ($trial->getCreatedBy() === $this) {
                $trial->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Accession>
     */
    public function getAccessions(): Collection
    {
        return $this->accessions;
    }

    public function addAccession(Accession $accession): self
    {
        if (!$this->accessions->contains($accession)) {
            $this->accessions[] = $accession;
            $accession->setCreatedBy($this);
        }

        return $this;
    }

    public function removeAccession(Accession $accession): self
    {
        if ($this->accessions->removeElement($accession)) {
            // set the owning side to null (unless already changed)
            if ($accession->getCreatedBy() === $this) {
                $accession->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AttributeTraitValue>
     */
    public function getAttributeTraitValues(): Collection
    {
        return $this->attributeTraitValues;
    }

    public function addAttributeTraitValue(AttributeTraitValue $attributeTraitValue): self
    {
        if (!$this->attributeTraitValues->contains($attributeTraitValue)) {
            $this->attributeTraitValues[] = $attributeTraitValue;
            $attributeTraitValue->setCreatedBy($this);
        }

        return $this;
    }

    public function removeAttributeTraitValue(AttributeTraitValue $attributeTraitValue): self
    {
        if ($this->attributeTraitValues->removeElement($attributeTraitValue)) {
            // set the owning side to null (unless already changed)
            if ($attributeTraitValue->getCreatedBy() === $this) {
                $attributeTraitValue->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Synonym>
     */
    public function getSynonyms(): Collection
    {
        return $this->synonyms;
    }

    public function addSynonym(Synonym $synonym): self
    {
        if (!$this->synonyms->contains($synonym)) {
            $this->synonyms[] = $synonym;
            $synonym->setCreatedBy($this);
        }

        return $this;
    }

    public function removeSynonym(Synonym $synonym): self
    {
        if ($this->synonyms->removeElement($synonym)) {
            // set the owning side to null (unless already changed)
            if ($synonym->getCreatedBy() === $this) {
                $synonym->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SharedWith>
     */
    public function getSharedWiths(): Collection
    {
        return $this->sharedWiths;
    }

    public function addSharedWith(SharedWith $sharedWith): self
    {
        if (!$this->sharedWiths->contains($sharedWith)) {
            $this->sharedWiths[] = $sharedWith;
            $sharedWith->setUser($this);
        }

        return $this;
    }

    public function removeSharedWith(SharedWith $sharedWith): self
    {
        if ($this->sharedWiths->removeElement($sharedWith)) {
            // set the owning side to null (unless already changed)
            if ($sharedWith->getUser() === $this) {
                $sharedWith->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Study>
     */
    public function getStudies(): Collection
    {
        return $this->studies;
    }

    public function addStudy(Study $study): self
    {
        if (!$this->studies->contains($study)) {
            $this->studies[] = $study;
            $study->setCreatedBy($this);
        }

        return $this;
    }

    public function removeStudy(Study $study): self
    {
        if ($this->studies->removeElement($study)) {
            // set the owning side to null (unless already changed)
            if ($study->getCreatedBy() === $this) {
                $study->setCreatedBy(null);
            }
        }

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
            $germplasm->setCreatedBy($this);
        }

        return $this;
    }

    public function removeGermplasm(Germplasm $germplasm): self
    {
        if ($this->germplasms->removeElement($germplasm)) {
            // set the owning side to null (unless already changed)
            if ($germplasm->getCreatedBy() === $this) {
                $germplasm->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StudyParameterValue>
     */
    public function getStudyParameterValues(): Collection
    {
        return $this->studyParameterValues;
    }

    public function addStudyParameterValue(StudyParameterValue $studyParameterValue): self
    {
        if (!$this->studyParameterValues->contains($studyParameterValue)) {
            $this->studyParameterValues[] = $studyParameterValue;
            $studyParameterValue->setCreatedBy($this);
        }

        return $this;
    }

    public function removeStudyParameterValue(StudyParameterValue $studyParameterValue): self
    {
        if ($this->studyParameterValues->removeElement($studyParameterValue)) {
            // set the owning side to null (unless already changed)
            if ($studyParameterValue->getCreatedBy() === $this) {
                $studyParameterValue->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Collection>
     */
    public function getCollections(): Collection
    {
        return $this->collections;
    }

    public function addCollection(Collection $collection): self
    {
        if (!$this->collections->contains($collection)) {
            $this->collections[] = $collection;
            $collection->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCollection(Collection $collection): self
    {
        if ($this->collections->removeElement($collection)) {
            // set the owning side to null (unless already changed)
            if ($collection->getCreatedBy() === $this) {
                $collection->setCreatedBy(null);
            }
        }

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
            $cross->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCross(Cross $cross): self
    {
        if ($this->crosses->removeElement($cross)) {
            // set the owning side to null (unless already changed)
            if ($cross->getCreatedBy() === $this) {
                $cross->setCreatedBy(null);
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
            $studyImage->setCreatedBy($this);
        }

        return $this;
    }

    public function removeStudyImage(StudyImage $studyImage): self
    {
        if ($this->studyImages->removeElement($studyImage)) {
            // set the owning side to null (unless already changed)
            if ($studyImage->getCreatedBy() === $this) {
                $studyImage->setCreatedBy(null);
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
            $observationLevel->setCreatedBy($this);
        }

        return $this;
    }

    public function removeObservationLevel(ObservationLevel $observationLevel): self
    {
        if ($this->observationLevels->removeElement($observationLevel)) {
            // set the owning side to null (unless already changed)
            if ($observationLevel->getCreatedBy() === $this) {
                $observationLevel->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GWAS>
     */
    public function getGWAS(): Collection
    {
        return $this->gWAS;
    }

    public function addGWA(GWAS $gWA): self
    {
        if (!$this->gWAS->contains($gWA)) {
            $this->gWAS[] = $gWA;
            $gWA->setCreatedBy($this);
        }

        return $this;
    }

    public function removeGWA(GWAS $gWA): self
    {
        if ($this->gWAS->removeElement($gWA)) {
            // set the owning side to null (unless already changed)
            if ($gWA->getCreatedBy() === $this) {
                $gWA->setCreatedBy(null);
            }
        }

        return $this;
    }
}
