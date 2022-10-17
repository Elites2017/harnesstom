<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TaxonomyRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=TaxonomyRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"study:read"}},
 *      denormalizationContext={"groups"={"study:write"}}
 * )
 */
class Taxonomy
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"taxonomy:read", "accession:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"taxonomy:read", "accession:read"})
     */
    private $taxonid;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"taxonomy:read", "accession:read"})
     */
    private $genus;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"taxonomy:read", "accession:read"})
     */
    private $species;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"taxonomy:read", "accession:read"})
     */
    private $subtaxa;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="taxonomies")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Accession::class, mappedBy="taxon")
     */
    private $accessions;

    public function __construct()
    {
        $this->accessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaxonid(): ?string
    {
        return $this->taxonid;
    }

    public function setTaxonid(string $taxonid): self
    {
        $this->taxonid = $taxonid;

        return $this;
    }

    public function getGenus(): ?string
    {
        return $this->genus;
    }

    public function setGenus(string $genus): self
    {
        $this->genus = $genus;

        return $this;
    }

    public function getSpecies(): ?string
    {
        return $this->species;
    }

    public function setSpecies(string $species): self
    {
        $this->species = $species;

        return $this;
    }

    public function getSubtaxa(): ?string
    {
        return $this->subtaxa;
    }

    public function setSubtaxa(string $subtaxa): self
    {
        $this->subtaxa = $subtaxa;

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
     * @return Collection<int, Accession>
     */
    public function getAccessions(): Collection
    {
        return $this->accessions;
    }

    public function addAccession(Accession $accession): self
    {
        if (!$this->accessions->contains($accession)) {
            $this->accessions[] = $accession;
            $accession->setTaxon($this);
        }

        return $this;
    }

    public function removeAccession(Accession $accession): self
    {
        if ($this->accessions->removeElement($accession)) {
            // set the owning side to null (unless already changed)
            if ($accession->getTaxon() === $this) {
                $accession->setTaxon(null);
            }
        }

        return $this;
    }

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->taxonid ."  ". $this->genus ." ". $this->species ." ". $this->subtaxa;
    }
}
