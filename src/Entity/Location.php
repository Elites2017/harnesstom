<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\LocationRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use PhpParser\Node\Expr\Cast\Array_;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=LocationRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"location:read"}},
 *      denormalizationContext={"groups"={"location:write"}}
 * )
 */
class Location
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"location:read", "country:read", "contact:read", "study:read"})
     * @SerializedName("locationDbId")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"location:read", "country:read", "contact:read", "study:read"})
     * @SerializedName("locationName")
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
     * @Groups({"location:read", "country:read", "contact:read", "study:read"})
     */
    private $longitudeCo;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
     * @Groups({"location:read", "country:read", "contact:read", "study:read"})
     */
    private $latitudeCo;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
     * @Groups({"location:read", "country:read", "contact:read", "study:read"})
     */
    private $altitudeCo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"location:read", "country:read", "contact:read", "study:read"})
     */
    private $siteStatus;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="locations")
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity=Country::class, inversedBy="locations")
     * @Groups({"location:read", "study:read"})
     */
    private $country;

    /**
     * @ORM\OneToMany(targetEntity=Study::class, mappedBy="location")
     * @Groups({"location:read", "country:read"})
     */
    private $studies;

    // API SECTION
    private $coordinates;

    /**
     * @ORM\Column(type="string", unique=true, length=255)
     */
    private $abbreviation;

    public function __construct()
    {
        $this->studies = new ArrayCollection();
        // API SECTION
        $this->coordinates = new ArrayCollection();
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

    public function getLongitudeCo(): ?string
    {
        return $this->longitudeCo;
    }

    public function setLongitudeCo(?string $longitudeCo): self
    {
        $this->longitudeCo = $longitudeCo;

        return $this;
    }

    public function getLatitudeCo(): ?string
    {
        return $this->latitudeCo;
    }

    public function setLatitudeCo(?string $latitudeCo): self
    {
        $this->latitudeCo = $latitudeCo;

        return $this;
    }

    public function getAltitudeCo(): ?string
    {
        return $this->altitudeCo;
    }

    public function setAltitudeCo(?string $altitudeCo): self
    {
        $this->altitudeCo = $altitudeCo;

        return $this;
    }

    public function getSiteStatus(): ?string
    {
        return $this->siteStatus;
    }

    public function setSiteStatus(?string $siteStatus): self
    {
        $this->siteStatus = $siteStatus;

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

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

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
            $study->setLocation($this);
        }

        return $this;
    }

    public function removeStudy(Study $study): self
    {
        if ($this->studies->removeElement($study)) {
            // set the owning side to null (unless already changed)
            if ($study->getLocation() === $this) {
                $study->setLocation(null);
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

    // API SECTION
    /**
     * @Groups({"location:read"})
     */
    public function getCountryName(){
        return $this->country->getName();
    }

    /**
     * @Groups({"location:read"})
     */
    public function getCountryCode(){
        return $this->country->getIso3();
    }

    /**
     * @Groups({"location:read"})
     */
    public function getCooordinates(): Array
    {
        $this->coordinates = [
            "geometry" => [$this->longitudeCo, $this->latitudeCo, $this->altitudeCo],
            "type" => "Point"
        ];
        return $this->coordinates;
    }

    /**
     * @Groups({"location:read"})
     */
    // public function getInstituteName()
    // {
    //     $this->instituteName = "";
    //     foreach ($this->studies as $OneOfThem) {
    //         $instituteName = $OneOfThem->getInstitute()->getName();
    //     }
    //     return $this->instituteName;
    // }

    // /**
    //  * @Groups({"location:read"})
    //  */
    // public function getInstituteAddress()
    // {
    //     // $this->streetNumber = "";
    //     // foreach ($this->studies as $oneOfThem) {
    //     //     $this->streetNumber = $oneOfThem->getInstitute()->getStreetNumber();
    //     // }
    //     return "";
    // }

    public function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(string $abbreviation): self
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }
}
