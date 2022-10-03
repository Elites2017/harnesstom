<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GenerationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=GenerationRepository::class)
 */
class Generation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private $ontology_id;

    /**
     * @ORM\ManyToOne(targetEntity=Generation::class, inversedBy="generations")
     */
    private $parentTerm;

    /**
     * @ORM\OneToMany(targetEntity=Pedigree::class, mappedBy="generation")
     */
    private $clear;

    /**
     * @ORM\OneToMany(targetEntity=Pedigree::class, mappedBy="generation")
     */
    private $pedigrees;

    public function __construct()
    {
        $this->generations = new ArrayCollection();
        $this->clear = new ArrayCollection();
        $this->pedigrees = new ArrayCollection();
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

    public function getOntologyId(): ?string
    {
        return $this->ontology_id;
    }

    public function setOntologyId(string $ontology_id): self
    {
        $this->ontology_id = $ontology_id;

        return $this;
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
     * @return Collection<int, Pedigree>
     */
    public function getClear(): Collection
    {
        return $this->clear;
    }

    public function addClear(Pedigree $clear): self
    {
        if (!$this->clear->contains($clear)) {
            $this->clear[] = $clear;
            $clear->setGeneration($this);
        }

        return $this;
    }

    public function removeClear(Pedigree $clear): self
    {
        if ($this->clear->removeElement($clear)) {
            // set the owning side to null (unless already changed)
            if ($clear->getGeneration() === $this) {
                $clear->setGeneration(null);
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
            $pedigree->setGeneration($this);
        }

        return $this;
    }

    public function removePedigree(Pedigree $pedigree): self
    {
        if ($this->pedigrees->removeElement($pedigree)) {
            // set the owning side to null (unless already changed)
            if ($pedigree->getGeneration() === $this) {
                $pedigree->setGeneration(null);
            }
        }

        return $this;
    }
}
