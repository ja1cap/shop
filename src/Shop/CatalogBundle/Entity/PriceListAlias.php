<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Weasty\Doctrine\Entity\AbstractEntity;

/**
 * Class PriceListAlias
 * @package Shop\CatalogBundle\Entity
 */
class PriceListAlias extends AbstractEntity
{

    const ALIAS_SKU = 'sku';
    const ALIAS_MANUFACTURER_SKU = 'manufacturer_sku';
    const ALIAS_NAME = 'name';
    const ALIAS_DESCRIPTION = 'description';
    const ALIAS_SHORT_DESCRIPTION = 'short_description';
    const ALIAS_PRICE = 'price';
    const ALIAS_CURRENCY = 'currency';
    const ALIAS_MANUFACTURER = 'manufacturer';
    const ALIAS_CATEGORY = 'category';
    const ALIAS_CONTRACTOR = 'contractor';
    const ALIAS_PARAMETER_PREFIX = 'parameter_';

    /**
     * @var array
     */
    public static $aliasesTitles = array(
        self::ALIAS_SKU => 'Артикул',
        self::ALIAS_MANUFACTURER_SKU => 'Артикул производитель',
        self::ALIAS_NAME => 'Наименование',
//        self::ALIAS_SHORT_DESCRIPTION => 'Краткое описание',
//        self::ALIAS_DESCRIPTION => 'Полное описание',
        self::ALIAS_PRICE => 'Цена',
        self::ALIAS_CURRENCY => 'Валюта',
        self::ALIAS_CATEGORY => 'Категория',
        self::ALIAS_MANUFACTURER => 'Производитель',
        self::ALIAS_CONTRACTOR => 'Контрагент',
    );

    /**
     * @var array
     */
    public static $entitiesAliasesMap = array(
        PriceListAlias::ALIAS_SKU => array(
            'entity' => 'price',
            'property' => 'sku',
        ),
        PriceListAlias::ALIAS_MANUFACTURER_SKU => array(
            'entity' => 'price',
            'property' => 'manufacturer_sku',
        ),
        PriceListAlias::ALIAS_CATEGORY => array(
            'entity' => 'proposal',
            'property' => 'categoryName',
        ),
        PriceListAlias::ALIAS_NAME => array(
            'entity' => 'proposal',
            'property' => 'title',
        ),
        PriceListAlias::ALIAS_MANUFACTURER => array(
            'entity' => 'proposal',
            'property' => 'manufacturerName',
        ),
        PriceListAlias::ALIAS_CONTRACTOR => array(
            'entity' => 'price',
            'property' => 'contractorName',
        ),
        PriceListAlias::ALIAS_PRICE => array(
            'entity' => 'price',
            'property' => 'value',
        ),
        //@TODO get currency alphabetic code
        PriceListAlias::ALIAS_CURRENCY => array(
            'entity' => 'price',
            'property' => 'currency',
        ),
    );

    /**
     * @var array
     */
    public static $aliasesCommonTitles = array(
        self::ALIAS_SKU => array(
            'артикул',
        ),
        self::ALIAS_MANUFACTURER_SKU => array(
            'артикул производителя',
        ),
        self::ALIAS_NAME => array(
            'название',
            'наименование',
            'наименование изделия',
        ),
        self::ALIAS_SHORT_DESCRIPTION => array(
            'краткое описание',
            'короткое описание',
        ),
        self::ALIAS_DESCRIPTION => array(
            'описание',
            'полное описание',
        ),
        self::ALIAS_PRICE => array(
            'цена',
            'стоимость',
        ),
        self::ALIAS_CURRENCY => array(
            'валюта',
        ),
        self::ALIAS_CATEGORY => array(
            'категория',
        ),
        self::ALIAS_MANUFACTURER => array(
            'производитель',
        ),
        self::ALIAS_CONTRACTOR => array(
            'контрагент',
            'поставщик',
        ),
    );

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $priceListId;

    /**
     * @var string
     */
    private $columnName;

    /**
     * @var string
     */
    private $aliasName;

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
     * Set priceListId
     *
     * @param integer $priceListId
     * @return PriceListAlias
     */
    public function setPriceListId($priceListId)
    {
        $this->priceListId = $priceListId;

        return $this;
    }

    /**
     * Get priceListId
     *
     * @return integer 
     */
    public function getPriceListId()
    {
        return $this->priceListId;
    }

    /**
     * Set columnName
     *
     * @param string $columnName
     * @return PriceListAlias
     */
    public function setColumnName($columnName)
    {
        $this->columnName = $columnName;

        return $this;
    }

    /**
     * Get columnName
     *
     * @return string 
     */
    public function getColumnName()
    {
        return $this->columnName;
    }

    /**
     * Set aliasName
     *
     * @param string $aliasName
     * @return PriceListAlias
     */
    public function setAliasName($aliasName)
    {
        $this->aliasName = $aliasName;

        return $this;
    }

    /**
     * Get aliasName
     *
     * @return string 
     */
    public function getAliasName()
    {
        return $this->aliasName;
    }
    /**
     * @var \Shop\CatalogBundle\Entity\PriceList
     */
    private $priceList;


    /**
     * Set priceList
     *
     * @param \Shop\CatalogBundle\Entity\PriceList $priceList
     * @return PriceListAlias
     */
    public function setPriceList(PriceList $priceList = null)
    {
        $this->priceList = $priceList;
        $this->priceListId = $priceList->getId();
        return $this;
    }

    /**
     * Get priceList
     *
     * @return \Shop\CatalogBundle\Entity\PriceList 
     */
    public function getPriceList()
    {
        return $this->priceList;
    }

    /**
     * @param $alias
     * @return array
     */
    public static function getAliasCommonTitles($alias)
    {
        return isset(self::$aliasesCommonTitles[$alias]) ? self::$aliasesCommonTitles[$alias] : array();
    }

    /**
     * @return array
     */
    public static function getAliasesCommonTitles()
    {
        return self::$aliasesCommonTitles;
    }

    /**
     * @param $alias
     * @return array
     */
    public static function getAliasesTitle($alias)
    {
        if(isset(self::$aliasesTitles[$alias])){
            return self::$aliasesTitles[$alias];
        }
        return null;
    }

    /**
     * @return array
     */
    public static function getAliasesTitles()
    {
        return self::$aliasesTitles;
    }

    /**
     * @return array
     */
    public static function getEntitiesAliasesMap()
    {
        return self::$entitiesAliasesMap;
    }

    /**
     * @param $alias
     * @return null|array
     */
    public static function getAliasEntityMap($alias){
        if(isset(self::$entitiesAliasesMap[$alias])){
            return self::$entitiesAliasesMap[$alias];
        }
        return null;
    }

}
