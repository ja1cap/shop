<?php

namespace Shop\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class HitProposal
 * @package Shop\MainBundle\Entity
 */
class HitProposal extends AbstractEntity
{

    /**
     * @var string
     */
    private $main_title;

    /**
     * @var string
     */
    private $main_description;

    /**
     * @var string
     */
    private $main_img_file_name;

    /**
     * @var string
     */
    protected $legend;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $image_file_name;

    /**
     * @var string
     */
    protected $discount_price;

    /**
     * @var string
     */
    protected $regular_price;

    /**
     * @var string
     */
    protected $price_legend;

    /**
     * @var string
     */
    protected $bonus;

    /**
     * @var string
     */
    protected $bonus_image_file_name;

    /**
     * @var integer
     */
    protected $id;


    /**
     * Set legend
     *
     * @param string $legend
     * @return HitProposal
     */
    public function setLegend($legend)
    {
        $this->legend = $legend;

        return $this;
    }

    /**
     * Get legend
     *
     * @return string 
     */
    public function getLegend()
    {
        return $this->legend;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return HitProposal
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return HitProposal
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set image_file_name
     *
     * @param string $imageFileName
     * @return HitProposal
     */
    public function setImageFileName($imageFileName)
    {
        $this->image_file_name = $imageFileName;

        return $this;
    }

    /**
     * Get image_file_name
     *
     * @return string 
     */
    public function getImageFileName()
    {
        return $this->image_file_name;
    }

    /**
     * Set discount_price
     *
     * @param string $discountPrice
     * @return HitProposal
     */
    public function setDiscountPrice($discountPrice)
    {
        $this->discount_price = $discountPrice;

        return $this;
    }

    /**
     * Get discount_price
     *
     * @return string 
     */
    public function getDiscountPrice()
    {
        return $this->discount_price;
    }

    /**
     * Set regular_price
     *
     * @param string $regularPrice
     * @return HitProposal
     */
    public function setRegularPrice($regularPrice)
    {
        $this->regular_price = $regularPrice;

        return $this;
    }

    /**
     * Get regular_price
     *
     * @return string 
     */
    public function getRegularPrice()
    {
        return $this->regular_price;
    }

    /**
     * Set price_legend
     *
     * @param string $priceLegend
     * @return HitProposal
     */
    public function setPriceLegend($priceLegend)
    {
        $this->price_legend = $priceLegend;

        return $this;
    }

    /**
     * Get price_legend
     *
     * @return string 
     */
    public function getPriceLegend()
    {
        return $this->price_legend;
    }

    /**
     * Set bonus
     *
     * @param string $bonus
     * @return HitProposal
     */
    public function setBonus($bonus)
    {
        $this->bonus = $bonus;

        return $this;
    }

    /**
     * Get bonus
     *
     * @return string 
     */
    public function getBonus()
    {
        return $this->bonus;
    }

    /**
     * Set bonus_image_file_name
     *
     * @param string $bonusImageFileName
     * @return HitProposal
     */
    public function setBonusImageFileName($bonusImageFileName)
    {
        $this->bonus_image_file_name = $bonusImageFileName;

        return $this;
    }

    /**
     * Get bonus_image_file_name
     *
     * @return string 
     */
    public function getBonusImageFileName()
    {
        return $this->bonus_image_file_name;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param UploadedFile $file
     * @return $this
     */
    public function setImage(UploadedFile $file = null){
        $this->setFile('image_file_name', $file);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage(){
        return $this->getFile('image_file_name');
    }

    /**
     * @return null|string
     */
    public function getImageUrl(){
        return $this->getFileUrl($this->getImageFileName());
    }

    /**
     * @param UploadedFile $file
     * @return $this
     */
    public function setBonusImage(UploadedFile $file = null){
        $this->setFile('bonus_image_file_name', $file);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBonusImage(){
        return $this->getFile('bonus_image_file_name');
    }

    /**
     * @return null|string
     */
    public function getBonusImageUrl(){
        return $this->getFileUrl($this->getBonusImageFileName());
    }

    /**
     * @var string
     */
    protected $timer_legend;

    /**
     * @var \DateTime
     */
    protected $timer_end_date;


    /**
     * Set timer_legend
     *
     * @param string $timerLegend
     * @return HitProposal
     */
    public function setTimerLegend($timerLegend)
    {
        $this->timer_legend = $timerLegend;

        return $this;
    }

    /**
     * Get timer_legend
     *
     * @return string 
     */
    public function getTimerLegend()
    {
        return $this->timer_legend;
    }

    /**
     * Set timer_end_date
     *
     * @param \DateTime $timerEndDate
     * @return HitProposal
     */
    public function setTimerEndDate($timerEndDate)
    {
        $this->timer_end_date = $timerEndDate;

        return $this;
    }

    /**
     * Get timer_end_date
     *
     * @return \DateTime 
     */
    public function getTimerEndDate()
    {
        return $this->timer_end_date;
    }

    /**
     * Set main_title
     *
     * @param string $mainTitle
     * @return Settings
     */
    public function setMainTitle($mainTitle)
    {
        $this->main_title = $mainTitle;

        return $this;
    }

    /**
     * Get main_title
     *
     * @return string
     */
    public function getMainTitle()
    {
        return $this->main_title;
    }

    /**
     * Set main_description
     *
     * @param string $mainDescription
     * @return Settings
     */
    public function setMainDescription($mainDescription)
    {
        $this->main_description = $mainDescription;

        return $this;
    }

    /**
     * Get main_description
     *
     * @return string
     */
    public function getMainDescription()
    {
        return $this->main_description;
    }

    /**
     * @param UploadedFile $file
     * @return $this
     */
    public function setMainImg(UploadedFile $file = null){
        $this->setFile('main_img_file_name', $file);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMainImg(){
        return $this->getFile('main_img_file_name');
    }

    /**
     * @return null|string
     */
    public function getMainImgUrl(){
        return $this->getFileUrl($this->getMainImgFilename());
    }

    protected $short_description;

    /**
     * @param mixed $short_description
     */
    public function setShortDescription($short_description)
    {
        $this->short_description = $short_description;
    }

    /**
     * @return mixed
     */
    public function getShortDescription()
    {
        return $this->short_description;
    }


    /**
     * Set main_img_file_name
     *
     * @param string $mainImgFileName
     * @return HitProposal
     */
    public function setMainImgFileName($mainImgFileName)
    {
        $this->main_img_file_name = $mainImgFileName;

        return $this;
    }

    /**
     * Get main_img_file_name
     *
     * @return string 
     */
    public function getMainImgFileName()
    {
        return $this->main_img_file_name;
    }

}
