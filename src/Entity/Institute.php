<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\InstituteRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=InstituteRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"institute:read"}},
 *      denormalizationContext={"groups"={"institute:write"}}
 * )
 */
class Institute
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"institute:read", "contact:read", "program:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"institute:read", "contact:read", "program:read"})
     */
    private $instcode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"institute:read", "contact:read", "program:read"})
     */
    private $acronym;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"institute:read", "contact:read", "program:read"})
     * @SerializedName("instituteName")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"institute:read", "contact:read", "program:read"})
     */
    private $streetNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"institute:read", "contact:read", "program:read"})
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"institute:read", "contact:read", "program:read"})
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity=Country::class, inversedBy="institutes")
     * @Groups({"institute:read", "contact:read", "program:read"})
     */
    private $country;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="institutes")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Contact::class, mappedBy="institute")
     * @Groups({"institute:read"})
     */
    private $contacts;

    /**
     * @ORM\OneToMany(targetEntity=CollectingMission::class, mappedBy="institute")
     * @Groups({"institute:read", "contact:read", "program:read"})
     */
    private $collectingMissions;

    /**
     * @ORM\OneToMany(targetEntity=Accession::class, mappedBy="instcode")
     * @Groups({"institute:read", "contact:read", "program:read"})
     */
    private $accessions;

    /**
     * @ORM\OneToMany(targetEntity=Study::class, mappedBy="institute")
     * @Groups({"institute:read", "contact:read", "program:read"})
     */
    private $studies;

    /**
     * @ORM\OneToMany(targetEntity=Cross::class, mappedBy="institute")
     * @Groups({"institute:read", "contact:read", "program:read"})
     */
    private $crosses;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
        $this->collectingMissions = new ArrayCollection();
        $this->accessions = new ArrayCollection();
        $this->studies = new ArrayCollection();
        $this->crosses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInstcode(): ?string
    {
        return $this->instcode;
    }

    public function setInstcode(string $instcode): self
    {
        $this->instcode = $instcode;

        return $this;
    }

    public function getAcronym(): ?string
    {
        return $this->acronym;
    }

    public function setAcronym(string $acronym): self
    {
        $this->acronym = $acronym;

        return $this;
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

    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    public function setStreetNumber(?string $streetNumber): self
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

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
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setInstitute($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getInstitute() === $this) {
                $contact->setInstitute(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CollectingMission>
     */
    public function getCollectingMissions(): Collection
    {
        return $this->collectingMissions;
    }

    public function addCollectingMission(CollectingMission $collectingMission): self
    {
        if (!$this->collectingMissions->contains($collectingMission)) {
            $this->collectingMissions[] = $collectingMission;
            $collectingMission->setInstitute($this);
        }

        return $this;
    }

    public function removeCollectingMission(CollectingMission $collectingMission): self
    {
        if ($this->collectingMissions->removeElement($collectingMission)) {
            // set the owning side to null (unless already changed)
            if ($collectingMission->getInstitute() === $this) {
                $collectingMission->setInstitute(null);
            }
        }

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
            $accession->setInstcode($this);
        }

        return $this;
    }

    public function removeAccession(Accession $accession): self
    {
        if ($this->accessions->removeElement($accession)) {
            // set the owning side to null (unless already changed)
            if ($accession->getInstcode() === $this) {
                $accession->setInstcode(null);
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
            $study->setInstitute($this);
        }

        return $this;
    }

    public function removeStudy(Study $study): self
    {
        if ($this->studies->removeElement($study)) {
            // set the owning side to null (unless already changed)
            if ($study->getInstitute() === $this) {
                $study->setInstitute(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cross>
     */
    public function getCrosses(): Collection
    {
        return $this->crosses;
    }

    public function addCross(Cross $cross): self
    {
        if (!$this->crosses->contains($cross)) {
            $this->crosses[] = $cross;
            $cross->setInstitute($this);
        }

        return $this;
    }

    public function removeCross(Cross $cross): self
    {
        if ($this->crosses->removeElement($cross)) {
            // set the owning side to null (unless already changed)
            if ($cross->getInstitute() === $this) {
                $cross->setInstitute(null);
            }
        }

        return $this;
    }

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->name ." ". $this->acronym;
    }
}
