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

    /**
     * @ORM\ManyToMany(targetEntity=CollectionClass::class, mappedBy="germplasm")
     */
    private $germplasmCollection;

    // API SECTION

    /**
     * @Groups({"germplasm:read"})
     */
    private $storageType;

    /**
     * @Groups({"germplasm:read"})
     */
    private $donors;

    /**
     * @ORM\ManyToOne(targetEntity=Institute::class, inversedBy="providedGermplasm")
     * @ORM\JoinColumn(nullable=false)
     */
    private $maintainerInstituteCode;

    /**
     * @ORM\OneToMany(targetEntity=Progeny::class, mappedBy="progenyParent1")
     */
    private $progenies;

    /**
     * @ORM\OneToMany(targetEntity=Progeny::class, mappedBy="progenyParent2")
     */
    private $parent2GermProgeny;
    // this variable is exactly the same as the progenies one

    /**
     * @ORM\OneToOne(targetEntity=Progeny::class, mappedBy="pedigreeGermplasm", cascade={"persist", "remove"})
     */
    private $progeny;

    /**
     * @ORM\OneToMany(targetEntity=Cross::class, mappedBy="parent2")
     */
    private $parent2GermCross;
    // this variable is exactly the same as the crosses one

    public function __construct()
    {
        $this->study = new ArrayCollection();
        $this->crosses = new ArrayCollection();
        $this->observationLevels = new ArrayCollection();
        $this->samples = new ArrayCollection();
        $this->pedigrees = new ArrayCollection();
        $this->qTLVariants = new ArrayCollection();
        $this->germplasmStudyImages = new ArrayCollection();
        $this->germplasmCollection = new ArrayCollection();
        $this->storageType = new ArrayCollection();
        $this->donors = new ArrayCollection();
        $this->progenies = new ArrayCollection();
        $this->parent2GermProgeny = new ArrayCollection();
        $this->parent2GermCross = new ArrayCollection();
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
        return new ArrayCollection(
            array_merge($this->crosses->toArray(), $this->parent2GermCross->toArray())
        );
        //return $this->crosses;
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

    /**
     * @return Collection<int, CollectionClass>
     */
    public function getGermplasmCollection(): Collection
    {
        return $this->germplasmCollection;
    }

    public function addGermplasmCollection(CollectionClass $germplasmCollection): self
    {
        if (!$this->germplasmCollection->contains($germplasmCollection)) {
            $this->germplasmCollection[] = $germplasmCollection;
            $germplasmCollection->addGermplasm($this);
        }

        return $this;
    }

    public function removeGermplasmCollection(CollectionClass $germplasmCollection): self
    {
        if ($this->germplasmCollection->removeElement($germplasmCollection)) {
            $germplasmCollection->removeGermplasm($this);
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
    // API SECTION

    /**
     * @Groups({"germplasm:read"})
    */
    public function getGermplasmName()
    {
        return $this->accession->getAccename();
    }

    /**
     * @Groups({"germplasm:read"})
    */
    public function getDefaultDisplayName()
    {
        return $this->accession->getAccename();
    }

    /**
     * @Groups({"germplasm:read"})
     * @SerializedName("germplasmPUI")
    */
    public function getPUI()
    {
        return $this->accession->getPuid();
    }

    /**
     * @Groups({"germplasm:read"})
    */
    public function getCountryOfOriginCode()
    {
        return $this->accession->getOrigcty()->getIso3();
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getAcquisitionSourceCode()
    {
        return $this->accession->getCollsrc() ? $this->accession->getCollsrc()->getOntologyId() : null;
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getBiologicalStatusOfAccessionCode()
    {
        return $this->accession->getSampstat()->getOntologyId();
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getBiologicalStatusOfAccessisonDescription()
    {
        return $this->accession->getSampstat()->getName();
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getMLSStatus()
    {
        return $code = $this->accession->getMLSStatus() ? $this->accession->getMLSStatus()->getOntologyId() : null;
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getBreedingMethodDbId()
    {
        return "...";
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getBreedingMethodName()
    {
        return "...";
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getSeedSource()
    {
        return $this->instcode .":". $this->maintainerNumb;
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getSeedSourceDescription()
    {
        return "...";
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getGermplasmPreprocessing()
    {
        return $this->preprocessing;
    }
    

    /**
     * @Groups({"germplasm:read"})
     */
    public function getAccessionNumber()
    {
        return $this->accession->getAccenumb();
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getTaxonIds()
    {
        $taxonIds = [
            "SourceName" => "NCBI",
            "taxonId" => $this->accession->getTaxon()->getTaxonid()
        ];
        return $taxonIds;
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getGenus()
    {
        return $this->accession->getTaxon()->getGenus();
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getSpecies()
    {
        return $this->accession->getTaxon()->getSpecies();
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getSubtaxa()
    {
        return $this->accession->getTaxon()->getSubtaxa();
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getInstituteCode()
    {
        return $this->accession->getInstcode()->getInstcode();
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getInstituteName()
    {
        return $this->accession->getInstcode()->getName();
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getAcquisitionDate()
    {
        return $this->accession->getAcqdate();
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getCollection()
    {
        $collectionName = [];
        foreach ($this->germplasmCollection as $key => $collection) {
            $collectionName [] = $collection->getName();
        }
        return $collectionName;
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getStorageTypes()
    {
        $storageType = [
            "code" => $this->accession->getStorage() ? $this->accession->getStorage()->getOntologyId() : null,
            "description" => $this->accession->getStorage() ? $this->accession->getStorage()->getName() : null
        ];
        return $storageType;
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getDonors()
    {
        $donors = [
            "donorAccessionNumber" => $this->accession->getDonornumb(),
            "donorInstituteCode" => $this->accession->getDonorcode() ? $this->accession->getDonorcode()->getInstcode(): null,
        ];
        return $donors;
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getCollectingInfo()
    {
        $collectingInfo = [
            "collectingDate" => $this->accession->getColldate(),
            "collectingMissionIdentifier" => $this->accession->getCollmissid() ? $this->accession->getCollmissid()->getName() : null,
            "collectingNumber" => $this->accession->getCollnumb(),
            "collectingInstitute" => [
                "instituteCode" => $this->accession->getCollCode() ? $this->accession->getCollCode()->getInstcode() : null,
                "instituteName" => $this->accession->getCollCode() ? $this->accession->getCollCode()->getName() : null,
                "instituteAddress" => $this->accession->getCollCode() ? $this->accession->getCollCode()->getStreetNumber() ." ". $this->accession->getCollCode()->getPostalCode() ." ". $this->accession->getCollCode()->getCity() ." ". $this->accession->getCollCode()->getCountry() : null
            ],
            "collectingSite" => [
                "latituteDecimal" => $this->accession->getDeclatitude(),
                "longitudeDecimal" => $this->accession->getDeclongitude(),
                "elevation" => $this->accession->getElevation(),
                "locationDescription" => $this->accession->getCollsite()
            ]
        ];
        return $collectingInfo;
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getGermplasmOrigin()
    {
        $germplasmOrigin = [
            "coordinateUncertainty" => "...",
            "coordinates" => [
                "geometry" => "...",
                "type" => ".."
            ],   
        ];
        return $germplasmOrigin;
    }

    /**
     * @Groups({"germplasm:read"})
     * @SerializedName("pedigree")
     */
    public function getAncestor()
    {
        return $this->accession->getBreedingInfo();
    }

    /**
     * @Groups({"germplasm:read"})
     * 
     */
    public function getCommonCropName()
    {
        return $this->program->getCrop()->getCommonCropName();
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getBreedingInstitute()
    {
        $breedingInstitute = [
            "instituteCode" => $this->accession->getBredcode() ? $this->accession->getBredcode()->getInstcode() : null,
            "instituteName" => $this->accession->getBredcode() ? $this->accession->getBredcode()->getName() : null,
              
        ];
        return $breedingInstitute;
    }

    /**
     * @Groups({"germplasm:read"})
     */
    public function getSynonyms()
    {
        $synonyms = [
            "synonym" => $this->accession->getSynonyms(),
            "type" => "NCBI",
              
        ];
        return $synonyms;
    }

    public function getMaintainerInstituteCode(): ?Institute
    {
        return $this->maintainerInstituteCode;
    }

    public function setMaintainerInstituteCode(?Institute $maintainerInstituteCode): self
    {
        $this->maintainerInstituteCode = $maintainerInstituteCode;

        return $this;
    }

    /**
     * @return Collection<int, Progeny>
     */
    public function getProgenies(): Collection
    {
        return new ArrayCollection(
            array_merge($this->progenies->toArray(), $this->parent2GermProgeny->toArray())
        );
        //return $this->progenies;
    }

    public function addProgeny(Progeny $progeny): self
    {
        if (!$this->progenies->contains($progeny)) {
            $this->progenies[] = $progeny;
            $progeny->setProgenyParent1($this);
        }

        return $this;
    }

    public function removeProgeny(Progeny $progeny): self
    {
        if ($this->progenies->removeElement($progeny)) {
            // set the owning side to null (unless already changed)
            if ($progeny->getProgenyParent1() === $this) {
                $progeny->setProgenyParent1(null);
            }
        }

        return $this;
    }

    public function getProgeny(): ?Progeny
    {
        return $this->progeny;
    }

    public function setProgeny(?Progeny $progeny): self
    {
        // unset the owning side of the relation if necessary
        if ($progeny === null && $this->progeny !== null) {
            $this->progeny->setPedigreeGermplasm(null);
        }

        // set the owning side of the relation if necessary
        if ($progeny !== null && $progeny->getPedigreeGermplasm() !== $this) {
            $progeny->setPedigreeGermplasm($this);
        }

        $this->progeny = $progeny;

        return $this;
    }

    /**
     * @return Collection<int, Cross>
     */
    public function getParent2GermCross(): Collection
    {
        return $this->parent2GermCross;
    }

    public function addParent2GermCross(Cross $parent2GermCross): self
    {
        if (!$this->parent2GermCross->contains($parent2GermCross)) {
            $this->parent2GermCross[] = $parent2GermCross;
            $parent2GermCross->setParent2($this);
        }

        return $this;
    }

    public function removeParent2GermCross(Cross $parent2GermCross): self
    {
        if ($this->parent2GermCross->removeElement($parent2GermCross)) {
            // set the owning side to null (unless already changed)
            if ($parent2GermCross->getParent2() === $this) {
                $parent2GermCross->setParent2(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Progeny>
     */
    public function getParent2GermProgeny(): Collection
    {
        return $this->parent2GermProgeny;
    }

    public function addParent2GermProgeny(Progeny $parent2GermProgeny): self
    {
        if (!$this->parent2GermProgeny->contains($parent2GermProgeny)) {
            $this->parent2GermProgeny[] = $parent2GermProgeny;
            $parent2GermProgeny->setProgenyParent2($this);
        }

        return $this;
    }

    public function removeParent2GermProgeny(Progeny $parent2GermProgeny): self
    {
        if ($this->parent2GermProgeny->removeElement($parent2GermProgeny)) {
            // set the owning side to null (unless already changed)
            if ($parent2GermProgeny->getProgenyParent2() === $this) {
                $parent2GermProgeny->setProgenyParent2(null);
            }
        }

        return $this;
    }

}
