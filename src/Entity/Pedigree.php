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
     * @ORM\OneToOne(targetEntity=Cross::class, inversedBy="pedigree", cascade={"persist", "remove"})
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "contact:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "observation_variable:read"})
     */
    private $pedigreeCross;

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

    public function __construct()
    {
        $this->germplasm = new ArrayCollection();
        $this->mappingPopulations = new ArrayCollection();
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

    public function getPedigreeCross(): ?Cross
    {
        return $this->pedigreeCross;
    }

    public function setPedigreeCross(?Cross $pedigreeCross): self
    {
        $this->pedigreeCross = $pedigreeCross;

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
}
