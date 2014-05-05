<?php
namespace Shop\CatalogBundle\Parser;

use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\Contractor;
use Shop\CatalogBundle\Entity\Manufacturer;
use Shop\CatalogBundle\Entity\Parameter;
use Shop\CatalogBundle\Entity\ParameterOption;
use Shop\CatalogBundle\Entity\Price;
use Shop\CatalogBundle\Entity\PriceList;
use Shop\CatalogBundle\Entity\PriceListAlias;
use Doctrine\Common\Persistence\ObjectManager;
use Shop\CatalogBundle\Entity\Proposal;
use Shop\CatalogBundle\Mapper\PriceParameterValuesMapper;
use Shop\CatalogBundle\Mapper\ProposalParameterValuesMapper;
use Weasty\MoneyBundle\Data\CurrencyResource;

/**
 * Class PriceListParser
 * @package Shop\CatalogBundle\Parser
 */
class PriceListParser {

    /**
     * @var ObjectManager|object
     */
    protected $em;

    /**
     * @var \Weasty\MoneyBundle\Converter\CurrencyCodeConverter
     */
    protected $currencyCodeConverter;

    function __construct($em, $currencyCodeConverter)
    {
        $this->em = $em;
        $this->currencyCodeConverter = $currencyCodeConverter;
    }

