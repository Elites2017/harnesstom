<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\GWASModelRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=GWASModelRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"gwas:read"}},
 *      denormalizationContext={"groups"={"gwas:write"}}
 * )
 */
class GWASModel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"gwas:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"gwas:read"})
     */
    private $name;

    /**
     * 
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private $ontology_id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="gWASModels")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=GWAS::class, mappedBy="gwasModel")
     */
    private $gWAS;

    /**
     * @ORM\ManyToOne(targetEntity=GWASModel::class, inversedBy="gWASModels")
     */
    private $parentTerm;

    /**
     * @ORM\OneToMany(targetEntity=GWASModel::class, mappedBy="parentTerm")
     */
    private $gWASModels;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $par_ont;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_poau;

    public function __construct()
    {
        $this->gWAS = new ArrayCollection();
        $this->gWASModels = new ArrayCollection();
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

    public function getOntologyId(): ?string
    {
        return $this->ontology_id;
    }

    public function setOntologyId(string $ontology_id): self
    {
        $this->ontology_id = $ontology_id;

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
            $gWA->setGwasModel($this);
        }

        return $this;
    }

    public function removeGWA(GWAS $gWA): self
    {
        if ($this->gWAS->removeElement($gWA)) {
            // set the owning side to null (unless already changed)
            if ($gWA->getGwasModel() === $this) {
                $gWA->setGwasModel(null);
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

    public function getParentTerm(): ?self
    {
        return $this->parentTerm;
    }

    public function setParentTerm(?self $parentTerm): self
    {
        $this->parentTerm = $parentTerm;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getGWASModels(): Collection
    {
        return $this->gWASModels;
    }

    public function addGWASModel(self $gWASModel): self
    {
        if (!$this->gWASModels->contains($gWASModel)) {
            $this->gWASModels[] = $gWASModel;
            $gWASModel->setParentTerm($this);
        }

        return $this;
    }

    public function removeGWASModel(self $gWASModel): self
    {
        if ($this->gWASModels->removeElement($gWASModel)) {
            // set the owning side to null (unless already changed)
            if ($gWASModel->getParentTerm() === $this) {
                $gWASModel->setParentTerm(null);
            }
        }

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

    public function getParOnt(): ?string
    {
        return $this->par_ont;
    }

    public function setParOnt(string $par_ont): self
    {
        $this->par_ont = $par_ont;

        return $this;
    }

    public function getIsPoau(): ?bool
    {
        return $this->is_poau;
    }

    public function setIsPoau(?bool $is_poau): self
    {
        $this->is_poau = $is_poau;

        return $this;
    }
}
