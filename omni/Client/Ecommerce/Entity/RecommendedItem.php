<?php
/**
 * THIS IS AN AUTOGENERATED FILE
 * DO NOT MODIFY
 */


namespace Ls\Omni\Client\Ecommerce\Entity;

class RecommendedItem
{

    /**
     * @property boolean $AllowedToSell
     */
    protected $AllowedToSell = null;

    /**
     * @property string $Description
     */
    protected $Description = null;

    /**
     * @property string $Details
     */
    protected $Details = null;

    /**
     * @property string $Id
     */
    protected $Id = null;

    /**
     * @property ArrayOfImageView $Images
     */
    protected $Images = null;

    /**
     * @property ArrayOfAttribute $ItemAttributes
     */
    protected $ItemAttributes = null;

    /**
     * @property ArrayOfPrice $Price
     */
    protected $Price = null;

    /**
     * @property string $ProductGroupId
     */
    protected $ProductGroupId = null;

    /**
     * @property string $SalesUomId
     */
    protected $SalesUomId = null;

    /**
     * @property ArrayOfUOM $UOMs
     */
    protected $UOMs = null;

    /**
     * @property ArrayOfVariant $Variants
     */
    protected $Variants = null;

    /**
     * @property ArrayOfVariantExt $VariantsExt
     */
    protected $VariantsExt = null;

    /**
     * @property ArrayOfVariantRegistration $VariantsRegistration
     */
    protected $VariantsRegistration = null;

    /**
     * @property float $Rating
     */
    protected $Rating = null;

    /**
     * @property string $Reasoning
     */
    protected $Reasoning = null;

    /**
     * @param boolean $AllowedToSell
     * @return $this
     */
    public function setAllowedToSell($AllowedToSell)
    {
        $this->AllowedToSell = $AllowedToSell;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getAllowedToSell()
    {
        return $this->AllowedToSell;
    }

    /**
     * @param string $Description
     * @return $this
     */
    public function setDescription($Description)
    {
        $this->Description = $Description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->Description;
    }

    /**
     * @param string $Details
     * @return $this
     */
    public function setDetails($Details)
    {
        $this->Details = $Details;
        return $this;
    }

    /**
     * @return string
     */
    public function getDetails()
    {
        return $this->Details;
    }

    /**
     * @param string $Id
     * @return $this
     */
    public function setId($Id)
    {
        $this->Id = $Id;
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @param ArrayOfImageView $Images
     * @return $this
     */
    public function setImages($Images)
    {
        $this->Images = $Images;
        return $this;
    }

    /**
     * @return ArrayOfImageView
     */
    public function getImages()
    {
        return $this->Images;
    }

    /**
     * @param ArrayOfAttribute $ItemAttributes
     * @return $this
     */
    public function setItemAttributes($ItemAttributes)
    {
        $this->ItemAttributes = $ItemAttributes;
        return $this;
    }

    /**
     * @return ArrayOfAttribute
     */
    public function getItemAttributes()
    {
        return $this->ItemAttributes;
    }

    /**
     * @param ArrayOfPrice $Price
     * @return $this
     */
    public function setPrice($Price)
    {
        $this->Price = $Price;
        return $this;
    }

    /**
     * @return ArrayOfPrice
     */
    public function getPrice()
    {
        return $this->Price;
    }

    /**
     * @param string $ProductGroupId
     * @return $this
     */
    public function setProductGroupId($ProductGroupId)
    {
        $this->ProductGroupId = $ProductGroupId;
        return $this;
    }

    /**
     * @return string
     */
    public function getProductGroupId()
    {
        return $this->ProductGroupId;
    }

    /**
     * @param string $SalesUomId
     * @return $this
     */
    public function setSalesUomId($SalesUomId)
    {
        $this->SalesUomId = $SalesUomId;
        return $this;
    }

    /**
     * @return string
     */
    public function getSalesUomId()
    {
        return $this->SalesUomId;
    }

    /**
     * @param ArrayOfUOM $UOMs
     * @return $this
     */
    public function setUOMs($UOMs)
    {
        $this->UOMs = $UOMs;
        return $this;
    }

    /**
     * @return ArrayOfUOM
     */
    public function getUOMs()
    {
        return $this->UOMs;
    }

    /**
     * @param ArrayOfVariant $Variants
     * @return $this
     */
    public function setVariants($Variants)
    {
        $this->Variants = $Variants;
        return $this;
    }

    /**
     * @return ArrayOfVariant
     */
    public function getVariants()
    {
        return $this->Variants;
    }

    /**
     * @param ArrayOfVariantExt $VariantsExt
     * @return $this
     */
    public function setVariantsExt($VariantsExt)
    {
        $this->VariantsExt = $VariantsExt;
        return $this;
    }

    /**
     * @return ArrayOfVariantExt
     */
    public function getVariantsExt()
    {
        return $this->VariantsExt;
    }

    /**
     * @param ArrayOfVariantRegistration $VariantsRegistration
     * @return $this
     */
    public function setVariantsRegistration($VariantsRegistration)
    {
        $this->VariantsRegistration = $VariantsRegistration;
        return $this;
    }

    /**
     * @return ArrayOfVariantRegistration
     */
    public function getVariantsRegistration()
    {
        return $this->VariantsRegistration;
    }

    /**
     * @param float $Rating
     * @return $this
     */
    public function setRating($Rating)
    {
        $this->Rating = $Rating;
        return $this;
    }

    /**
     * @return float
     */
    public function getRating()
    {
        return $this->Rating;
    }

    /**
     * @param string $Reasoning
     * @return $this
     */
    public function setReasoning($Reasoning)
    {
        $this->Reasoning = $Reasoning;
        return $this;
    }

    /**
     * @return string
     */
    public function getReasoning()
    {
        return $this->Reasoning;
    }


}