    public function parse(PriceList $priceList){

        $filePath = $priceList->getPriceListFilePath();
        $objPHPExcel = \PHPExcel_IOFactory::load($filePath);

        $activeSheet = $objPHPExcel->getActiveSheet();
        $rowIterator = $activeSheet->getRowIterator();

        $aliasesEntitiesMap = PriceListAlias::getEntitiesAliasesMap();

        $priceListColumnsAliases = array();
        foreach($priceList->getAliases() as $priceListAlias){
            if($priceListAlias instanceof PriceListAlias){
                $priceListColumnsAliases[$priceListAlias->getColumnName()] = $priceListAlias->getAliasName();
            }
        }

        $parameters = array();

        $proposalsData = array();

        $parameterOptionRepository = $this->em->getRepository('ShopCatalogBundle:ParameterOption');
        $parametersOptionsIndexedByName = array();

        $groupedRowsData = array();

        /**
         * @var $row \PHPExcel_Worksheet_Row
         */
        foreach($rowIterator as $row){

            $rowData = array(
                'parametersValues' => array(),
            );

            if($row->getRowIndex() > $priceList->getIdentifiersRowIndex()){

                $proposalParametersValues = array();

                $cellIterator = $row->getCellIterator();

                /**
                 * @var $cell \PHPExcel_Cell
                 */
                foreach($cellIterator as $cell){

                    $alias = null;
                    if(isset($priceListColumnsAliases[$cell->getColumn()])){
                        $alias = $priceListColumnsAliases[$cell->getColumn()];
                    }

                    if($alias){

                        $cellValue = trim($cell->getValue());

                        switch($alias){
                            case PriceListAlias::ALIAS_CURRENCY:

                                $rowData[$alias] = $this->getCurrencyCodeConverter()->convert($cellValue, CurrencyResource::CODE_TYPE_ISO_4217_NUMERIC);
                                break;

                            default:

                                if(in_array($alias, array_keys(PriceListAlias::getAliasesCommonTitles()))){

                                    $rowData[$alias] = $cellValue;

                                } elseif(strpos($alias, PriceListAlias::ALIAS_PARAMETER_PREFIX) === 0){

                                    $parameterId = (int)str_replace(PriceListAlias::ALIAS_PARAMETER_PREFIX, '', $alias);
                                    if($parameterId){

                                        if(isset($parameters[$parameterId])){

                                            $parameter = $parameters[$parameterId];

                                        } else {

                                            $parameter = $this->em->getRepository('ShopCatalogBundle:Parameter')->findOneBy(array(
                                                'id' => $parameterId,
                                            ));


                                            if($parameter instanceof Parameter){
                                                $parameters[$parameterId] = $parameter;
                                            }

                                        }

                                        if($parameter){

                                            $parameterOption = null;

                                            if($cellValue){

                                                if(isset($parametersOptionsIndexedByName[$parameterId][$cellValue])){

                                                    $parameterOption = $parametersOptionsIndexedByName[$parameterId][$cellValue];

                                                } else {

                                                    $parameterOption = $parameterOptionRepository->findOneBy(array(
                                                        'parameterId' => $parameter->getId(),
                                                        'name' => array(
                                                            $cellValue,
                                                            str_replace('х', 'x', $cellValue), //Replace russian delimiter with english char
                                                        ),
                                                    ));

                                                    if(!$parameterOption instanceof ParameterOption){

                                                        $parameterOption = new ParameterOption();
                                                        $parameterOption->setName($cellValue);
                                                        $parameter->addOption($parameterOption);

                                                        $this->em->persist($parameterOption);

                                                    }

                                                    if(!isset($parametersOptionsIndexedByName[$parameterId])){
                                                        $parametersOptionsIndexedByName[$parameterId] = array();
                                                    }

                                                    $parametersOptionsIndexedByName[$parameterId][$cellValue] = $parameterOption;

                                                }

                                            }

                                            if($parameter->getIsPriceParameter()){

                                                $rowData['parametersValues'][$parameterId] = $parameterOption;

                                            } else {

                                                $proposalParametersValues[$parameterId] = $parameterOption;

                                            }

                                        }


                                    }

                                }

                        }

                    }

                }

                $isValid = $this->validateRow($rowData);
                if($isValid){

                    if(!$rowData[PriceListAlias::ALIAS_CATEGORY]){
                        $rowData[PriceListAlias::ALIAS_CATEGORY] = $priceList->getCategory() ? $priceList->getCategory()->getName() : null;
                    }

                    if(!$rowData[PriceListAlias::ALIAS_CONTRACTOR]){
                        $rowData[PriceListAlias::ALIAS_CONTRACTOR] = $priceList->getContractor() ? $priceList->getContractor()->getName() : null;
                    }

                    if(!$rowData[PriceListAlias::ALIAS_MANUFACTURER]){
                        $rowData[PriceListAlias::ALIAS_MANUFACTURER] = $priceList->getManufacturer() ? $priceList->getManufacturer()->getName() : null;
                    }

                    $proposalName = $rowData[PriceListAlias::ALIAS_NAME];

                    if(!isset($proposalsData[$proposalName])){

                        $proposalData = array();

                        foreach($aliasesEntitiesMap as $alias => $aliasEntityMap){
                            if(isset($rowData[$alias])){

                                switch($aliasEntityMap['entity']){
                                    case 'proposal':

                                        $proposalData[$alias] = $rowData[$alias];
                                        break;

                                    case 'price':

                                        switch($aliasEntityMap['property']){

                                            case 'contractorName';
                                                $proposalData[$alias] = $rowData[$alias];
                                                break;

                                        }

                                        break;
                                }

                            }
                        }

                        $proposalData['parametersValues'] = $proposalParametersValues;

                        $proposalsData[$proposalName] = $proposalData;

                    } else {

                        $proposalData = $proposalsData[$proposalName];

                    }

                    $categoryName = $proposalData[PriceListAlias::ALIAS_CATEGORY];
                    $contractorName = $proposalData[PriceListAlias::ALIAS_CONTRACTOR];
                    $manufacturerName = $proposalData[PriceListAlias::ALIAS_MANUFACTURER];

                    if($categoryName && $contractorName && $manufacturerName){

                        if(!isset($groupedRowsData[$categoryName])){
                            $groupedRowsData[$categoryName] = array();
                        }

                        if(!isset($groupedRowsData[$categoryName][$proposalName])){
                            $groupedRowsData[$categoryName][$proposalName] = array();
                        }

                        $groupedRowsData[$categoryName][$proposalName][] = $rowData;

                    }

                }

            }

        }

        $formattedCategoryNames = $this->formatNames(array_keys($groupedRowsData));

        /**
         * @var $categoryRepository \Shop\CatalogBundle\Entity\CategoryRepository
         */
        $categoryRepository = $this->em->getRepository('ShopCatalogBundle:Category');
        $existingCategories = $categoryRepository->findCategoriesByName($formattedCategoryNames);

        $proposalRepository = $this->getProposalRepository();
        $priceRepository =$this->getPriceRepository();

        foreach($groupedRowsData as $categoryName => $proposals){

            $category = null;
            $formattedCategoryName = $this->formatName($categoryName);

            foreach($existingCategories as $existingCategory){

                if($existingCategory instanceof Category){

                    $formattedExistingCategoryName = $this->formatName($existingCategory->getName());
                    if($formattedExistingCategoryName == $formattedCategoryName){

                        $category = $existingCategory;
                        break;

                    }

                }

            }

            if(!$category instanceof Category){

                $category = new Category();
                $category
                    ->setName($categoryName)
                    ->setStatus(Category::STATUS_ON)
                ;

                $this->em->persist($category);

            }

            $proposalsNames = array_keys($proposals);
            $formattedProposalsNames = $this->formatNames($proposalsNames);

            $existingCategoryProposals = $proposalRepository->findProposalsByName($formattedProposalsNames);

            foreach($proposals as $proposalName => $pricesData){

                if(!isset($proposalsData[$proposalName])){
                    continue;
                }

                $proposalData = $proposalsData[$proposalName];

                $proposal = null;
                $formattedProposalName = $this->formatName($proposalName);

                foreach($existingCategoryProposals as $existingProposal){

                    if($existingProposal instanceof Proposal){

                        $formattedExistingProposalName = $this->formatName($existingProposal->getTitle());
                        if($formattedExistingProposalName == $formattedProposalName){

                            $proposal = $existingProposal;
                            break;

                        }

                    }

                }

                $proposal = $this->mapProposalData($priceList, $category, $proposal, $proposalData);

                $skuPriceDataKeys = array();
                $manufacturerSkuPriceDataKeys = array();

                foreach($pricesData as $priceDataKey => $priceData){

                    if(isset($priceData[PriceListAlias::ALIAS_SKU]) && $priceData[PriceListAlias::ALIAS_SKU]){

                        $sku = $priceData[PriceListAlias::ALIAS_SKU];
                        $skuPriceDataKeys[$sku] = $priceDataKey;

                    }

                    if(isset($priceData[PriceListAlias::ALIAS_MANUFACTURER_SKU]) && $priceData[PriceListAlias::ALIAS_MANUFACTURER_SKU]){

                        $manufacturerSku = $priceData[PriceListAlias::ALIAS_MANUFACTURER_SKU];
                        $manufacturerSkuPriceDataKeys[$manufacturerSku] = $priceDataKey;

                    }

                }

                if($proposal && $proposal->getId()){

                    $pricesSku = array_keys($skuPriceDataKeys);
                    $pricesManufacturerSku = array_keys($manufacturerSkuPriceDataKeys);

                    $prices = $priceRepository->findProposalPricesBySku(
                        $proposal->getId(),
                        $pricesSku,
                        $pricesManufacturerSku
                    );

                } else {

                    $prices = array();

                }

                /**
                 * @var $price Price
                 */
                foreach($prices as $price){

                    $priceData = null;
                    $skuPriceDataKey = ($price->getSku() && isset($skuPriceDataKeys[$price->getSku()])) ? $skuPriceDataKeys[$price->getSku()] : null;
                    $manufacturerSkuPriceDataKey = ($price->getManufacturerSku() && isset($manufacturerSkuPriceDataKeys[$price->getManufacturerSku()])) ? $manufacturerSkuPriceDataKeys[$price->getManufacturerSku()] : null;

                    if($skuPriceDataKey !== null && isset($pricesData[$skuPriceDataKey])){

                        $priceData = $pricesData[$skuPriceDataKey];
                        unset($pricesData[$skuPriceDataKey]);

                    } elseif($manufacturerSkuPriceDataKey !== null && isset($priceList[$manufacturerSkuPriceDataKey])){

                        $priceData = $pricesData[$manufacturerSkuPriceDataKey];

                    }

                    if($priceData){
                        $this->mapPriceData($priceList, $price, $priceData);
                    }

                }

                foreach($pricesData as $priceData){

                    $price = $this->mapPriceData($priceList, null, $priceData);
                    if($price){

                        $proposal->addPrice($price);
                        $this->getEm()->persist($price);

                    }

                }

            }

        }

        return $priceList;

    }

