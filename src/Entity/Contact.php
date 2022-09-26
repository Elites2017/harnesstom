<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ContactRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=ContactRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"contact:read"}},
 *      denormalizationContext={"groups"={"contact:write"}}
 * )
 */
class Contact
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"contact:read", "program:read", "institute:read", "crop:read", "study:read"})
     * @SerializedName("contactDbId")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"contact:read", "program:read", "institute:read", "crop:read", "study:read"})
     */
    private $orcid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"contact:read", "program:read", "institute:read", "crop:read", "study:read"})
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Person::class, inversedBy="contacts")
     * @Groups({"contact:read", "program:read", "institute:read", "crop:read", "study:read"})
     */
    private $person;

    /**
     * @ORM\ManyToOne(targetEntity=Institute::class, inversedBy="contacts")
     * @Groups({"contact:read", "program:read", "crop:read"})
     */
    private $institute;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="contacts")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Program::class, mappedBy="contact")
     * @Groups({"contact:read"})
     */
    private $programs;

    public function __construct()
    {
        $this->programs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrcid(): ?string
    {
        return $this->orcid;
    }

    public function setOrcid(string $orcid): self
    {
        $this->orcid = $orcid;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function getInstitute(): ?Institute
    {
        return $this->institute;
    }

    public function setInstitute(?Institute $institute): self
    {
        $this->institute = $institute;

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
     * @return Collection<int, Program>
     */
    public function getPrograms(): Collection
    {
        return $this->programs;
    }

    public function addProgram(Program $program): self
    {
        if (!$this->programs->contains($program)) {
            $this->programs[] = $program;
            $program->setContact($this);
        }

        return $this;
    }

    public function removeProgram(Program $program): self
    {
        if ($this->programs->removeElement($program)) {
            // set the owning side to null (unless already changed)
            if ($program->getContact() === $this) {
                $program->setContact(null);
            }
        }

        return $this;
    }

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->orcid;
    }

    // API SECTION
    /**
     * @Groups({"contact:read"})
     */
    public function getName(){
        return $this->person->getFirstName() ." ".  $this->person->getMiddleName() ." ". $this->person->getLastName();
    }

    /**
     * @Groups({"contact:read"})
     */
    public function getEmail(){
        return $this->person->getUser()->getEmail() ." ".  $this->person->getMiddleName() ." ". $this->person->getLastName();
    }

    /**
     * @Groups({"contact:read"})
     */
    public function getInstituteName(){
        return $this->institute->getName();
    }

    /**
     * @Groups({"contact:read"})
     * @return Collection<collection>
     */
    public function getContacts(): Array
    {
        $this->contacts = [
            "contactDbId" => $this->getOrcid(),
            "email" => $this->person->getUser()->getEmail(),
            "instituteName" => $this->institute->getName(),
            "name" => $this->person->getFirstName() ." ". $this->person->getMiddleName() ." ".$this->person->getLastName(),
            "orcid" => $this->orcid,
            "type" => $this->type
        ];
        return $this->contacts;
    }
}
