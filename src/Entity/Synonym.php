<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SynonymRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=SynonymRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"synonym:read"}},
 *      denormalizationContext={"groups"={"synonym:write"}}
 * )
 */
class Synonym
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"synonym:read", "accession:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"synonym:read", "accession:read"})
     */
    private $tgrc;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"synonym:read", "accession:read"})
     */
    private $usda;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"synonym:read", "accession:read"})
     */
    private $comav;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fma01;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $uib;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pgr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $eusol;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cccode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ndl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avrc;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $inra;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $unitus;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resqProject360;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reseq150;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=Accession::class, inversedBy="synonyms")
     */
    private $accession;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="synonyms")
     */
    private $createdBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTgrc(): ?string
    {
        return $this->tgrc;
    }

    public function setTgrc(?string $tgrc): self
    {
        $this->tgrc = $tgrc;

        return $this;
    }

    public function getUsda(): ?string
    {
        return $this->usda;
    }

    public function setUsda(?string $usda): self
    {
        $this->usda = $usda;

        return $this;
    }

    public function getComav(): ?string
    {
        return $this->comav;
    }

    public function setComav(?string $comav): self
    {
        $this->comav = $comav;

        return $this;
    }

    public function getFma01(): ?string
    {
        return $this->fma01;
    }

    public function setFma01(?string $fma01): self
    {
        $this->fma01 = $fma01;

        return $this;
    }

    public function getUib(): ?string
    {
        return $this->uib;
    }

    public function setUib(?string $uib): self
    {
        $this->uib = $uib;

        return $this;
    }

    public function getPgr(): ?string
    {
        return $this->pgr;
    }

    public function setPgr(?string $pgr): self
    {
        $this->pgr = $pgr;

        return $this;
    }

    public function getEusol(): ?string
    {
        return $this->eusol;
    }

    public function setEusol(?string $eusol): self
    {
        $this->eusol = $eusol;

        return $this;
    }

    public function getCccode(): ?string
    {
        return $this->cccode;
    }

    public function setCccode(?string $cccode): self
    {
        $this->cccode = $cccode;

        return $this;
    }

    public function getNdl(): ?string
    {
        return $this->ndl;
    }

    public function setNdl(?string $ndl): self
    {
        $this->ndl = $ndl;

        return $this;
    }

    public function getAvrc(): ?string
    {
        return $this->avrc;
    }

    public function setAvrc(?string $avrc): self
    {
        $this->avrc = $avrc;

        return $this;
    }

    public function getInra(): ?string
    {
        return $this->inra;
    }

    public function setInra(?string $inra): self
    {
        $this->inra = $inra;

        return $this;
    }

    public function getUnitus(): ?string
    {
        return $this->unitus;
    }

    public function setUnitus(?string $unitus): self
    {
        $this->unitus = $unitus;

        return $this;
    }

    public function getResqProject360(): ?string
    {
        return $this->resqProject360;
    }

    public function setResqProject360(?string $resqProject360): self
    {
        $this->resqProject360 = $resqProject360;

        return $this;
    }

    public function getReseq150(): ?string
    {
        return $this->reseq150;
    }

    public function setReseq150(?string $reseq150): self
    {
        $this->reseq150 = $reseq150;

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

    public function getAccession(): ?Accession
    {
        return $this->accession;
    }

    public function setAccession(?Accession $accession): self
    {
        $this->accession = $accession;

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
}
