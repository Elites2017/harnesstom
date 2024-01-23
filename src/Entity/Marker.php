<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\MarkerRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=MarkerRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"marker:read"}},
 *      denormalizationContext={"groups"={"marker:write"}}
 * )
 */
class Marker
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"})
     */
    private $linkageGroupName;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"})
     */
    private $position;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"})
     */
    private $start;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"})
     */
    private $end;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"})
     */
    private $refAllele;

    /**
     * @ORM\Column(type="array")
     * @Groups({"marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"})
     */
    private $altAllele = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"})
     */
    private $primerName1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"})
     */
    private $primerSeq1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"})
     */
    private $primerName2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"})
     */
    private $primerSeq2;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=GenotypingPlatform::class, inversedBy="markers")
     * @Groups({"marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"})
     */
    private $genotypingPlatform;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="markers")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=GWASVariant::class, mappedBy="marker")
     * @Groups({"marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"})
     */
    private $gWASVariants;

    /**
     * @ORM\OneToMany(targetEntity=VariantSet::class, mappedBy="marker")
     * @Groups({"marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"})
     */
    private $variantSets;

    /**
     * @ORM\OneToMany(targetEntity=QTLVariant::class, mappedBy="closestMarker")
     * @Groups({"marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"})
     */
    private $qTLVariants;

    /**
     * @ORM\OneToMany(targetEntity=QTLVariant::class, mappedBy="flankingMarkerStart")
     * @Groups({"marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"})
     */
    private $fMarkerStartQTLVariants;

    /**
     * @ORM\OneToMany(targetEntity=QTLVariant::class, mappedBy="flankingMarkerEnd")
     * @Groups({"marker:read", "mapping_population:read", "country:read", "contact:read", "study:read"})
     */
    private $fMarkerEndQTLVariants;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $platformNameBuffer;

    /**
     * @ORM\OneToMany(targetEntity=MarkerSynonym::class, mappedBy="markerName")
     */
    private $markerSynonyms;

    public function __construct()
    {
        $this->gWASVariants = new ArrayCollection();
        $this->variantSets = new ArrayCollection();
        $this->qTLVariants = new ArrayCollection();
        $this->fMarkerStartQTLVariants = new ArrayCollection();
        $this->fMarkerEndQTLVariants = new ArrayCollection();
        $this->markerSynonyms = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLinkageGroupName(): ?string
    {
        return $this->linkageGroupName;
    }

    public function setLinkageGroupName(string $linkageGroupName): self
    {
        $this->linkageGroupName = $linkageGroupName;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getStart(): ?int
    {
        return $this->start;
    }

    public function setStart(?int $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?int
    {
        return $this->end;
    }

    public function setEnd(?int $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getRefAllele(): ?string
    {
        return $this->refAllele;
    }

    public function setRefAllele(?string $refAllele): self
    {
        $this->refAllele = $refAllele;

        return $this;
    }

    public function getAltAllele(): ?array
    {
        return $this->altAllele;
    }

    public function setAltAllele(?array $altAllele): self
    {
        $this->altAllele = $altAllele;

        return $this;
    }

    public function getPrimerName1(): ?string
    {
        return $this->primerName1;
    }

    public function setPrimerName1(?string $primerName1): self
    {
        $this->primerName1 = $primerName1;

        return $this;
    }

    public function getPrimerSeq1(): ?string
    {
        return $this->primerSeq1;
    }

    public function setPrimerSeq1(?string $primerSeq1): self
    {
        $this->primerSeq1 = $primerSeq1;

        return $this;
    }

    public function getPrimerName2(): ?string
    {
        return $this->primerName2;
    }

    public function setPrimerName2(?string $primerName2): self
    {
        $this->primerName2 = $primerName2;

        return $this;
    }

    public function getPrimerSeq2(): ?string
    {
        return $this->primerSeq2;
    }

    public function setPrimerSeq2(?string $primerSeq2): self
    {
        $this->primerSeq2 = $primerSeq2;

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

    public function getGenotypingPlatform(): ?GenotypingPlatform
    {
        return $this->genotypingPlatform;
    }

    public function setGenotypingPlatform(?GenotypingPlatform $genotypingPlatform): self
    {
        $this->genotypingPlatform = $genotypingPlatform;

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
     * @return Collection<int, GWASVariant>
     */
    public function getGWASVariants(): Collection
    {
        return $this->gWASVariants;
    }

    public function addGWASVariant(GWASVariant $gWASVariant): self
    {
        if (!$this->gWASVariants->contains($gWASVariant)) {
            $this->gWASVariants[] = $gWASVariant;
            $gWASVariant->setMarker($this);
        }

        return $this;
    }

    public function removeGWASVariant(GWASVariant $gWASVariant): self
    {
        if ($this->gWASVariants->removeElement($gWASVariant)) {
            // set the owning side to null (unless already changed)
            if ($gWASVariant->getMarker() === $this) {
                $gWASVariant->setMarker(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, VariantSet>
     */
    public function getVariantSets(): Collection
    {
        return $this->variantSets;
    }

    public function addVariantSet(VariantSet $variantSet): self
    {
        if (!$this->variantSets->contains($variantSet)) {
            $this->variantSets[] = $variantSet;
            $variantSet->setMarker($this);
        }

        return $this;
    }

    public function removeVariantSet(VariantSet $variantSet): self
    {
        if ($this->variantSets->removeElement($variantSet)) {
            // set the owning side to null (unless already changed)
            if ($variantSet->getMarker() === $this) {
                $variantSet->setMarker(null);
            }
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
            $qTLVariant->setClosestMarker($this);
        }

        return $this;
    }

    public function removeQTLVariant(QTLVariant $qTLVariant): self
    {
        if ($this->qTLVariants->removeElement($qTLVariant)) {
            // set the owning side to null (unless already changed)
            if ($qTLVariant->getClosestMarker() === $this) {
                $qTLVariant->setClosestMarker(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QTLVariant>
     */
    public function getFMarkerStartQTLVariants(): Collection
    {
        return $this->fMarkerStartQTLVariants;
    }

    public function addFMarkerStartQTLVariant(QTLVariant $qTLVariant): self
    {
        if (!$this->fMarkerStartQTLVariants->contains($qTLVariant)) {
            $this->fMarkerStartQTLVariants[] = $qTLVariant;
            $qTLVariant->setFlankingMarkerStart($this);
        }

        return $this;
    }

    public function removeFMarkerStartQTLVariant(QTLVariant $qTLVariant): self
    {
        if ($this->fMarkerStartQTLVariants->removeElement($qTLVariant)) {
            // set the owning side to null (unless already changed)
            if ($qTLVariant->getFlankingMarkerStart() === $this) {
                $qTLVariant->setFlankingMarkerStart(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QTLVariant>
     */
    public function getFMarkerEndQTLVariants(): Collection
    {
        return $this->fMarkerEndQTLVariants;
    }

    public function addFMarkerEndQTLVariant(QTLVariant $qTLVariant): self
    {
        if (!$this->fMarkerEndQTLVariants->contains($qTLVariant)) {
            $this->fMarkerEndQTLVariants[] = $qTLVariant;
            $qTLVariant->setFlankingMarkerEnd($this);
        }

        return $this;
    }

    public function removeFMarkerEndQTLVariant(QTLVariant $qTLVariant): self
    {
        if ($this->fMarkerEndQTLVariants->removeElement($qTLVariant)) {
            // set the owning side to null (unless already changed)
            if ($qTLVariant->getFlankingMarkerEnd() === $this) {
                $qTLVariant->setFlankingMarkerEnd(null);
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

    public function getPlatformNameBuffer(): ?string
    {
        return $this->platformNameBuffer;
    }

    public function setPlatformNameBuffer(?string $platformNameBuffer): self
    {
        $this->platformNameBuffer = $platformNameBuffer;

        return $this;
    }

    /**
     * @return Collection<int, MarkerSynonym>
     */
    public function getMarkerSynonyms(): Collection
    {
        return $this->markerSynonyms;
    }

    public function addMarkerSynonym(MarkerSynonym $markerSynonym): self
    {
        if (!$this->markerSynonyms->contains($markerSynonym)) {
            $this->markerSynonyms[] = $markerSynonym;
            $markerSynonym->setMarkerName($this);
        }

        return $this;
    }

    public function removeMarkerSynonym(MarkerSynonym $markerSynonym): self
    {
        if ($this->markerSynonyms->removeElement($markerSynonym)) {
            // set the owning side to null (unless already changed)
            if ($markerSynonym->getMarkerName() === $this) {
                $markerSynonym->setMarkerName(null);
            }
        }

        return $this;
    }

    // API September 2023 . BrAPI 2.1

    /**
     * @Groups({"marker:read"})
     */
    public function getVariantDbId() {
        return $this->name;
    }

    /**
     * @Groups({"marker:read"})
     */
    public function getAdditionalInfo() {
        $addInfo = [
            "genotypingPlatform" => [
                "name" => $this->genotypingPlatform ? $this->genotypingPlatform->getName() : null,
                "description" => $this->genotypingPlatform ? $this->genotypingPlatform->getDescription() : null,
                "markerCount" => $this->genotypingPlatform ? $this->genotypingPlatform->getMarkerCount() : null,
                "bioProjectId" => $this->genotypingPlatform ? $this->genotypingPlatform->getBioProjectID() : null
            ]

        ];
        return $addInfo;
    }

    /**
     * @Groups({"marker:read"})
     */
    public function getAlternateBases() {
        return $this->altAllele;
    }

    /**
     * @Groups({"marker:read"})
     */
    public function getReferenceBases() {
        return $this->refAllele;
    }

    /**
     * @Groups({"marker:read"})
     */
    public function getReferenceName() {
        return $this->linkageGroupName;
    }

    /**
     * @Groups({"marker:read"})
     */
    public function getReferenceSetName() {
        return $this->genotypingPlatform ? $this->genotypingPlatform->getRefSetName() : null;
    }

    /**
     * @Groups({"marker:read"})
     */
    public function getVariantNames() {
        $synonyms = [];
        foreach ($this->markerSynonyms as $key => $oneSynonym) {
            # code...
            $synonyms [] = $oneSynonym->getMarkerName();
        }
        return $synonyms;
    }

    /**
     * @Groups({"marker:read"})
     */
    public function getVariantType() {
        return $this->type;
    }

    
}
