<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AnalyteRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=AnalyteRepository::class)
 * @ApiResource(attributes={
 *  "normalizationContext"={"groups"={"analyte:read"}, "enable_max_depth"=true},
 *  "denormalizationContext"={"groups"={"analyte:write"}}
 * })
 */
class Analyte
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
    private $name;

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
    private $AnalyteCode;

    /**
     * @ORM\Column(type="float", nullable=true)
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
    private $retentionTime;

    /**
     * @ORM\Column(type="float", nullable=true)
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
    private $massToChargeRatio;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=AnnotationLevel::class, inversedBy="analytes")
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "data_type:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "kinship_algorithm:read", "trial:read",
     * "metabolic_trait:read", "metabolite_class:read", "gwas:read", "gwas_model:read", "qtl_study:ready", "observation_level:read",
     * "germplasm_s_i:read", "genotyping_platform:read", "user:read", "person:read", "enzyme:read", "factor_type:read", "season:read",
     * "location:read", "growth_f_t:read", "experimental_d_t:read", "study_p_v:read", "study_image:read", "study_g_i:read", "crop:read",
     * "biological_status:read", "analyte:read", "analyte_class:read", "allelic_e_e:read", "analyte_f_h:read", "anatomical_entity:read",
     * "attribute:read", "attribute_category:read", "attribute_t_v:read", "breeding_method:read", "ci_criteria:read",
     * "collecting_mission:read", "collecting_source:read", "contact:read", "genetic_t_m:read", "gwas_stat_test:read", "gwas_variant:read",
     * "identifcation_level:read", "metabolite_value", "method_class:read", "observation_value:read", "qtl_method:read", "qtl_statistic:read",
     * "qtl_e_e:read", "qtl_variant:read", "scale:read", "scale_category:read", "software:read", "sequencing_type:read", "taxonomy:read",
     * "sequencing_instrument:read", "storage_type:read", "structure_method:read", "synonym:read", "threshold_method:read", "unit:read",
     * "trait_class:read", "trait_processing:read", "trial_type:read", "var_c_s:read", "variant_set:read", "variant_s_m:read"})
     */
    private $annotationLevel;

    /**
     * @ORM\ManyToOne(targetEntity=IdentificationLevel::class, inversedBy="analytes")
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "data_type:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "kinship_algorithm:read", "trial:read",
     * "metabolic_trait:read", "metabolite_class:read", "gwas:read", "gwas_model:read", "qtl_study:ready", "observation_level:read",
     * "germplasm_s_i:read", "genotyping_platform:read", "user:read", "person:read", "enzyme:read", "factor_type:read", "season:read",
     * "location:read", "growth_f_t:read", "experimental_d_t:read", "study_p_v:read", "study_image:read", "study_g_i:read", "crop:read",
     * "biological_status:read", "analyte:read", "analyte_class:read", "allelic_e_e:read", "analyte_f_h:read", "anatomical_entity:read",
     * "annotation_level:read", "attribute:read", "attribute_category:read", "attribute_t_v:read", "breeding_method:read", "ci_criteria:read",
     * "collecting_mission:read", "collecting_source:read", "contact:read", "genetic_t_m:read", "gwas_stat_test:read", "gwas_variant:read",
     * "metabolite_value", "method_class:read", "observation_value:read", "qtl_method:read", "qtl_statistic:read",
     * "qtl_e_e:read", "qtl_variant:read", "scale:read", "scale_category:read", "software:read", "sequencing_type:read", "taxonomy:read",
     * "sequencing_instrument:read", "storage_type:read", "structure_method:read", "synonym:read", "threshold_method:read", "unit:read",
     * "trait_class:read", "trait_processing:read", "trial_type:read", "var_c_s:read", "variant_set:read", "variant_s_m:read"})
     */
    private $identificationLevel;

    /**
     * @ORM\ManyToOne(targetEntity=ObservationVariableMethod::class, inversedBy="analytes")
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "data_type:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "kinship_algorithm:read", "trial:read",
     * "metabolic_trait:read", "metabolite_class:read", "gwas:read", "gwas_model:read", "qtl_study:ready",
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
    private $observationVariableMethod;

    /**
     * @ORM\ManyToOne(targetEntity=AnalyteClass::class, inversedBy="analytes")
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "data_type:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "kinship_algorithm:read", "trial:read",
     * "metabolic_trait:read", "metabolite_class:read", "gwas:read", "gwas_model:read", "qtl_study:ready", "observation_level:read",
     * "germplasm_s_i:read", "genotyping_platform:read", "user:read", "person:read", "enzyme:read", "factor_type:read", "season:read",
     * "location:read", "growth_f_t:read", "experimental_d_t:read", "study_p_v:read", "study_image:read", "study_g_i:read", "crop:read",
     * "biological_status:read", "analyte:read", "allelic_e_e:read", "analyte_f_h:read", "anatomical_entity:read",
     * "annotation_level:read", "attribute:read", "attribute_category:read", "attribute_t_v:read", "breeding_method:read", "ci_criteria:read",
     * "collecting_mission:read", "collecting_source:read", "contact:read", "genetic_t_m:read", "gwas_stat_test:read", "gwas_variant:read",
     * "identifcation_level:read", "metabolite_value", "method_class:read", "observation_value:read", "qtl_method:read", "qtl_statistic:read",
     * "qtl_e_e:read", "qtl_variant:read", "scale:read", "scale_category:read", "software:read", "sequencing_type:read", "taxonomy:read",
     * "sequencing_instrument:read", "storage_type:read", "structure_method:read", "synonym:read", "threshold_method:read", "unit:read",
     * "trait_class:read", "trait_processing:read", "trial_type:read", "var_c_s:read", "variant_set:read", "variant_s_m:read"})
     */
    private $analyteClass;

    /**
     * @ORM\ManyToOne(targetEntity=AnalyteFlavorHealth::class, inversedBy="analytes")
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "data_type:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "kinship_algorithm:read", "trial:read",
     * "metabolic_trait:read", "metabolite_class:read", "gwas:read", "gwas_model:read", "qtl_study:ready", "observation_level:read",
     * "germplasm_s_i:read", "genotyping_platform:read", "user:read", "person:read", "enzyme:read", "factor_type:read", "season:read",
     * "location:read", "growth_f_t:read", "experimental_d_t:read", "study_p_v:read", "study_image:read", "study_g_i:read", "crop:read",
     * "biological_status:read", "analyte:read", "analyte_class:read", "allelic_e_e:read", "anatomical_entity:read",
     * "annotation_level:read", "attribute:read", "attribute_category:read", "attribute_t_v:read", "breeding_method:read", "ci_criteria:read",
     * "collecting_mission:read", "collecting_source:read", "contact:read", "genetic_t_m:read", "gwas_stat_test:read", "gwas_variant:read",
     * "identifcation_level:read", "metabolite_value", "method_class:read", "observation_value:read", "qtl_method:read", "qtl_statistic:read",
     * "qtl_e_e:read", "qtl_variant:read", "scale:read", "scale_category:read", "software:read", "sequencing_type:read", "taxonomy:read",
     * "sequencing_instrument:read", "storage_type:read", "structure_method:read", "synonym:read", "threshold_method:read", "unit:read",
     * "trait_class:read", "trait_processing:read", "trial_type:read", "var_c_s:read", "variant_set:read", "variant_s_m:read"})
     */
    private $healthAndFlavor;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="analytes")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Metabolite::class, mappedBy="analyte")
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "data_type:read", "study:read",
     * "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
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
    private $metabolites;

    public function __construct()
    {
        $this->metabolites = new ArrayCollection();
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

    public function getAnalyteCode(): ?string
    {
        return $this->AnalyteCode;
    }

    public function setAnalyteCode(string $AnalyteCode): self
    {
        $this->AnalyteCode = $AnalyteCode;

        return $this;
    }

    public function getRetentionTime(): ?float
    {
        return $this->retentionTime;
    }

    public function setRetentionTime(?float $retentionTime): self
    {
        $this->retentionTime = $retentionTime;

        return $this;
    }

    public function getMassToChargeRatio(): ?float
    {
        return $this->massToChargeRatio;
    }

    public function setMassToChargeRatio(?float $massToChargeRatio): self
    {
        $this->massToChargeRatio = $massToChargeRatio;

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

    public function getAnnotationLevel(): ?AnnotationLevel
    {
        return $this->annotationLevel;
    }

    public function setAnnotationLevel(?AnnotationLevel $annotationLevel): self
    {
        $this->annotationLevel = $annotationLevel;

        return $this;
    }

    public function getIdentificationLevel(): ?IdentificationLevel
    {
        return $this->identificationLevel;
    }

    public function setIdentificationLevel(?IdentificationLevel $identificationLevel): self
    {
        $this->identificationLevel = $identificationLevel;

        return $this;
    }

    public function getObservationVariableMethod(): ?ObservationVariableMethod
    {
        return $this->observationVariableMethod;
    }

    public function setObservationVariableMethod(?ObservationVariableMethod $observationVariableMethod): self
    {
        $this->observationVariableMethod = $observationVariableMethod;

        return $this;
    }

    public function getAnalyteClass(): ?AnalyteClass
    {
        return $this->analyteClass;
    }

    public function setAnalyteClass(?AnalyteClass $analyteClass): self
    {
        $this->analyteClass = $analyteClass;

        return $this;
    }

    public function getHealthAndFlavor(): ?AnalyteFlavorHealth
    {
        return $this->healthAndFlavor;
    }

    public function setHealthAndFlavor(?AnalyteFlavorHealth $healthAndFlavor): self
    {
        $this->healthAndFlavor = $healthAndFlavor;

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
     * @return Collection<int, Metabolite>
     */
    public function getMetabolites(): Collection
    {
        return $this->metabolites;
    }

    public function addMetabolite(Metabolite $metabolite): self
    {
        if (!$this->metabolites->contains($metabolite)) {
            $this->metabolites[] = $metabolite;
            $metabolite->setAnalyte($this);
        }

        return $this;
    }

    public function removeMetabolite(Metabolite $metabolite): self
    {
        if ($this->metabolites->removeElement($metabolite)) {
            // set the owning side to null (unless already changed)
            if ($metabolite->getAnalyte() === $this) {
                $metabolite->setAnalyte(null);
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
