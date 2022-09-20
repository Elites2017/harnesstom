<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TrialRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=TrialRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"trial:read"}},
 *      denormalizationContext={"groups"={"trial:write"}}
 * )
 */
class Trial
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"trial:read", "study:read"})
     * @SerializedName("trialDbId")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"trial:read", "study:read"})
     * @SerializedName("trialName")
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"trial:read"})
     * @SerializedName("trialDescription")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"trial:read"})s
     */
    private $abbreviation;

    /**
     * @ORM\Column(type="date")
     * @Groups({"trial:read"})
     */
    private $startDate;

    /**
     * @ORM\Column(type="date")
     * @Groups({"trial:read"})
     */
    private $endDate;

    /**
     * @ORM\Column(type="date")
     * @Groups({"trial:read"})
     */
    private $publicReleaseDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"trial:read"})
     */
    private $license;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"trial:read"})
     * @SerializedName("trialPUI")
     */
    private $pui;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @Groups({"trial:read"})
     */
    private $publicationReference = [];

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=Program::class, inversedBy="trials")
     * @Groups({"trial:read"})
     */
    private $program;

    /**
     * @ORM\ManyToOne(targetEntity=TrialType::class, inversedBy="trials")
     * @Groups({"trial:read"})
     */
    private $trialType;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="trials")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=SharedWith::class, mappedBy="trial")
     * @Groups({"trial:read"})
     */
    private $sharedWiths;

    /**
     * @ORM\OneToMany(targetEntity=Study::class, mappedBy="trial")
     * @Groups({"trial:read"})
     */
    private $studies;

    public function __construct()
    {
        $this->sharedWiths = new ArrayCollection();
        $this->studies = new ArrayCollection();
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

    public function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(string $abbreviation): self
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getPublicReleaseDate(): ?\DateTimeInterface
    {
        return $this->publicReleaseDate;
    }

    public function setPublicReleaseDate(\DateTimeInterface $publicReleaseDate): self
    {
        $this->publicReleaseDate = $publicReleaseDate;

        return $this;
    }

    public function getLicense(): ?string
    {
        return $this->license;
    }

    public function setLicense(?string $license): self
    {
        $this->license = $license;

        return $this;
    }

    public function getPui(): ?string
    {
        return $this->pui;
    }

    public function setPui(string $pui): self
    {
        $this->pui = $pui;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
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

    public function getProgram(): ?Program
    {
        return $this->program;
    }

    public function setProgram(?Program $program): self
    {
        $this->program = $program;

        return $this;
    }

    public function getTrialType(): ?TrialType
    {
        return $this->trialType;
    }

    public function setTrialType(?TrialType $trialType): self
    {
        $this->trialType = $trialType;

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
     * @return Collection<int, SharedWith>
     */
    public function getSharedWiths(): Collection
    {
        return $this->sharedWiths;
    }

    public function addSharedWith(SharedWith $sharedWith): self
    {
        if (!$this->sharedWiths->contains($sharedWith)) {
            $this->sharedWiths[] = $sharedWith;
            $sharedWith->setTrial($this);
        }

        return $this;
    }

    public function removeSharedWith(SharedWith $sharedWith): self
    {
        if ($this->sharedWiths->removeElement($sharedWith)) {
            // set the owning side to null (unless already changed)
            if ($sharedWith->getTrial() === $this) {
                $sharedWith->setTrial(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Study>
     */
    public function getStudies(): Collection
    {
        return $this->studies;
    }

    public function addStudy(Study $study): self
    {
        if (!$this->studies->contains($study)) {
            $this->studies[] = $study;
            $study->setTrial($this);
        }

        return $this;
    }

    public function removeStudy(Study $study): self
    {
        if ($this->studies->removeElement($study)) {
            // set the owning side to null (unless already changed)
            if ($study->getTrial() === $this) {
                $study->setTrial(null);
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
}
