<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\GWASStatTestRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=GWASStatTestRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"gwas_s_t:read"}},
 *      denormalizationContext={"groups"={"gwas_s_t:write"}}
 * )
 */
class GWASStatTest
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"gwas_s_t:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"gwas_s_t:read"})
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="gWASStatTests")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=GWAS::class, mappedBy="gwasStatTest")
     */
    private $gWAS;

    /**
     * @ORM\ManyToOne(targetEntity=GWASStatTest::class, inversedBy="gWASStatTests")
     */
    private $parentTerm;

    /**
     * @ORM\OneToMany(targetEntity=GWASStatTest::class, mappedBy="parentTerm")
     */
    private $gWASStatTests;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    public function __construct()
    {
        $this->gWAS = new ArrayCollection();
        $this->gWASStatTests = new ArrayCollection();
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
            $gWA->setGwasStatTest($this);
        }

        return $this;
    }

    public function removeGWA(GWAS $gWA): self
    {
        if ($this->gWAS->removeElement($gWA)) {
            // set the owning side to null (unless already changed)
            if ($gWA->getGwasStatTest() === $this) {
                $gWA->setGwasStatTest(null);
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
    public function getGWASStatTests(): Collection
    {
        return $this->gWASStatTests;
    }

    public function addGWASStatTest(self $gWASStatTest): self
    {
        if (!$this->gWASStatTests->contains($gWASStatTest)) {
            $this->gWASStatTests[] = $gWASStatTest;
            $gWASStatTest->setParentTerm($this);
        }

        return $this;
    }

    public function removeGWASStatTest(self $gWASStatTest): self
    {
        if ($this->gWASStatTests->removeElement($gWASStatTest)) {
            // set the owning side to null (unless already changed)
            if ($gWASStatTest->getParentTerm() === $this) {
                $gWASStatTest->setParentTerm(null);
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
}
