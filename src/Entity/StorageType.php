<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\StorageTypeRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=StorageTypeRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"storage_type:read"}},
 *      denormalizationContext={"groups"={"synonym:write"}}
 * )
 */
class StorageType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"storage_type:read", "accession:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"storage_type:read", "accession:read"})
     * @SerializedName("description")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"storage_type:read", "accession:read"})
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="storageTypes")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Accession::class, mappedBy="storage")
     * @Groups({"storage_type:read"})
     */
    private $accessions;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=StorageType::class, inversedBy="storageTypes")
     */
    private $parentTerm;

    /**
     * @ORM\OneToMany(targetEntity=StorageType::class, mappedBy="parentTerm")
     */
    private $storageTypes;

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
        $this->accessions = new ArrayCollection();
        $this->storageTypes = new ArrayCollection();
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

    public function setOntologyId(?string $ontology_id): self
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
            $accession->setStorage($this);
        }

        return $this;
    }

    public function removeAccession(Accession $accession): self
    {
        if ($this->accessions->removeElement($accession)) {
            // set the owning side to null (unless already changed)
            if ($accession->getStorage() === $this) {
                $accession->setStorage(null);
            }
        }

        return $this;
    }

    // create a toString method to return the object name / ontology_id which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->name;
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
    public function getStorageTypes(): Collection
    {
        return $this->storageTypes;
    }

    public function addStorageType(self $storageType): self
    {
        if (!$this->storageTypes->contains($storageType)) {
            $this->storageTypes[] = $storageType;
            $storageType->setParentTerm($this);
        }

        return $this;
    }

    public function removeStorageType(self $storageType): self
    {
        if ($this->storageTypes->removeElement($storageType)) {
            // set the owning side to null (unless already changed)
            if ($storageType->getParentTerm() === $this) {
                $storageType->setParentTerm(null);
            }
        }

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
