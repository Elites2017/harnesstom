<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CrossRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=CrossRepository::class)
 * @ORM\Table(name="`cross`")
 * @ApiResource(
 *      normalizationContext={"groups"={"cross:read"}},
 *      denormalizationContext={"groups"={"cross:write"}}
 * )
 */
class Cross
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"cross:read", "program:read", "location:read", "mapping_population:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"cross:read", "program:read", "location:read", "mapping_population:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"cross:read", "program:read", "location:read", "mapping_population:read"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"cross:read", "program:read", "location:read", "mapping_population:read"})
     */
    private $parent1Type;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"cross:read", "program:read", "location:read", "mapping_population:read"})
     */
    private $parent2Type;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"cross:read", "program:read", "location:read", "mapping_population:read"})
     */
    private $year;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $publicationReference = [];

    /**
     * @ORM\ManyToOne(targetEntity=Study::class, inversedBy="crosses")
     */
    private $study;

    /**
     * @ORM\ManyToOne(targetEntity=Institute::class, inversedBy="crosses")
     */
    private $institute;

    /**
     * @ORM\ManyToOne(targetEntity=BreedingMethod::class, inversedBy="crosses")
     */
    private $breedingMethod;

    /**
     * @ORM\ManyToOne(targetEntity=Germplasm::class, inversedBy="crosses")
     */
    private $parent1;

    /**
     * @ORM\ManyToOne(targetEntity=Germplasm::class, inversedBy="crosses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $parent2;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="crosses")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=MappingPopulation::class, mappedBy="mappingPopulationCross")
     */
    private $mappingPopulations;

    /**
     * @ORM\OneToMany(targetEntity=Pedigree::class, mappedBy="pedigreeCross")
     */
    private $pedigrees;

    /**
     * @ORM\OneToMany(targetEntity=Progeny::class, mappedBy="progenyCross")
     */
    private $progenies;

    private $parents;

    public function __construct()
    {
        $this->mappingPopulations = new ArrayCollection();
        $this->pedigrees = new ArrayCollection();
        $this->progenies = new ArrayCollection();
        $this->parents = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getParent1Type(): ?string
    {
        return $this->parent1Type;
    }

    public function setParent1Type(string $parent1Type): self
    {
        $this->parent1Type = $parent1Type;

        return $this;
    }

    public function getParent2Type(): ?string
    {
        return $this->parent2Type;
    }

    public function setParent2Type(string $parent2Type): self
    {
        $this->parent2Type = $parent2Type;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
    {
        $this->year = $year;

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

    public function getPublicationReference(): ?array
    {
        return $this->publicationReference;
    }

    public function setPublicationReference(?array $publicationReference): self
    {
        $this->publicationReference = $publicationReference;

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

    public function getInstitute(): ?Institute
    {
        return $this->institute;
    }

    public function setInstitute(?Institute $institute): self
    {
        $this->institute = $institute;

        return $this;
    }

    public function getBreedingMethod(): ?BreedingMethod
    {
        return $this->breedingMethod;
    }

    public function setBreedingMethod(?BreedingMethod $breedingMethod): self
    {
        $this->breedingMethod = $breedingMethod;

        return $this;
    }

    public function getParent1(): ?Germplasm
    {
        return $this->parent1;
    }

    public function setParent1(?Germplasm $parent1): self
    {
        $this->parent1 = $parent1;

        return $this;
    }

    public function getParent2(): ?Germplasm
    {
        return $this->parent2;
    }

    public function setParent2(?Germplasm $parent2): self
    {
        $this->parent2 = $parent2;

        return $this;
    }
    
    // DP - Sep 29th 5h08PM
    public function getParents()
    {
        $this->parents [] = $this->parent1;
        $this->parents [] = $this->parent2;
        return $this->parents;
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
            $mappingPopulation->setMappingPopulationCross($this);
        }

        return $this;
    }

    public function removeMappingPopulation(MappingPopulation $mappingPopulation): self
    {
        if ($this->mappingPopulations->removeElement($mappingPopulation)) {
            // set the owning side to null (unless already changed)
            if ($mappingPopulation->getMappingPopulationCross() === $this) {
                $mappingPopulation->setMappingPopulationCross(null);
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
            $pedigree->setPedigreeCross($this);
        }

        return $this;
    }

    public function removePedigree(Pedigree $pedigree): self
    {
        if ($this->pedigrees->removeElement($pedigree)) {
            // set the owning side to null (unless already changed)
            if ($pedigree->getPedigreeCross() === $this) {
                $pedigree->setPedigreeCross(null);
            }
        }

        return $this;
    }

    // API SECTION
    /**
     * @Groups({"cross:read"})
     */
    public function getCrossDbId() {
        return $this->id;
    }

    /**
     * @Groups({"cross:read"})
     */
    public function getCrossName() {
        return $this->name;
    }

    /**
     * @Groups({"cross:read"})
     */
    public function getCrossType() {
        return "type...";
    }

    /**
     * @Groups({"cross:read"})
     */
    public function getCrossingProjectDbId() {
        return $this->study->getId();
    }

    /**
     * @Groups({"cross:read"})
     */
    public function getCrossingProjectName() {
        return $this->study->getName();
    }

    /**
     * @Groups({"cross:read"})
     * @SerializedName("parent1")
     */
    public function getCrossParent1() {
        $parent1 = [
            "germplasmDbid" => $this->parent1->getId(),
            "germplasmName" => $this->parent1->getGermplasmID(),
            "observationUnitDbid" => "",
            "observationUnitName" => "",
            "parentType" => $this->parent1Type

        ];
        return $parent1;
    }

    /**
     * @Groups({"cross:read"})
     * @SerializedName("parent2")
     */
    public function getCrossParent2() {
        $parent2 = [
            "germplasmDbid" => $this->parent2->getId(),
            "germplasmName" => $this->parent2->getGermplasmID(),
            "observationUnitDbid" => "",
            "observationUnitName" => "",
            "parentType" => $this->parent2Type

        ];
        return $parent2;
    }

    /**
     * @Groups({"cross:read"})
     */
    public function getPlannedCrossDbId() {
        return "...";
    }

    /**
     * @Groups({"cross:read"})
     */
    public function getPlannedCrossName() {
        return "...";
    }

    /**
     * @Groups({"cross:read"})
     */
    public function getCrossAttributes() {
        $crossAttribute = [
            "crossAttributeName" => "... No name",
            "crossAttributeValue" => "... No value",
        ];
        return $crossAttribute;
    }

    /**
     * @Groups({"cross:read"})
     */
    public function getPollinationEvents() {
        $pollinationEvents = [
            "pollinationNumber" => "... N/A",
            "pollinationSuccessful" => "... N/A",
            "pollinationTimeStamp" => $this->year,
        ];
        return $pollinationEvents;

    }

    /**
     * @return Collection<int, Progeny>
     */
    public function getProgenies(): Collection
    {
        return $this->progenies;
    }

    public function addProgeny(Progeny $progeny): self
    {
        if (!$this->progenies->contains($progeny)) {
            $this->progenies[] = $progeny;
            $progeny->setProgenyCross($this);
        }

        return $this;
    }

    public function removeProgeny(Progeny $progeny): self
    {
        if ($this->progenies->removeElement($progeny)) {
            // set the owning side to null (unless already changed)
            if ($progeny->getProgenyCross() === $this) {
                $progeny->setProgenyCross(null);
            }
        }

        return $this;
    }
}