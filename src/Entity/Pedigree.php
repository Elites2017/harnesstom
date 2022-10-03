<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PedigreeRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=PedigreeRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"pedigree:read"}},
 *      denormalizationContext={"groups"={"pedigree:write"}}
 * )
 */
class Pedigree
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "observation_variable:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "observation_variable:read"})
     */
    private $pedigreeEntryID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "observation_variable:read"})
     */
    private $generation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "observation_variable:read"})
     */
    private $ancestorPedigreeEntryID;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToMany(targetEntity=Germplasm::class, inversedBy="pedigrees")
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "trial:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "observation_variable:read"})
     */
    private $germplasm;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="pedigrees")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=MappingPopulation::class, mappedBy="pedigreeGeneration")
     * @Groups({"mls_status:read", "marker:read", "trial:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "observation_variable:read"})
     */
    private $mappingPopulations;

    /**
     * @ORM\ManyToOne(targetEntity=Cross::class, inversedBy="pedigrees")
     */
    private $pedigreeCross;

    private $parenstOfPeds;

    public function __construct()
    {
        $this->germplasm = new ArrayCollection();
        $this->mappingPopulations = new ArrayCollection();
        $this->parenstOfPeds = new ArrayCollection();
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

    public function getGeneration(): ?string
    {
        return $this->generation;
    }

    public function setGeneration(?string $generation): self
    {
        $this->generation = $generation;

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
     * @return Collection<int, MappingPopulation>
     */
    public function getMappingPopulations(): Collection
    {
        return $this->mappingPopulations;
    }

    public function addMappingPopulation(MappingPopulation $mappingPopulation): self
    {
        if (!$this->mappingPopulations->contains($mappingPopulation)) {
            $this->mappingPopulations[] = $mappingPopulation;
            $mappingPopulation->setPedigreeGeneration($this);
        }

        return $this;
    }

    public function removeMappingPopulation(MappingPopulation $mappingPopulation): self
    {
        if ($this->mappingPopulations->removeElement($mappingPopulation)) {
            // set the owning side to null (unless already changed)
            if ($mappingPopulation->getPedigreeGeneration() === $this) {
                $mappingPopulation->setPedigreeGeneration(null);
            }
        }

        return $this;
    }

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->pedigreeEntryID;
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

    // API SECTION
    /**
     * @Groups({"pedigree:read"})
     */
    public function getPedigreeDbId() {
        return $this->id;
    }

    /**
     * @Groups({"pedigree:read"})
     */
    public function getBreedingMethodDbId() {
        return $this->pedigreeCross->getBreedingMethod()->getOntologyId();
    }

    /**
     * @Groups({"pedigree:read"})
     */
    public function getBreedingMethodName() {
        return $this->pedigreeCross->getBreedingMethod()->getName();
    }

    /**
     * @Groups({"pedigree:read"})
     */
    public function getCrossingProjectDbId() {
        return $this->pedigreeCross->getStudy()->getName();
    }

    /**
     * @Groups({"pedigree:read"})
     */
    public function getCrossingYear() {
        return $this->pedigreeCross->getYear();
    }

    /**
     * @Groups({"pedigree:read"})
     */
    public function getDefaultDisplayName() {
        return $this->pedigreeEntryID;
    }

    /**
     * @Groups({"pedigree:read"})
     */
    public function getFamilyCode() {
        return "N/A";
    }

    /**
     * @Groups({"pedigree:read"})
     */
    public function getGermplasmDbId() {
        return $this->germplasm[0]->getGermplasmID();
    }

    /**
     * @Groups({"pedigree:read"})
     */
    public function getGermplasmName() {
        return $this->germplasm[0]->getAccession()->getAcceName();
    }

    /**
     * @Groups({"pedigree:read"})
     */
    public function getGermplasmPUI() {
        return $this->germplasm[0]->getAccession()->getPuid();
    }

    /**
    * @Groups({"pedigree:read"})
     */
    public function getParents() {
        $this->parenstOfPeds [] = $this->pedigreeCross->getParent1()->getGermplasmID();
        $this->parenstOfPeds [] = $this->pedigreeCross->getParent2()->getGermplasmID();
        $parent = [
            [
                "germplasmDbid" => $this->pedigreeCross->getParent1()->getGermplasmID(),
                "germplasmName" => $this->pedigreeCross->getParent1()->getAccession()->getAccename(),
                "parentType" => $this->pedigreeCross->getParent1Type()
            ],
            [
                "germplasmDbid" => $this->pedigreeCross->getParent2()->getGermplasmID(),
                "germplasmName" => $this->pedigreeCross->getParent2()->getAccession()->getAccename(),
                "parentType" => $this->pedigreeCross->getParent2Type()
            ]
        ];
        return $parent;
    }

    /**
    * @Groups({"pedigree:read"})
     */
    public function getProgeny() {
        // To show the progenies
        // pedigree progeny table (linked to germplasm, because they are germplasms)
        $pedigreeProgenyArr = [];
        $germplasmUsedinThePedigree = $this->getGermplasm()[0];

        // progenies with all data even if parent
        $progenies = $this->getGermplasm()[0]->getProgenies();

        // this array is to filter the progenies to have only the real ones
        $realProgeniesOnly = [];

        // If their generation is P for parent, do not add it in the realProgenyOnly array
        foreach ($progenies as $key => $progen) {
            if ($progen->getPedigreeGermplasm()->getPedigrees()[0]->getGeneration() !== "P"){
                $realProgeniesOnly [] = $progen;
            }
        }

        // this is to have the type of the parent
        $typeOfParentOfProgeny = "";
        // As all progenies / pedigrees are germplasms, check which one is used in the pedigree. (pedigree x cross)
        foreach ($realProgeniesOnly as $key => $oneProgeny) {
            // get the type of the parent which is used in the germplasm
            if ($germplasmUsedinThePedigree === $oneProgeny->getProgenyCross()->getParent1()) {
                $typeOfParentOfProgeny = $oneProgeny->getProgenyCross()->getParent1Type();
            } else {
                $typeOfParentOfProgeny = $oneProgeny->getProgenyCross()->getParent2Type();
            }
            $pedigreeProgenyArr [] = [
                "germplasmDbid" => $oneProgeny->getPedigreeGermplasm()->getGermplasmID(),
                "germplasmName" => $oneProgeny->getPedigreeGermplasm()->getAccession()->getAccename(),
                "parentType" => $typeOfParentOfProgeny,
            ];
        }

        return $pedigreeProgenyArr;
    }

    //  * @Groups({"pedigree:read"})
    //  */
    public function getSiblings() {
        $siblings = [];
        return $this->id;
    }


    // public function getParent() {
    //     $parent = [
    //         "germplasmDbId" =>,
    //         "germplasmName" =>,
    //         "parentType" =>
    //     ];
    //     return $parent;
    // }

    // /**
    //  * @Groups({"pedigree:read"})
    //  */
    // public function getPedigree() {
    //     return $this->id;
    // }

    // /**
    //  * @Groups({"pedigree:read"})
    //  */
    // public function getProgeny() {
    //     $progeny = [
    //         "germplasmDbId" =>,
    //         "germplasmName" =>,
    //         "parentType" =>
    //     ];
    //     return $progeny;
    // }

    /**
     * @Groups({"pedigree:read"})
     */
    // public function getSiblings() {
    //     $siblings = [
    //         "germplasmDbId" =>,
    //         "germplasmName" =>
    //     ];
    //     return $siblings;
    // }

    /**
     * @Groups({"pedigree:read"})
     */
    // public function getExternalReferences() {
    //     $externalReferences = [
    //         "externalReferenceId" =>,
    //         "referenceSource" =>
    //     ];
    //     return $externalReferences;
    // }

    /**
     * @Groups({"pedigree:read"})
     */
    // public function getCrossAttributes() {
    //     $crossAttribute = [
    //         "crossAttributeName" => "... No name",
    //         "crossAttributeValue" => "... No value",
    //     ];
    //     return $crossAttribute;
    // }

    /**
     * @Groups({"pedigree:read"})
     */
    // public function getPollinationEvents() {
    //     $pollinationEvents = [
    //         "pollinationNumber" => "... N/A",
    //         "pollinationSuccessful" => "... N/A",
    //         "pollinationTimeStamp" => $this->year,
    //     ];
    //     return $pollinationEvents;

    // }
}
