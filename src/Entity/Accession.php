<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AccessionRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=AccessionRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"accession:read"}},
 *      denormalizationContext={"groups"={"accession:write"}}
 * )
 */
class Accession
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"accession:read", "program:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"accession:read", "program:read"})
     * @SerializedName("accessionNumber")
     */
    private $accenumb;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"accession:read", "program:read"})
     * @SerializedName("germplasmName")
     */
    private $accename;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"accession:read", "program:read"})
     * @SerializedName("germplasmPUI")
     */
    private $puid;

    /**
     * @ORM\ManyToOne(targetEntity=Country::class, inversedBy="accessions")
     * @Groups({"accession:read", "program:read"})
     * @SerializedName("countryOfOriginCode")
     */
    private $origcty;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"accession:read", "program:read"})
     */
    private $origmuni;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"accession:read", "program:read"})
     */
    private $origadmin1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"accession:read", "program:read"})
     */
    private $origadmin2;

    /**
     * @ORM\ManyToOne(targetEntity=CollectingSource::class, inversedBy="accessions")
     * @Groups({"accession:read", "program:read"})
     */
    private $collsrc;

    /**
     * @ORM\ManyToOne(targetEntity=BiologicalStatus::class, inversedBy="accessions")
     * @Groups({"accession:read", "program:read"})
     */
    private $sampstat;

    /**
     * @ORM\ManyToOne(targetEntity=Taxonomy::class, inversedBy="accessions")
     * @Groups({"accession:read", "program:read"})
     * @SerializedName("taxonIds")
     */
    private $taxon;

    /**
     * @ORM\ManyToOne(targetEntity=Institute::class, inversedBy="accessions")
     * @Groups({"accession:read", "program:read"})
     * @SerializedName("instituteCode")
     */
    private $instcode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"accession:read", "program:read"})
     */
    private $maintainernumb;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"accession:read", "program:read"})
     * @SerializedName("acquisitionDate")
     */
    private $acqdate;

    /**
     * @ORM\ManyToOne(targetEntity=StorageType::class, inversedBy="accessions")
     * @Groups({"accession:read", "program:read"})
     * @SerializedName("storageType")
     */
    private $storage;

    /**
     * @ORM\ManyToOne(targetEntity=Institute::class, inversedBy="accessions")
     * @Groups({"accession:read", "program:read"})
     * @SerializedName("donorInstituteCode")
     */
    private $donorcode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"accession:read", "program:read"})
     */
    private $donornumb;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"accession:read", "program:read"})
     */
    private $collnumb;

    /**
     * @ORM\ManyToOne(targetEntity=Institute::class, inversedBy="accessions")
     */
    private $collcode;

    /**
     * @ORM\ManyToOne(targetEntity=CollectingMission::class, inversedBy="accessions")
     * @Groups({"accession:read", "program:read"})
     */
    private $collmissid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"accession:read", "program:read"})
     */
    private $colldate;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
     * @Groups({"accession:read", "program:read"})
     */
    private $declatitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
     * @Groups({"accession:read", "program:read"})
     */
    private $declongitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
     * @Groups({"accession:read", "program:read"})
     */
    private $elevation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"accession:read", "program:read"})
     */
    private $collsite;

    /**
     * @ORM\ManyToOne(targetEntity=Institute::class, inversedBy="accessions")
     * @Groups({"accession:read", "program:read"})
     * @SerializedName("breedingInstitute")
     */
    private $bredcode;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="accessions")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Synonym::class, mappedBy="accession")
     * @Groups({"accession:read", "program:read"})
     */
    private $synonyms;

    /**
     * @ORM\OneToMany(targetEntity=AttributeTraitValue::class, mappedBy="accession")
     * @Groups({"accession:read", "program:read"})
     */
    private $attributeTraitValues;


    /**
     * @ORM\ManyToOne(targetEntity=MLSStatus::class, inversedBy="accessions")
     * @Groups({"accession:read", "program:read"})
     */
    private $mlsStatus;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $breedingInfo;

    // API variables
    private $donnors;
    private $collectingInfo;

    /**
     * @ORM\OneToMany(targetEntity=Germplasm::class, mappedBy="accession")
     */
    private $germplasmNumber;

    /**
     * @ORM\OneToMany(targetEntity=Germplasm::class, mappedBy="accession")
     */
    private $germplasms;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $doi;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $publicationRef = [];

    public function __construct()
    {
        $this->synonyms = new ArrayCollection();
        $this->attributeTraitValues = new ArrayCollection();
        // API variables
        $this->donnors = new ArrayCollection();
        $this->collectingInfo = new ArrayCollection();
        $this->germplasmNumber = new ArrayCollection();
        $this->germplasms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccenumb(): ?string
    {
        return $this->accenumb;
    }

    public function setAccenumb(string $accenumb): self
    {
        $this->accenumb = $accenumb;

        return $this;
    }

    public function getAccename(): ?string
    {
        return $this->accename;
    }

    public function setAccename(?string $accename): self
    {
        $this->accename = $accename;

        return $this;
    }

    public function getPuid(): ?string
    {
        return $this->puid;
    }

    public function setPuid(string $puid): self
    {
        $this->puid = $puid;

        return $this;
    }

    public function getOrigcty(): ?Country
    {
        return $this->origcty;
    }

    public function setOrigcty(?Country $origcty): self
    {
        $this->origcty = $origcty;

        return $this;
    }

    public function getOrigmuni(): ?string
    {
        return $this->origmuni;
    }

    public function setOrigmuni(?string $origmuni): self
    {
        $this->origmuni = $origmuni;

        return $this;
    }

    public function getOrigadmin1(): ?string
    {
        return $this->origadmin1;
    }

    public function setOrigadmin1(?string $origadmin1): self
    {
        $this->origadmin1 = $origadmin1;

        return $this;
    }

    public function getOrigadmin2(): ?string
    {
        return $this->origadmin2;
    }

    public function setOrigadmin2(?string $origadmin2): self
    {
        $this->origadmin2 = $origadmin2;

        return $this;
    }

    public function getCollsrc(): ?CollectingSource
    {
        return $this->collsrc;
    }

    public function setCollsrc(?CollectingSource $collsrc): self
    {
        $this->collsrc = $collsrc;

        return $this;
    }

    public function getSampstat(): ?BiologicalStatus
    {
        return $this->sampstat;
    }

    public function setSampstat(?BiologicalStatus $sampstat): self
    {
        $this->sampstat = $sampstat;

        return $this;
    }

    public function getTaxon(): ?Taxonomy
    {
        return $this->taxon;
    }

    public function setTaxon(?Taxonomy $taxon): self
    {
        $this->taxon = $taxon;

        return $this;
    }

    public function getInstcode(): ?Institute
    {
        return $this->instcode;
    }

    public function setInstcode(?Institute $instcode): self
    {
        $this->instcode = $instcode;

        return $this;
    }

    public function getMaintainernumb(): ?string
    {
        return $this->maintainernumb;
    }

    public function setMaintainernumb(string $maintainernumb): self
    {
        $this->maintainernumb = $maintainernumb;

        return $this;
    }

    public function getAcqdate(): ?string
    {
        return $this->acqdate;
    }

    public function setAcqdate(?string $acqdate): self
    {
        $this->acqdate = $acqdate;

        return $this;
    }

    public function getStorage(): ?StorageType
    {
        return $this->storage;
    }

    public function setStorage(?StorageType $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    public function getDonorcode(): ?Institute
    {
        return $this->donorcode;
    }

    public function setDonorcode(?Institute $donorcode): self
    {
        $this->donorcode = $donorcode;

        return $this;
    }

    public function getDonornumb(): ?string
    {
        return $this->donornumb;
    }

    public function setDonornumb(string $donornumb): self
    {
        $this->donornumb = $donornumb;

        return $this;
    }

    public function getCollnumb(): ?string
    {
        return $this->collnumb;
    }

    public function setCollnumb(?string $collnumb): self
    {
        $this->collnumb = $collnumb;

        return $this;
    }

    public function getCollcode(): ?Institute
    {
        return $this->collcode;
    }

    public function setCollcode(?Institute $collcode): self
    {
        $this->collcode = $collcode;

        return $this;
    }

    public function getCollmissid(): ?CollectingMission
    {
        return $this->collmissid;
    }

    public function setCollmissid(?CollectingMission $collmissid): self
    {
        $this->collmissid = $collmissid;

        return $this;
    }

    public function getColldate(): ?string
    {
        return $this->colldate;
    }

    public function setColldate(?string $colldate): self
    {
        $this->colldate = $colldate;

        return $this;
    }

    public function getDeclatitude(): ?string
    {
        return $this->declatitude;
    }

    public function setDeclatitude(?string $declatitude): self
    {
        $this->declatitude = $declatitude;

        return $this;
    }

    public function getDeclongitude(): ?string
    {
        return $this->declongitude;
    }

    public function setDeclongitude(?string $declongitude): self
    {
        $this->declongitude = $declongitude;

        return $this;
    }

    public function getElevation(): ?string
    {
        return $this->elevation;
    }

    public function setElevation(?string $elevation): self
    {
        $this->elevation = $elevation;

        return $this;
    }

    public function getCollsite(): ?string
    {
        return $this->collsite;
    }

    public function setCollsite(?string $collsite): self
    {
        $this->collsite = $collsite;

        return $this;
    }

    public function getBredcode(): ?Institute
    {
        return $this->bredcode;
    }

    public function setBredcode(?Institute $bredcode): self
    {
        $this->bredcode = $bredcode;

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
            $synonym->setAccession($this);
        }

        return $this;
    }

    public function removeSynonym(Synonym $synonym): self
    {
        if ($this->synonyms->removeElement($synonym)) {
            // set the owning side to null (unless already changed)
            if ($synonym->getAccession() === $this) {
                $synonym->setAccession(null);
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
            $attributeTraitValue->setAccession($this);
        }

        return $this;
    }

    public function removeAttributeTraitValue(AttributeTraitValue $attributeTraitValue): self
    {
        if ($this->attributeTraitValues->removeElement($attributeTraitValue)) {
            // set the owning side to null (unless already changed)
            if ($attributeTraitValue->getAccession() === $this) {
                $attributeTraitValue->setAccession(null);
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
            $germplasm->setAccession($this);
        }

        return $this;
    }

    public function removeGermplasm(Germplasm $germplasm): self
    {
        if ($this->germplasms->removeElement($germplasm)) {
            // set the owning side to null (unless already changed)
            if ($germplasm->getAccession() === $this) {
                $germplasm->setAccession(null);
            }
        }

        return $this;
    }

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->accenumb;
    }

    public function getMlsStatus(): ?MLSStatus
    {
        return $this->mlsStatus;
    }

    public function setMlsStatus(?MLSStatus $mlsStatus): self
    {
        $this->mlsStatus = $mlsStatus;

        return $this;
    }

    public function getBreedingInfo(): ?string
    {
        return $this->breedingInfo;
    }

    public function setBreedingInfo(?string $breedingInfo): self
    {
        $this->breedingInfo = $breedingInfo;

        return $this;
    }

    // FOR THE API
    // <- MAGIC IS HERE, you can set a group on a method.
    // /**
    //  * @Groups({"accession:read", "program:read"})
    //  */
    // public function getDonnors(): string
    // {
    //     return $this->donornumb .". This should be an array with donnorInstituteCode, donnorAccessionNumber ";
    // }

    /**
     * @Groups({"accession:read", "program:read"})
     * @return Collection<collection>
     */
    public function getDonnors(): Array
    {
        $this->donnors = [
            "donnorInstituteCode" => $this->donorcode ? $this->donorcode->getInstcode() : null,
            "donnorAccessionNumber"=> $this->getDonornumb()
        ];
        return $this->donnors;
    }

    /**
     * @Groups({"accession:read", "program:read"})
     */
    public function getCollectingInfo(): Array
    {
        $this->collectingInfo = [
            "collNumber" => $this->collnumb,
            "collInstitute"=> $this->collcode,
            "collMission" => $this->collmissid,
            "date" => "Some Date",
            "institute" => "Some Inst",
            "site" => [
                "alt" => "89",
                "long" => "74",
                "elev" => "12",
            ]
        ];
        return $this->collectingInfo;
        // .". This should be an array with collNumber, collInstitute, collectingMission, date, institute and site(alt, long, ele) ". $this->collnumb;
    }

    /**
     * @Groups({"accession:read", "program:read"})
     */
    public function getPedigree(): ?string
    {
        return $this->breedingInfo;
    }

    /**
     * @Groups({"accession:read"})
     */
    public function getAcquisitionSourceCode()
    {
        return $this->collsrc ? $this->collsrc->getOntologyId() : null;
    }

    /**
     * @Groups({"accession:read"})
     */
    public function getBiologicalStatusOfAccessionCode()
    {
        return $this->sampstat->getOntologyId();
    }

    /**
     * @Groups({"accession:read"})
     */
    public function getBiologicalStatusOfAccessisonDescription()
    {
        return $this->sampstat->getName();
    }

    /**
     * @Groups({"accession:read"})
     */
    public function getBreedingMethodDbId()
    {
        return "...";
    }

    /**
     * @Groups({"accession:read"})
     */
    public function getBreedingMethodName()
    {
        return "...";
    }

    /**
     * @Groups({"accession:read"})
     */
    public function getCollection()
    {
        return "...";
    }

    // /**
    //  * @Groups({"accession:read", "program:read"})
    //  * @return Collection<collection>
    //  */
    // public function getSthg(): Collection
    // {
    //     return $this->synonyms;
    // }

    /**
     * @return Collection<int, Germplasm>
     */
    public function getGermplasmNumber(): Collection
    {
        return $this->germplasmNumber;
    }

    public function addGermplasmNumber(Germplasm $germplasmNumber): self
    {
        if (!$this->germplasmNumber->contains($germplasmNumber)) {
            $this->germplasmNumber[] = $germplasmNumber;
            $germplasmNumber->setAccession($this);
        }

        return $this;
    }

    public function removeGermplasmNumber(Germplasm $germplasmNumber): self
    {
        if ($this->germplasmNumber->removeElement($germplasmNumber)) {
            // set the owning side to null (unless already changed)
            if ($germplasmNumber->getAccession() === $this) {
                $germplasmNumber->setAccession(null);
            }
        }

        return $this;
    }

    public function getDoi(): ?string
    {
        return $this->doi;
    }

    public function setDoi(?string $doi): self
    {
        $this->doi = $doi;

        return $this;
    }

    public function getPublicationRef(): ?array
    {
        return $this->publicationRef;
    }

    public function setPublicationRef(?array $publicationRef): self
    {
        $this->publicationRef = $publicationRef;

        return $this;
    }
}
