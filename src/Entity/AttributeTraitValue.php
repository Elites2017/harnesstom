<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AttributeTraitValueRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=AttributeTraitValueRepository::class)
 * @ApiResource(attributes={
 *  "normalizationContext"={"groups"={"attribute_t_v:read"}, "enable_max_depth"=true},
 *  "denormalizationContext"={"groups"={"attribute_t_v:write"}}
 * })
 */
class AttributeTraitValue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "data_type:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "kinship_algorithm:read", "trial:read",
     * "metabolic_trait:read", "metabolite_class:read", "gwas:read", "gwas_model:read", "qtl_study:ready", "observation_level:read",
     * "germplasm_s_i:read", "genotyping_platform:read", "user:read", "person:read", "enzyme:read", "factor_type:read", "season:read",
     * "location:read", "growth_f_t:read", "experimental_d_t:read", "study_p_v:read", "study_image:read", "study_g_i:read", "crop:read",
     * "biological_status:read", "analyte:read", "analyte_class:read", "allelic_e_e:read", "analyte_f_h:read", "anatomical_entity:read",
     * "annotation_level:read", "attribute:read", "attribute_category:read", "attribute_t_v:read", "breeding_method:read", "ci_criteria:read",
     * "collecting_mission:read", "collecting_source:read", "contact:read", "genetic_t_m:read", "gwas_stat_test:read", "gwas_variant:read",
     * "identifcation_level:read", "metabolite_value", "method_class:read", "observation_value:read", "qtl_method:read", "qtl_statistic:read",
     * "qtl_e_e:read", "qtl_variant:read", "scale:read", "scale_category:read", "software:read", "sequencing_type:read", "taxonomy:read",
     * "sequencing_instrument:read", "storage_type:read", "structure_method:read", "synonym:read", "threshold_method:read", "unit:read",
     * "trait_class:read", "trait_processing:read", "trial_type:read", "var_c_s:read", "variant_set:read", "variant_s_m:read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TraitClass::class, inversedBy="attributeTraitValues")
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "data_type:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "kinship_algorithm:read", "trial:read",
     * "metabolic_trait:read", "metabolite_class:read", "gwas:read", "gwas_model:read", "qtl_study:ready", "observation_level:read",
     * "germplasm_s_i:read", "genotyping_platform:read", "user:read", "person:read", "enzyme:read", "factor_type:read", "season:read",
     * "location:read", "growth_f_t:read", "experimental_d_t:read", "study_p_v:read", "study_image:read", "study_g_i:read", "crop:read",
     * "biological_status:read", "analyte:read", "analyte_class:read", "allelic_e_e:read", "analyte_f_h:read", "anatomical_entity:read",
     * "annotation_level:read", "attribute:read", "attribute_category:read", "attribute_t_v:read", "breeding_method:read", "ci_criteria:read",
     * "collecting_mission:read", "collecting_source:read", "contact:read", "genetic_t_m:read", "gwas_stat_test:read", "gwas_variant:read",
     * "identifcation_level:read", "metabolite_value", "method_class:read", "observation_value:read", "qtl_method:read", "qtl_statistic:read",
     * "qtl_e_e:read", "qtl_variant:read", "scale:read", "scale_category:read", "software:read", "sequencing_type:read", "taxonomy:read",
     * "sequencing_instrument:read", "storage_type:read", "structure_method:read", "synonym:read", "threshold_method:read", "unit:read",
     * "trait_class:read", "trait_processing:read", "trial_type:read", "var_c_s:read", "variant_set:read", "variant_s_m:read"})
     */
    private $trait;

    /**
     * @ORM\ManyToOne(targetEntity=MetabolicTrait::class, inversedBy="attributeTraitValues")
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "data_type:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "kinship_algorithm:read", "trial:read",
     * "metabolite_class:read", "gwas:read", "gwas_model:read", "qtl_study:ready", "observation_level:read",
     * "germplasm_s_i:read", "genotyping_platform:read", "user:read", "person:read", "enzyme:read", "factor_type:read", "season:read",
     * "location:read", "growth_f_t:read", "experimental_d_t:read", "study_p_v:read", "study_image:read", "study_g_i:read", "crop:read",
     * "biological_status:read", "analyte:read", "analyte_class:read", "allelic_e_e:read", "analyte_f_h:read", "anatomical_entity:read",
     * "annotation_level:read", "attribute:read", "attribute_category:read", "attribute_t_v:read", "breeding_method:read", "ci_criteria:read",
     * "collecting_mission:read", "collecting_source:read", "contact:read", "genetic_t_m:read", "gwas_stat_test:read", "gwas_variant:read",
     * "identifcation_level:read", "metabolite_value", "method_class:read", "observation_value:read", "qtl_method:read", "qtl_statistic:read",
     * "qtl_e_e:read", "qtl_variant:read", "scale:read", "scale_category:read", "software:read", "sequencing_type:read", "taxonomy:read",
     * "sequencing_instrument:read", "storage_type:read", "structure_method:read", "synonym:read", "threshold_method:read", "unit:read",
     * "trait_class:read", "trait_processing:read", "trial_type:read", "var_c_s:read", "variant_set:read", "variant_s_m:read"})
     */
    private $metabolicTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Attribute::class, inversedBy="attributeTraitValues")
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "data_type:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "kinship_algorithm:read", "trial:read",
     * "metabolic_trait:read", "metabolite_class:read", "gwas:read", "gwas_model:read", "qtl_study:ready", "observation_level:read",
     * "germplasm_s_i:read", "genotyping_platform:read", "user:read", "person:read", "enzyme:read", "factor_type:read", "season:read",
     * "location:read", "growth_f_t:read", "experimental_d_t:read", "study_p_v:read", "study_image:read", "study_g_i:read", "crop:read",
     * "biological_status:read", "analyte:read", "analyte_class:read", "allelic_e_e:read", "analyte_f_h:read", "anatomical_entity:read",
     * "annotation_level:read", "attribute_category:read", "attribute_t_v:read", "breeding_method:read", "ci_criteria:read",
     * "collecting_mission:read", "collecting_source:read", "contact:read", "genetic_t_m:read", "gwas_stat_test:read", "gwas_variant:read",
     * "identifcation_level:read", "metabolite_value", "method_class:read", "observation_value:read", "qtl_method:read", "qtl_statistic:read",
     * "qtl_e_e:read", "qtl_variant:read", "scale:read", "scale_category:read", "software:read", "sequencing_type:read", "taxonomy:read",
     * "sequencing_instrument:read", "storage_type:read", "structure_method:read", "synonym:read", "threshold_method:read", "unit:read",
     * "trait_class:read", "trait_processing:read", "trial_type:read", "var_c_s:read", "variant_set:read", "variant_s_m:read"})
     */
    private $attribute;

    /**
     * @ORM\ManyToOne(targetEntity=Accession::class, inversedBy="attributeTraitValues")
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "data_type:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "cross:read", "sample:read", "institute:read", "kinship_algorithm:read", "trial:read",
     * "metabolic_trait:read", "metabolite_class:read", "gwas:read", "gwas_model:read", "qtl_study:ready", "observation_level:read",
     * "germplasm_s_i:read", "genotyping_platform:read", "user:read", "person:read", "enzyme:read", "factor_type:read", "season:read",
     * "location:read", "growth_f_t:read", "experimental_d_t:read", "study_p_v:read", "study_image:read", "study_g_i:read", "crop:read",
     * "biological_status:read", "analyte:read", "analyte_class:read", "allelic_e_e:read", "analyte_f_h:read", "anatomical_entity:read",
     * "annotation_level:read", "attribute:read", "attribute_category:read", "attribute_t_v:read", "breeding_method:read", "ci_criteria:read",
     * "collecting_mission:read", "collecting_source:read", "contact:read", "genetic_t_m:read", "gwas_stat_test:read", "gwas_variant:read",
     * "identifcation_level:read", "metabolite_value", "method_class:read", "observation_value:read", "qtl_method:read", "qtl_statistic:read",
     * "qtl_e_e:read", "qtl_variant:read", "scale:read", "scale_category:read", "software:read", "sequencing_type:read", "taxonomy:read",
     * "sequencing_instrument:read", "storage_type:read", "structure_method:read", "synonym:read", "threshold_method:read", "unit:read",
     * "trait_class:read", "trait_processing:read", "trial_type:read", "var_c_s:read", "variant_set:read", "variant_s_m:read"})
     */
    private $accession;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "data_type:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "kinship_algorithm:read", "trial:read",
     * "metabolic_trait:read", "metabolite_class:read", "gwas:read", "gwas_model:read", "qtl_study:ready", "observation_level:read",
     * "germplasm_s_i:read", "genotyping_platform:read", "user:read", "person:read", "enzyme:read", "factor_type:read", "season:read",
     * "location:read", "growth_f_t:read", "experimental_d_t:read", "study_p_v:read", "study_image:read", "study_g_i:read", "crop:read",
     * "biological_status:read", "analyte:read", "analyte_class:read", "allelic_e_e:read", "analyte_f_h:read", "anatomical_entity:read",
     * "annotation_level:read", "attribute:read", "attribute_category:read", "attribute_t_v:read", "breeding_method:read", "ci_criteria:read",
     * "collecting_mission:read", "collecting_source:read", "contact:read", "genetic_t_m:read", "gwas_stat_test:read", "gwas_variant:read",
     * "identifcation_level:read", "metabolite_value", "method_class:read", "observation_value:read", "qtl_method:read", "qtl_statistic:read",
     * "qtl_e_e:read", "qtl_variant:read", "scale:read", "scale_category:read", "software:read", "sequencing_type:read", "taxonomy:read",
     * "sequencing_instrument:read", "storage_type:read", "structure_method:read", "synonym:read", "threshold_method:read", "unit:read",
     * "trait_class:read", "trait_processing:read", "trial_type:read", "var_c_s:read", "variant_set:read", "variant_s_m:read"})
     */
    private $value;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "data_type:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "kinship_algorithm:read", "trial:read",
     * "metabolic_trait:read", "metabolite_class:read", "gwas:read", "gwas_model:read", "qtl_study:ready", "observation_level:read",
     * "germplasm_s_i:read", "genotyping_platform:read", "user:read", "person:read", "enzyme:read", "factor_type:read", "season:read",
     * "location:read", "growth_f_t:read", "experimental_d_t:read", "study_p_v:read", "study_image:read", "study_g_i:read", "crop:read",
     * "biological_status:read", "analyte:read", "analyte_class:read", "allelic_e_e:read", "analyte_f_h:read", "anatomical_entity:read",
     * "annotation_level:read", "attribute:read", "attribute_category:read", "attribute_t_v:read", "breeding_method:read", "ci_criteria:read",
     * "collecting_mission:read", "collecting_source:read", "contact:read", "genetic_t_m:read", "gwas_stat_test:read", "gwas_variant:read",
     * "identifcation_level:read", "metabolite_value", "method_class:read", "observation_value:read", "qtl_method:read", "qtl_statistic:read",
     * "qtl_e_e:read", "qtl_variant:read", "scale:read", "scale_category:read", "software:read", "sequencing_type:read", "taxonomy:read",
     * "sequencing_instrument:read", "storage_type:read", "structure_method:read", "synonym:read", "threshold_method:read", "unit:read",
     * "trait_class:read", "trait_processing:read", "trial_type:read", "var_c_s:read", "variant_set:read", "variant_s_m:read"})
     */
    private $publicationReference = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="attributeTraitValues")
     */
    private $createdBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrait(): ?TraitClass
    {
        return $this->trait;
    }

    public function setTrait(?TraitClass $trait): self
    {
        $this->trait = $trait;

        return $this;
    }

    public function getMetabolicTrait(): ?MetabolicTrait
    {
        return $this->metabolicTrait;
    }

    public function setMetabolicTrait(?MetabolicTrait $metabolicTrait): self
    {
        $this->metabolicTrait = $metabolicTrait;

        return $this;
    }

    public function getAttribute(): ?Attribute
    {
        return $this->attribute;
    }

    public function setAttribute(?Attribute $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getAccession(): ?Accession
    {
        return $this->accession;
    }

    public function setAccession(?Accession $accession): self
    {
        $this->accession = $accession;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

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

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->trait;
    }
}
