<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Shop\MainBundle\Entity\AbstractEntity;

/**
 * PriceList
 */
class PriceList extends AbstractEntity
{

    const STATUS_UPLOADED = 1;
    const STATUS_PARSED = 2;
    const STATUS_INVALID_FILE = -1;
    const STATUS_PARSE_ERROR = -2;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var \DateTime
     */
    private $createDate;

    /**
     * @var \DateTime
     */
    private $updateDate;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var array
     */
    public static $statuses = array(
        self::STATUS_UPLOADED => 'Загружен',
        self::STATUS_PARSED => 'Обработан',
        self::STATUS_INVALID_FILE => 'Файл не подходит',
        self::STATUS_PARSE_ERROR => 'Ошибка обработки',
    );

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
     * Set name
     *
     * @param string $name
     * @return PriceList
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
     * Set fileName
     *
     * @param string $fileName
     * @return PriceList
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string 
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return PriceList
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     * @return PriceList
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return PriceList
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getTextStatus(){
        return self::$statuses[$this->getStatus()];
    }

    public function getPriceListFile(){
        return $this->getFile('fileName');
    }

    public function setPriceListFile($file = null){
        return $this->setFile('fileName', $file);
    }

    public function getPriceListFilePath(){
        return $this->getFilePath($this->getFileName());
    }

    public function getPriceListFileUrl(){
        return $this->getFileUrl($this->getFileName());
    }

    /**
     * @var integer
     */
    private $contractorId;

    /**
     * @var \Shop\CatalogBundle\Entity\Contractor
     */
    private $contractor;


    /**
     * Set contractorId
     *
     * @param integer $contractorId
     * @return PriceList
     */
    public function setContractorId($contractorId)
    {
        $this->contractorId = $contractorId;

        return $this;
    }

    /**
     * Get contractorId
     *
     * @return integer 
     */
    public function getContractorId()
    {
        return $this->contractorId;
    }

    /**
     * Set contractor
     *
     * @param \Shop\CatalogBundle\Entity\Contractor $contractor
     * @return PriceList
     */
    public function setContractor(Contractor $contractor = null)
    {
        $this->contractor = $contractor;
        $this->contractorId = $contractor ? $contractor->getId() : null;
        return $this;
    }

    /**
     * Get contractor
     *
     * @return \Shop\CatalogBundle\Entity\Contractor 
     */
    public function getContractor()
    {
        return $this->contractor;
    }
}
