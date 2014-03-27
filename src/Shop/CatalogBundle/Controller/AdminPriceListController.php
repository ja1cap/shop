<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\ContractorCurrency;
use Shop\CatalogBundle\Entity\Manufacturer;
use Shop\CatalogBundle\Entity\Parameter;
use Shop\CatalogBundle\Entity\ParameterOption;
use Shop\CatalogBundle\Entity\PriceList;
use Shop\CatalogBundle\Entity\PriceListAlias;
use Shop\CatalogBundle\Entity\Proposal;
use Shop\CatalogBundle\Form\Type\PriceListType;
use Shop\CatalogBundle\Mapper\PriceParameterValuesMapper;
use Shop\CatalogBundle\Mapper\ProposalParameterValuesMapper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Shop\CatalogBundle\Entity\PriceListRepository;
use Shop\CatalogBundle\Entity\Price;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminPriceListController
 * @package Shop\CatalogBundle\Controller
 */
class AdminPriceListController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function priceListsAction()
    {
        return $this->render('ShopCatalogBundle:AdminPriceList:priceLists.html.twig', array(
            'priceLists' => $this->getPriceListRepository()->findAll(),
        ));
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function priceListAction($id, Request $request)
    {

        $entity = $this->getPriceListRepository()->findOneBy(array(
            'id' => $id,
        ));

        if(!$entity instanceof PriceList){
            $entity = new PriceList();
        }

        $isNew = !$entity->getId();
        $form = $this->createForm(new PriceListType(), $entity);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $entity->setStatus(PriceList::STATUS_UPLOADED);
                $entity->setCreateDate(new \DateTime());
                $em->persist($entity);
            }

            $entity->setUpdateDate(new \DateTime());

            $em->flush();

            return $this->redirect($this->generateUrl('price_lists'));

        } else {

            return $this->render('ShopCatalogBundle:AdminPriceList:priceList.html.twig', array(
                'title' => $isNew ? 'Добавление прайс-листа' : 'Изменение прайс-листа',
                'form' => $form->createView(),
                'priceList' => $entity,
            ));

        }

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Exception
     */
    public function priceListsAliasesAction($id, Request $request){

        $priceList = $this->getPriceListRepository()->findOneBy(array(
            'id' => (int)$id,
        ));

        if(!$priceList instanceof PriceList){
            throw $this->createNotFoundException('Прайс-лист не найден');
        }

        if($request->getMethod() == 'POST'){

            $em = $this->getDoctrine()->getManager();

            foreach($priceList->getAliases() as $priceListAlias){
                $em->remove($priceListAlias);
            }

            $priceList->resetAliases();

            $categoryId = $request->get('categoryId');
            $category = $this->getDoctrine()->getRepository('ShopCatalogBundle:Category')->findOneBy(array(
                'id' => $categoryId,
            ));

            if(!$category instanceof Category){
                throw $this->createNotFoundException('Category not found');
            }

            $priceList->setCategory($category);

            $manufacturerId = $request->get('manufacturerId');
            $manufacturer = $this->getDoctrine()->getRepository('ShopCatalogBundle:Manufacturer')->findOneBy(array(
                'id' => $manufacturerId,
            ));

            if(!$manufacturer instanceof Manufacturer){
                throw $this->createNotFoundException('Manufacturer not found');
            }

            $priceList->setManufacturer($manufacturer);

            $identifiersAliases = $request->get('identifiers');

            if(is_array($identifiersAliases)){

                foreach($identifiersAliases as $columnName => $aliasName){

                    if(
                        $aliasName
                        &&
                        (
                            in_array($aliasName, array_keys(PriceListAlias::$aliasesCommonTitles))
                            ||
                            strpos($aliasName, PriceListAlias::ALIAS_PARAMETER_PREFIX) !== false
                        )
                    ){

                        $priceListAlias = new PriceListAlias();
                        $priceListAlias
                            ->setColumnName($columnName)
                            ->setAliasName($aliasName)
                        ;

                        $priceList->addAlias($priceListAlias);

                    }

                }

            }

            $em->flush();

            return $this->redirect($this->generateUrl('price_lists'));

        } else {

            $filePath = $priceList->getPriceListFilePath();
            $objPHPExcel = \PHPExcel_IOFactory::load($filePath);

            $activeSheet = $objPHPExcel->getActiveSheet();
            $rowIterator = $activeSheet->getRowIterator();

            $rowIterator->seek($priceList->getIdentifiersRowIndex());
            $identifiersRow = $rowIterator->current();
            $rowIterator->resetStart();

            if($identifiersRow){

                $identifiers = array();

                /**
                 * @var $identifiersRow \PHPExcel_Worksheet_Row
                 */
                $cellIterator = $identifiersRow->getCellIterator();

                /**
                 * @var $cell \PHPExcel_Cell
                 */
                foreach($cellIterator as $cell){

                    $alias = null;
                    $title = trim(mb_strtolower($cell->getValue(), 'UTF-8'));
                    foreach(PriceListAlias::getAliasesCommonTitles() as $iAlias => $aliasCommonTitles){

                        if(is_array($aliasCommonTitles) && in_array($title, $aliasCommonTitles)){
                            $alias = $iAlias;
                            break;
                        }

                    }

                    $identifiers[$cell->getColumn()] = array(
                        'column' => $cell->getColumn(),
                        'alias' => $alias,
                        'value' => $cell->getValue(),
                        'cell' => $cell,
                    );

                }


            } else {

                //@TODO error identifiers row not found
                throw new \Exception('Identifiers row not found');

            }

            $options = array();

            $aliasesTitles = PriceListAlias::getAliasesTitles();
            foreach($aliasesTitles as $alias => $aliasTitle){
                $options[$alias] = $aliasTitle;
            }

            $parameters = $this->getDoctrine()->getRepository('ShopCatalogBundle:Parameter')->findBy(
                array(),
                array(
                    'name' => 'ASC',
                )
            );

            /**
             * @var $parameter \Shop\CatalogBundle\Entity\Parameter
             */
            foreach($parameters as $parameter){
                $options[PriceListAlias::ALIAS_PARAMETER_PREFIX . $parameter->getId()] = 'Параметр - ' . $parameter->getName();
            }

            $categories = $this->getDoctrine()->getRepository('ShopCatalogBundle:Category')->findBy(
                array(),
                array(
                    'name' => 'ASC',
                )
            );

            $manufacturers = $this->getDoctrine()->getRepository('ShopCatalogBundle:Manufacturer')->findBy(
                array(),
                array(
                    'name' => 'ASC',
                )
            );

            $priceListCurrentColumnsAliases = array();
            foreach($priceList->getAliases() as $priceListAlias){
                if($priceListAlias instanceof PriceListAlias){
                    $priceListCurrentColumnsAliases[$priceListAlias->getColumnName()] = $priceListAlias->getAliasName();
                }
            }

            return $this->render('ShopCatalogBundle:AdminPriceList:priceListAliases.html.twig', array(
                'requiredAliases' => PriceListAlias::getRequiredAliasesTitles(),
                'identifiers' => $identifiers,
                'options' => $options,
                'categories' => $categories,
                'manufacturers' => $manufacturers,
                'priceList' => $priceList,
                'priceListCurrentColumnsAliases' => $priceListCurrentColumnsAliases,
                'title' => $priceList->getName(),
            ));

        }

    }

    public function parsePriceListAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $priceList = $this->getPriceListRepository()->findOneBy(array(
            'id' => (int)$id,
        ));

        if(!$priceList instanceof PriceList){
            throw $this->createNotFoundException('Прайс-лист не найден');
        }

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

        $parameterOptionRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:ParameterOption');
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

                                            $parameter = $this->getDoctrine()->getRepository('ShopCatalogBundle:Parameter')->findOneBy(array(
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

                                                        $em->persist($parameterOption);

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
        $categoryRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Category');
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

                $em->persist($category);

            }

            $proposalsNames = array_keys($proposals);
            $formattedProposalsNames = $this->formatNames($proposalsNames);

            /**
             * @var $proposalRepository \Shop\CatalogBundle\Entity\ProposalRepository
             */
            $proposalRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Proposal');
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
                    $em->persist($proposal);

                }

                if(isset($proposalsParametersValues[$proposalName]) && is_array($proposalsParametersValues[$proposalName])){

                    $proposalParametersValues = $proposalsParametersValues[$proposalName];

                    $proposalParameterValuesMapper = new ProposalParameterValuesMapper($this->getDoctrine()->getManager(), $proposal);
                    $proposalParameterValuesMapper->mapParameterValues($proposalParametersValues);

                }

                if($proposal->getId()){

                    $pricesSku = array_keys($pricesData);

                    $priceRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Price');
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
                            $priceParameterValuesMapper = new PriceParameterValuesMapper($this->getDoctrine()->getManager(), $price);
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
                        $priceParameterValuesMapper = new PriceParameterValuesMapper($this->getDoctrine()->getManager(), $price);
                        $priceParameterValuesMapper->mapParameterValues($parameterValuesData, false);
                    }

                    $em->persist($price);

                }

            }

        }

//        var_dump($groupedRowsData);
//        die;

        $priceList->setStatus(PriceList::STATUS_PARSED);
        $priceList->setUpdateDate(new \DateTime());

        $em->flush();

        return $this->redirect($this->generateUrl('price_lists'));

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

    /**
     * @param $id
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function downloadPriceListAction($id)
    {

        $priceList = $this->getPriceListRepository()->findOneBy(array(
            'id' => (int)$id,
        ));

        if(!$priceList instanceof PriceList){
            throw $this->createNotFoundException('Прайс-лист не найден');
        }

        $path = $priceList->getPriceListFilePath();
        $content = @file_get_contents($path);

        $response = new Response();

        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $priceList->getName() . '.' . pathinfo($path, PATHINFO_EXTENSION));

        $response->setContent($content);
        return $response;
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePriceListAction($id)
    {

        $entity = $this->getPriceListRepository()->findOneBy(array(
            'id' => $id
        ));

        if($entity){

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('price_lists'));

    }

    /**
     * @return PriceListRepository
     */
    protected function getPriceListRepository(){
        return $this->getDoctrine()->getRepository('ShopCatalogBundle:PriceList');
    }

}