    /**
     * @param $rowData
     * @return bool
     */
    protected function validateRow($rowData)
    {
        $rowAliases = array_keys(array_filter($rowData));
        $isValid = (
            in_array(PriceListAlias::ALIAS_PRICE, $rowAliases)
            && in_array(PriceListAlias::ALIAS_CURRENCY, $rowAliases)
            && (
                in_array(PriceListAlias::ALIAS_SKU, $rowAliases)
                ||
                in_array(PriceListAlias::ALIAS_MANUFACTURER_SKU, $rowAliases)
            )
        );
        return $isValid;
    }

    /**
     * @param PriceList $priceList
     * @param Category $category
     * @param $proposal
     * @param $proposalData
     * @return Proposal
     */
    protected function mapProposalData(PriceList $priceList, Category $category, $proposal, $proposalData){

        if(!$proposal instanceof Proposal){

            $proposal = new Proposal();
            $proposal
                ->setStatus(Proposal::STATUS_ON)
                ->setManufacturer($priceList->getManufacturer())
                ->setDefaultContractor($priceList->getContractor())
            ;

            $category->addProposal($proposal);
            $this->em->persist($proposal);

        }

        if(is_array($proposalData)){

            foreach($proposalData as $alias => $value){

                switch($alias){
                    case PriceListAlias::ALIAS_CATEGORY:

                        $proposal->setCategory($category);
                        break;

                    case PriceListAlias::ALIAS_MANUFACTURER:

                        if($value){

                            $manufacturerRepository = $this->getManufacturerRepository();
                            $manufacturer = $manufacturerRepository->findOneBy(array(
                                'name' => (string)$value,
                            ));

                            if($manufacturer instanceof Manufacturer){

                                $proposal->setManufacturer($manufacturer);

                            }

                        }
                        break;

                    case PriceListAlias::ALIAS_CONTRACTOR:

                        if($value){

                            $contractorRepository = $this->getContractorRepository();
                            $contractor = $contractorRepository->findOneBy(array(
                                'name' => (string)$value,
                            ));

                            if($contractor instanceof Contractor){

                                $proposal->setDefaultContractor($contractor);

                            }

                        }
                        break;


                        break;
                    case 'parametersValues':

                        $proposalParametersValues = $value;

                        $proposalParameterValuesMapper = new ProposalParameterValuesMapper($this->em, $proposal);
                        $proposalParameterValuesMapper->mapParameterValues($proposalParametersValues, false);

                        break;

                    default:

                        $aliasEntityMap = PriceListAlias::getAliasEntityMap($alias);
                        if(isset($aliasEntityMap['entity']) && $aliasEntityMap['entity'] == 'proposal' && isset($aliasEntityMap['property'])){

                            $proposal[$aliasEntityMap['property']] = $value;

                        }

                }

            }

        }

        return $proposal;

    }

