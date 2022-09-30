<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProgramRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=ProgramRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"program:read"}},
 *      denormalizationContext={"groups"={"program:write"}}
 * )
 */
class Program
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"program:read"})
     * @SerializedName("programDbId")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"program:read"})
     * @SerializedName("programName")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"program:read"})
     */
    private $abbreviation;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"program:read"})
     */
    private $objective;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"program:read"})
     * @SerializedName("documentationURL")
     */
    private $externalRef;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=Crop::class, inversedBy="programs")
     * @Groups({"program:read"})
     */
    private $crop;

    /**
     * @ORM\ManyToOne(targetEntity=Contact::class, inversedBy="programs")
     */
    private $contact;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="programs")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Trial::class, mappedBy="program")
     * @Groups({"program:read"})
     */
    private $trials;

    /**
     * @ORM\OneToMany(targetEntity=Germplasm::class, mappedBy="program")
     * @Groups({"program:read"})
     */
    private $germplasms;

    public function __construct()
    {
        $this->trials = new ArrayCollection();
        $this->germplasms = new ArrayCollection();
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

    public function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(string $abbreviation): self
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }

    public function getObjective(): ?string
    {
        return $this->objective;
    }

    public function setObjective(?string $objective): self
    {
        $this->objective = $objective;

        return $this;
    }

    public function getExternalRef(): ?string
    {
        return $this->externalRef;
    }

    public function setExternalRef(?string $externalRef): self
    {
        $this->externalRef = $externalRef;

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

    public function getCrop(): ?Crop
    {
        return $this->crop;
    }

    public function setCrop(?Crop $crop): self
    {
        $this->crop = $crop;

        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;

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
     * @return Collection<int, Trial>
     */
    public function getTrials(): Collection
    {
        return $this->trials;
    }

    public function addTrial(Trial $trial): self
    {
        if (!$this->trials->contains($trial)) {
            $this->trials[] = $trial;
            $trial->setProgram($this);
        }

        return $this;
    }

    public function removeTrial(Trial $trial): self
    {
        if ($this->trials->removeElement($trial)) {
            // set the owning side to null (unless already changed)
            if ($trial->getProgram() === $this) {
                $trial->setProgram(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Germplasm>
     */
    public function getGermplasms(): Collection
    {
        return $this->germplasms;
    }

    public function addGermplasm(Germplasm $germplasm): self
    {
        if (!$this->germplasms->contains($germplasm)) {
            $this->germplasms[] = $germplasm;
            $germplasm->setProgram($this);
        }

        return $this;
    }

    public function removeGermplasm(Germplasm $germplasm): self
    {
        if ($this->germplasms->removeElement($germplasm)) {
            // set the owning side to null (unless already changed)
            if ($germplasm->getProgram() === $this) {
                $germplasm->setProgram(null);
            }
        }

        return $this;
    }

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->name ." ". $this->abbreviation;
    }

    // API SECTION
    /**
     * @Groups({"program:read"})
     */
    public function getLeadPersonName(){
        return $this->contact->getPerson()->getFirstName() ." ". $this->contact->getPerson()->getMiddleName() ." ".$this->contact->getPerson()->getLastName();
    }

    /**
     * @Groups({"program:read"})
     */
    public function getLeadPersonDbId(){
        return $this->contact->getOrcid();
    }

    /**
     * @Groups({"program:read"})
     */
    public function getCommonCropName(){
        return $this->crop->getCommonCropName();
    }
}
