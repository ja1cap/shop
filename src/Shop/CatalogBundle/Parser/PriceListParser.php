<?php
namespace Shop\CatalogBundle\Parser;

use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\ContractorCurrency;
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
     * @param ObjectManager|object $em
     */
    function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function parse(PriceList $priceList){

        if(!$priceList->getManufacturer() instanceof Manufacturer){
            throw new \Exception('Price list manufacturer not defined');
        }

        $filePath = $priceList->getPriceListFilePath();
        $objPHPExcel = \PHPExcel_IOFactory::load($filePath);

        $activeSheet = $objPHPExcel->getActiveSheet();
        $rowIterator = $activeSheet->getRowIterator();

        $priceListColumnsAliases = array();
        foreach($priceList->getAliases() as $priceListAlias){
            if($priceListAlias instanceof PriceListAlias){
                $priceListColumnsAliases[$priceListAlias->getColumnName()] = $priceListAlias->getAliasName();
            }
        }

        $parameters = array();
        $proposalsParametersValues = array();

        $parameterOptionRepository = $this->em->getRepository('ShopCatalogBundle:ParameterOption');
        $parametersOptionsIndexedByName = array();

        $groupedRowsData = array();

        /**
         * @var $row \PHPExcel_Worksheet_Row
         */
        foreach($rowIterator as $row){

            $rowData = array(
                PriceListAlias::ALIAS_SKU => null,
                PriceListAlias::ALIAS_NAME => null,
                PriceListAlias::ALIAS_SHORT_DESCRIPTION => null,
                PriceListAlias::ALIAS_DESCRIPTION => null,
                PriceListAlias::ALIAS_PRICE => null,
                PriceListAlias::ALIAS_CURRENCY => null,
                PriceListAlias::ALIAS_CATEGORY => null,
                PriceListAlias::ALIAS_MANUFACTURER => null,
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

                                if(is_numeric($cellValue) && in_array($cellValue, ContractorCurrency::$currenciesNumericCodes)){

                                    $rowData[$alias] = $cellValue;

                                } elseif(is_string($cellValue) && isset(ContractorCurrency::$currenciesAlphabeticCodesNumericCodes[$cellValue])) {

                                    $rowData[$alias] = ContractorCurrency::$currenciesAlphabeticCodesNumericCodes[$cellValue];

                                }

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
                                                            str_replace('х', 'x', $cellValue), //@TODO remove
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

                $isValid = !array_diff(PriceListAlias::getRequiredAliases(), array_keys(array_filter($rowData)));
                if($isValid){

                    if(!$rowData[PriceListAlias::ALIAS_CATEGORY]){
                        $rowData[PriceListAlias::ALIAS_CATEGORY] = $priceList->getCategory()->getName();
                    }

                    $categoryName = $rowData[PriceListAlias::ALIAS_CATEGORY];
                    $proposalName = $rowData[PriceListAlias::ALIAS_NAME];

                    if($proposalParametersValues){
                        $proposalsParametersValues[$proposalName] = $proposalParametersValues;
                    }

                    $sku = $rowData[PriceListAlias::ALIAS_SKU];

                    if(!isset($groupedRowsData[$categoryName])){
                        $groupedRowsData[$categoryName] = array();
                    }

                    if(!isset($groupedRowsData[$categoryName][$proposalName])){
                        $groupedRowsData[$categoryName][$proposalName] = array();
                    }

                    $groupedRowsData[$categoryName][$proposalName][$sku] = $rowData;

                }

            }

        }

        $formattedCategoriesNames = $this->formatNames(array_keys($groupedRowsData));

        /**
         * @var $categoryRepository \Shop\CatalogBundle\Entity\CategoryRepository
         */
        $categoryRepository = $this->em->getRepository('ShopCatalogBundle:Category');
        $existingCategories = $categoryRepository->findCategoriesByName($formattedCategoriesNames);

        foreach($groupedRowsData as $categoryName => $proposals){

            $category = null;
            $formattedCategoryName = str_replace(' ', '', trim(mb_strtolower($categoryName, 'UTF-8')));

            foreach($existingCategories as $existingCategory){

                if($existingCategory instanceof Category){

                    $formattedExistingCategoryName = str_replace(' ', '', trim(mb_strtolower($existingCategory->getName(), 'UTF-8')));
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
                    ->setStatus(Category::STATUS_OFF)
                ;

                $this->em->persist($category);

            }

            $proposalsNames = array_keys($proposals);
            $formattedProposalsNames = $this->formatNames($proposalsNames);

            /**
             * @var $proposalRepository \Shop\CatalogBundle\Entity\ProposalRepository
             */
            $proposalRepository = $this->em->getRepository('ShopCatalogBundle:Proposal');
            $existingCategoryProposals = $proposalRepository->findProposalsByName($formattedProposalsNames);

            foreach($proposals as $proposalName => $pricesData){

                $proposal = null;
                $formattedProposalName = str_replace(' ', '', trim(mb_strtolower($proposalName, 'UTF-8')));

                foreach($existingCategoryProposals as $existingProposal){

                    if($existingProposal instanceof Proposal){

                        $formattedExistingProposalName = str_replace(' ', '', trim(mb_strtolower($existingProposal->getTitle(), 'UTF-8')));
                        if($formattedExistingProposalName == $formattedProposalName){

                            $proposal = $existingProposal;
                            break;

                        }

                    }

                }

                if(!$proposal instanceof Proposal){

                    $proposal = new Proposal();
                    $proposal
                        ->setTitle($proposalName)
                        ->setStatus(Proposal::STATUS_ON)
                        ->setManufacturer($priceList->getManufacturer())
                        ->setDefaultContractor($priceList->getContractor())
                    ;

                    $category->addProposal($proposal);
                    $this->em->persist($proposal);

                }

                if(isset($proposalsParametersValues[$proposalName]) && is_array($proposalsParametersValues[$proposalName])){

                    $proposalParametersValues = $proposalsParametersValues[$proposalName];

                    $proposalParameterValuesMapper = new ProposalParameterValuesMapper($this->em->getManager(), $proposal);
                    $proposalParameterValuesMapper->mapParameterValues($proposalParametersValues);

                }

                if($proposal->getId()){

                    $pricesSku = array_keys($pricesData);

                    $priceRepository = $this->em->getRepository('ShopCatalogBundle:Price');
                    $prices = $priceRepository->findBy(array(
                        'proposalId' => $proposal->getId(),
                        'sku' => array_unique($pricesSku),
                    ));

                } else {

                    $prices = array();

                }

                /**
                 * @var $price Price
                 */
                foreach($prices as $price){

                    if(isset($pricesData[$price->getSku()])){

                        $priceData = $pricesData[$price->getSku()];

                        $price
                            //->setStatus(Price::STATUS_ON)
                            ->setValue($priceData[PriceListAlias::ALIAS_PRICE])
                            ->setCurrencyNumericCode($priceData[PriceListAlias::ALIAS_CURRENCY])
                            ->setContractor($priceList->getContractor())
                        ;

                        $parameterValuesData = $priceData['parametersValues'];
                        if($parameterValuesData){
                            $priceParameterValuesMapper = new PriceParameterValuesMapper($this->em->getManager(), $price);
                            $priceParameterValuesMapper->mapParameterValues($parameterValuesData);
                        }

                        unset($pricesData[$price->getSku()]);

                    }

                }

                foreach($pricesData as $priceData){

                    $price = new Price();
                    $price
                        ->setStatus(Price::STATUS_ON)
                        ->setSku($priceData[PriceListAlias::ALIAS_SKU])
                        ->setValue($priceData[PriceListAlias::ALIAS_PRICE])
                        ->setCurrencyNumericCode($priceData[PriceListAlias::ALIAS_CURRENCY])
                        ->setContractor($priceList->getContractor())
                    ;

                    $proposal->addPrice($price);

                    $parameterValuesData = $priceData['parametersValues'];
                    if($parameterValuesData){
                        $priceParameterValuesMapper = new PriceParameterValuesMapper($this->em->getManager(), $price);
                        $priceParameterValuesMapper->mapParameterValues($parameterValuesData, false);
                    }

                    $this->em->persist($price);

                }

            }

        }

        return $priceList;

    }

    /**
     * @param $names
     * @return array
     */
    protected function formatNames($names){
        return array_unique(
            array_filter(
                array_map(
                    function($name){
                        return str_replace(' ', '', trim(mb_strtolower($name, 'UTF-8')));
                    },
                    $names
                )
            )
        );
    }

} 