<?php

namespace App\Entity;

use App\Repository\GenotypingPlatformRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GenotypingPlatformRepository::class)
 */
class GenotypingPlatform
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $methodDescription;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $refSetName;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $publishedDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bioProjectID;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $markerCount;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $assemblyPUI;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $publicationRef = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=SequencingType::class, inversedBy="genotypingPlatforms")
     */
    private $sequencingType;

    /**
     * @ORM\ManyToOne(targetEntity=SequencingInstrument::class, inversedBy="genotypingPlatforms")
     */
    private $sequencingInstrument;

    /**
     * @ORM\ManyToOne(targetEntity=VarCallSoftware::class, inversedBy="genotypingPlatforms")
     */
    private $varCallSoftware;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="genotypingPlatforms")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Marker::class, mappedBy="genotypingPlatform")
     */
    private $markers;

    /**
     * @ORM\OneToMany(targetEntity=VariantSetMetadata::class, mappedBy="genotypingPlatform")
     */
    private $variantSetMetadata;

    public function __construct()
    {
        $this->markers = new ArrayCollection();
        $this->variantSetMetadata = new ArrayCollection();
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

    public function getMethodDescription(): ?string
    {
        return $this->methodDescription;
    }

    public function setMethodDescription(?string $methodDescription): self
    {
        $this->methodDescription = $methodDescription;

        return $this;
    }

    public function getRefSetName(): ?string
    {
        return $this->refSetName;
    }

    public function setRefSetName(?string $refSetName): self
    {
        $this->refSetName = $refSetName;

        return $this;
    }

    public function getPublishedDate(): ?\DateTimeInterface
    {
        return $this->publishedDate;
    }

    public function setPublishedDate(?\DateTimeInterface $publishedDate): self
    {
        $this->publishedDate = $publishedDate;

        return $this;
    }

    public function getBioProjectID(): ?string
    {
        return $this->bioProjectID;
    }

    public function setBioProjectID(?string $bioProjectID): self
    {
        $this->bioProjectID = $bioProjectID;

        return $this;
    }

    public function getMarkerCount(): ?int
    {
        return $this->markerCount;
    }

    public function setMarkerCount(?int $markerCount): self
    {
        $this->markerCount = $markerCount;

        return $this;
    }

    public function getAssemblyPUI(): ?string
    {
        return $this->assemblyPUI;
    }

    public function setAssemblyPUI(string $assemblyPUI): self
    {
        $this->assemblyPUI = $assemblyPUI;

        return $this;
    }

    public function getPublicationRef(): ?array
    {
        return $this->publicationRef;
    }

    public function setPublicationRef(?array $publicationRef): self
    {
        $this->publicationRef = $publicationRef;

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

    public function getSequencingType(): ?SequencingType
    {
        return $this->sequencingType;
    }

    public function setSequencingType(?SequencingType $sequencingType): self
    {
        $this->sequencingType = $sequencingType;

        return $this;
    }

    public function getSequencingInstrument(): ?SequencingInstrument
    {
        return $this->sequencingInstrument;
    }

    public function setSequencingInstrument(?SequencingInstrument $sequencingInstrument): self
    {
        $this->sequencingInstrument = $sequencingInstrument;

        return $this;
    }

    public function getVarCallSoftware(): ?VarCallSoftware
    {
        return $this->varCallSoftware;
    }

    public function setVarCallSoftware(?VarCallSoftware $varCallSoftware): self
    {
        $this->varCallSoftware = $varCallSoftware;

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
     * @return Collection<int, Marker>
     */
    public function getMarkers(): Collection
    {
        return $this->markers;
    }

    public function addMarker(Marker $marker): self
    {
        if (!$this->markers->contains($marker)) {
            $this->markers[] = $marker;
            $marker->setGenotypingPlatform($this);
        }

        return $this;
    }

    public function removeMarker(Marker $marker): self
    {
        if ($this->markers->removeElement($marker)) {
            // set the owning side to null (unless already changed)
            if ($marker->getGenotypingPlatform() === $this) {
                $marker->setGenotypingPlatform(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, VariantSetMetadata>
     */
    public function getVariantSetMetadata(): Collection
    {
        return $this->variantSetMetadata;
    }

    public function addVariantSetMetadata(VariantSetMetadata $variantSetMetadata): self
    {
        if (!$this->variantSetMetadata->contains($variantSetMetadata)) {
            $this->variantSetMetadata[] = $variantSetMetadata;
            $variantSetMetadata->setGenotypingPlatform($this);
        }

        return $this;
    }

    public function removeVariantSetMetadata(VariantSetMetadata $variantSetMetadata): self
    {
        if ($this->variantSetMetadata->removeElement($variantSetMetadata)) {
            // set the owning side to null (unless already changed)
            if ($variantSetMetadata->getGenotypingPlatform() === $this) {
                $variantSetMetadata->setGenotypingPlatform(null);
            }
        }

        return $this;
    }
}