    /**
     * @param PriceList $priceList
     * @param $price
     * @param $priceData
     * @return mixed
     */
    protected function mapPriceData(PriceList $priceList, $price, $priceData){

        if(!$price instanceof Price){

            if(!$priceData[PriceListAlias::ALIAS_SKU]){
                return null;
            }

            $price = new Price();
            $price
                ->setStatus(Price::STATUS_ON)
                ->setSku($priceData[PriceListAlias::ALIAS_SKU])
                ->setManufacturerSku($priceData[PriceListAlias::ALIAS_MANUFACTURER_SKU])
            ;

        }

        if(is_array($priceData)){

            foreach($priceData as $alias => $value){

                switch($alias){
                    case PriceListAlias::ALIAS_CONTRACTOR:

                        if($value){

                            $contractorRepository = $this->getContractorRepository();
                            $contractor = $contractorRepository->findOneBy(array(
                                'name' => (string)$value,
                            ));

                            if($contractor instanceof Contractor){
                                $price->setContractor($contractor);
                            }

                        }
                        break;


                        break;
                    case 'parametersValues':

                        if($value){

                            $parameterValuesData = $value;

                            $priceParameterValuesMapper = new PriceParameterValuesMapper($this->getEm(), $price);
                            $priceParameterValuesMapper->mapParameterValues($parameterValuesData, false);

                        }

                        break;

                    default:

                        $aliasEntityMap = PriceListAlias::getAliasEntityMap($alias);
                        if(isset($aliasEntityMap['entity']) && $aliasEntityMap['entity'] == 'price' && isset($aliasEntityMap['property'])){

                            $price[$aliasEntityMap['property']] = $value;

                        }

                }

            }

            if(!$price->getContractor()){
                $price->setContractor($priceList->getContractor());
            }

        }

        return $price;

    }

    /**
     * @param $names
     * @return array
     */
    protected function formatNames($names){
        return array_unique(
            array_filter(
                array_map(
                    array($this, 'formatName'),
                    $names
                )
            )
        );
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function formatName($name){
        return str_replace(' ', '', trim(mb_strtolower($name, 'UTF-8')));
    }

    /**
     * @return \Weasty\MoneyBundle\Converter\CurrencyCodeConverter
     */
    public function getCurrencyCodeConverter()
    {
        return $this->currencyCodeConverter;
    }

    /**
     * @return ObjectManager|object
     */
    protected function getEm()
    {
        return $this->em;
    }

    /**
     * @return \Shop\CatalogBundle\Entity\PriceRepository
     */
    protected function getPriceRepository(){
        return $this->getEm()->getRepository('ShopCatalogBundle:Price');
    }

    /**
     * @return \Shop\CatalogBundle\Entity\ProposalRepository
     */
    public function getProposalRepository(){
        return $this->getEm()->getRepository('ShopCatalogBundle:Proposal');
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getManufacturerRepository()
    {
        $manufacturerRepository = $this->getEm()->getRepository('ShopCatalogBundle:Manufacturer');
        return $manufacturerRepository;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getContractorRepository()
    {
        $contractorRepository = $this->getEm()->getRepository('ShopCatalogBundle:Contractor');
        return $contractorRepository;
    }

} 