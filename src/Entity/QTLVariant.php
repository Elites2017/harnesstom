<?php

namespace App\Entity;

use App\Repository\QTLVariantRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QTLVariantRepository::class)
 */
class QTLVariant
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
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $createdBy;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $publicationReference = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $locusName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $locus;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $r2QTLxE;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $r2Global;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $statisticQTLxEValue;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $r2;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $dA;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $dominance;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $additive;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $qtlStatsValue;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $positiveAllele;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ciStart;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ciEnd;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $detectName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $originalTraitName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $peakPosition;

    /**
     * @ORM\ManyToOne(targetEntity=QTLStudy::class, inversedBy="qTLVariants")
     */
    private $qtlStudy;

    /**
     * @ORM\ManyToOne(targetEntity=ObservationVariable::class, inversedBy="qTLVariants")
     */
    private $observationVariable;

    /**
     * @ORM\ManyToOne(targetEntity=Metabolite::class, inversedBy="qTLVariants")
     */
    private $metabolite;

    /**
     * @ORM\ManyToOne(targetEntity=Marker::class, inversedBy="qTLVariants")
     */
    private $closestMarker;

    /**
     * @ORM\ManyToOne(targetEntity=Marker::class, inversedBy="qTLVariants")
     */
    private $flankingMarkerStart;

    /**
     * @ORM\ManyToOne(targetEntity=Marker::class, inversedBy="qTLVariants")
     */
    private $flankingMarkerEnd;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkageGroupName;

    /**
     * @ORM\ManyToOne(targetEntity=Germplasm::class, inversedBy="qTLVariants")
     */
    private $positiveAlleleParent;

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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

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

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

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

    public function getLocusName(): ?string
    {
        return $this->locusName;
    }

    public function setLocusName(?string $locusName): self
    {
        $this->locusName = $locusName;

        return $this;
    }

    public function getLocus(): ?string
    {
        return $this->locus;
    }

    public function setLocus(?string $locus): self
    {
        $this->locus = $locus;

        return $this;
    }

    public function getR2QTLxE(): ?float
    {
        return $this->r2QTLxE;
    }

    public function setR2QTLxE(?float $r2QTLxE): self
    {
        $this->r2QTLxE = $r2QTLxE;

        return $this;
    }

    public function getR2Global(): ?float
    {
        return $this->r2Global;
    }

    public function setR2Global(?float $r2Global): self
    {
        $this->r2Global = $r2Global;

        return $this;
    }

    public function getStatisticQTLxEValue(): ?float
    {
        return $this->statisticQTLxEValue;
    }

    public function setStatisticQTLxEValue(?float $statisticQTLxEValue): self
    {
        $this->statisticQTLxEValue = $statisticQTLxEValue;

        return $this;
    }

    public function getR2(): ?float
    {
        return $this->r2;
    }

    public function setR2(?float $r2): self
    {
        $this->r2 = $r2;

        return $this;
    }

    public function getDA(): ?float
    {
        return $this->dA;
    }

    public function setDA(?float $dA): self
    {
        $this->dA = $dA;

        return $this;
    }

    public function getDominance(): ?float
    {
        return $this->dominance;
    }

    public function setDominance(?float $dominance): self
    {
        $this->dominance = $dominance;

        return $this;
    }

    public function getAdditive(): ?float
    {
        return $this->additive;
    }

    public function setAdditive(?float $additive): self
    {
        $this->additive = $additive;

        return $this;
    }

    public function getQtlStatsValue(): ?float
    {
        return $this->qtlStatsValue;
    }

    public function setQtlStatsValue(?float $qtlStatsValue): self
    {
        $this->qtlStatsValue = $qtlStatsValue;

        return $this;
    }

    public function getPositiveAllele(): ?string
    {
        return $this->positiveAllele;
    }

    public function setPositiveAllele(?string $positiveAllele): self
    {
        $this->positiveAllele = $positiveAllele;

        return $this;
    }

    public function getCiStart(): ?int
    {
        return $this->ciStart;
    }

    public function setCiStart(?int $ciStart): self
    {
        $this->ciStart = $ciStart;

        return $this;
    }

    public function getCiEnd(): ?int
    {
        return $this->ciEnd;
    }

    public function setCiEnd(?int $ciEnd): self
    {
        $this->ciEnd = $ciEnd;

        return $this;
    }

    public function getDetectName(): ?string
    {
        return $this->detectName;
    }

    public function setDetectName(?string $detectName): self
    {
        $this->detectName = $detectName;

        return $this;
    }

    public function getOriginalTraitName(): ?string
    {
        return $this->originalTraitName;
    }

    public function setOriginalTraitName(?string $originalTraitName): self
    {
        $this->originalTraitName = $originalTraitName;

        return $this;
    }

    public function getPeakPosition(): ?int
    {
        return $this->peakPosition;
    }

    public function setPeakPosition(?int $peakPosition): self
    {
        $this->peakPosition = $peakPosition;

        return $this;
    }

    public function getQtlStudy(): ?QTLStudy
    {
        return $this->qtlStudy;
    }

    public function setQtlStudy(?QTLStudy $qtlStudy): self
    {
        $this->qtlStudy = $qtlStudy;

        return $this;
    }

    public function getObservationVariable(): ?ObservationVariable
    {
        return $this->observationVariable;
    }

    public function setObservationVariable(?ObservationVariable $observationVariable): self
    {
        $this->observationVariable = $observationVariable;

        return $this;
    }

    public function getMetabolite(): ?Metabolite
    {
        return $this->metabolite;
    }

    public function setMetabolite(?Metabolite $metabolite): self
    {
        $this->metabolite = $metabolite;

        return $this;
    }

    public function getClosestMarker(): ?Marker
    {
        return $this->closestMarker;
    }

    public function setClosestMarker(?Marker $closestMarker): self
    {
        $this->closestMarker = $closestMarker;

        return $this;
    }

    public function getFlankingMarkerStart(): ?Marker
    {
        return $this->flankingMarkerStart;
    }

    public function setFlankingMarkerStart(?Marker $flankingMarkerStart): self
    {
        $this->flankingMarkerStart = $flankingMarkerStart;

        return $this;
    }

    public function getFlankingMarkerEnd(): ?Marker
    {
        return $this->flankingMarkerEnd;
    }

    public function setFlankingMarkerEnd(?Marker $flankingMarkerEnd): self
    {
        $this->flankingMarkerEnd = $flankingMarkerEnd;

        return $this;
    }

    public function getLinkageGroupName(): ?string
    {
        return $this->linkageGroupName;
    }

    public function setLinkageGroupName(?string $linkageGroupName): self
    {
        $this->linkageGroupName = $linkageGroupName;

        return $this;
    }

    public function getPositiveAlleleParent(): ?Germplasm
    {
        return $this->positiveAlleleParent;
    }

    public function setPositiveAlleleParent(?Germplasm $positiveAlleleParent): self
    {
        $this->positiveAlleleParent = $positiveAlleleParent;

        return $this;
    }
}
