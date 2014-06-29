<?php

namespace Shop\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Weasty\Doctrine\Entity\AbstractEntity;

/**
 * Review
 */
class Review extends AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $customerName;

    /**
     * @var string
     */
    private $customerImageFileName;

    /**
     * @var string
     */
    private $companyName;

    /**
     * @var string
     */
    private $companyImageFileName;

    /**
     * @var string
     */
    private $reviewFileName;

    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $videoCode;


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
     * Set customerName
     *
     * @param string $customerName
     * @return Review
     */
    public function setCustomerName($customerName)
    {
        $this->customerName = $customerName;

        return $this;
    }

    /**
     * Get customerName
     *
     * @return string 
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }

    /**
     * Set customerImageFileName
     *
     * @param string $customerImageFileName
     * @return Review
     */
    public function setCustomerImageFileName($customerImageFileName)
    {
        $this->customerImageFileName = $customerImageFileName;

        return $this;
    }

    /**
     * Get customerImageFileName
     *
     * @return string 
     */
    public function getCustomerImageFileName()
    {
        return $this->customerImageFileName;
    }

    /**
     * Set companyName
     *
     * @param string $companyName
     * @return Review
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get companyName
     *
     * @return string 
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set companyImageFileName
     *
     * @param string $companyImageFileName
     * @return Review
     */
    public function setCompanyImageFileName($companyImageFileName)
    {
        $this->companyImageFileName = $companyImageFileName;

        return $this;
    }

    /**
     * Get companyImageFileName
     *
     * @return string 
     */
    public function getCompanyImageFileName()
    {
        return $this->companyImageFileName;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     * @return Review
     */
    public function setReviewFileName($fileName)
    {
        $this->reviewFileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string 
     */
    public function getReviewFileName()
    {
        return $this->reviewFileName;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return Review
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set videoCode
     *
     * @param string $videoCode
     * @return Review
     */
    public function setVideoCode($videoCode)
    {
        $this->videoCode = $videoCode;

        return $this;
    }

    /**
     * Get videoCode
     *
     * @return string 
     */
    public function getVideoCode()
    {
        return $this->videoCode;
    }

    public function getCustomerImage(){
        return $this->getFile('customer_image_file_name');
    }

    public function setCustomerImage($file = null){
        $this->setFile('customer_image_file_name', $file);
    }

    public function getCustomerImageUrl(){
        return $this->getFileUrl($this->getCustomerImageFileName());
    }

    public function getCompanyImage(){
        return $this->getFile('company_image_file_name');
    }

    public function setCompanyImage($file = null){
        $this->setFile('company_image_file_name', $file);
    }

    public function getCompanyImageUrl(){
        return $this->getFileUrl($this->getCompanyImageFileName());
    }

    public function getReviewFile(){
        return $this->getFile('review_file_name');
    }

    public function setReviewFile($file = null){
        $this->setFile('review_file_name', $file);
    }

    public function getReviewFileUrl(){
        return $this->getFileUrl($this->getReviewFileName());
    }

    /**
     * @return bool
     */
    public function getIsVideoReview(){
        return (bool)$this->getVideoCode();
    }

}
