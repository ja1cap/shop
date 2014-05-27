<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\PriceList\PriceListBuilder;
use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\Manufacturer;
use Shop\CatalogBundle\Entity\PriceList;
use Shop\CatalogBundle\Entity\PriceListAlias;
use Shop\CatalogBundle\Form\Type\CreatePriceListType;
use Shop\CatalogBundle\Form\Type\PriceListType;
use Shop\CatalogBundle\PriceList\PriceListMapper;
use Shop\CatalogBundle\PriceList\PriceListParser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Shop\CatalogBundle\Entity\PriceListRepository;
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

    public function createPriceListAction(Request $request){

        $builder = new PriceListBuilder($this->getDoctrine()->getManager());
        $mapper = new PriceListMapper();

        $form = $this->createForm(new CreatePriceListType(), $mapper);
        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $builder->build($mapper->getCategoryId(), $mapper->getContractorsIds(), $mapper->getManufacturersIds());

            $priceList = $builder->getPriceList();
            $objPHPExcel = $builder->getObjPHPExcel();

            $priceList->setName($mapper->getName());

            $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
            $fileName = $priceList->getFileName();
            $path = $priceList->getUploadDirPath() . DIRECTORY_SEPARATOR . $fileName;
            $objWriter->save($path);
            chmod($path, 0777);

            $em = $this->getDoctrine()->getManager();

            $em->persist($priceList);
            $em->flush();

            $objWriter = new \PHPExcel_Writer_HTML($objPHPExcel);
            return $this->render('ShopCatalogBundle:AdminPriceList:viewPriceList.html.twig', array(
                'priceList' => $priceList,
                'priceListSheetData' => $objWriter->generateSheetData(),
            ));

        } else {

            return $this->render('ShopCatalogBundle:AdminPriceList:priceList.html.twig', array(
                'title' => 'Создание прайс-листа',
                'form' => $form->createView(),
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

                $parameters = $this->getDoctrine()->getRepository('ShopCatalogBundle:Parameter')->findBy(
                    array(),
                    array(
                        'name' => 'ASC',
                    )
                );

                $parametersNames = array();

                /**
                 * @var $parameter \Shop\CatalogBundle\Entity\Parameter
                 */
                foreach($parameters as $parameter){
                    $parametersNames[$parameter->getId()] = $parameter->getName();
                }

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

                    if(!$alias){

                        $maxPercent = 0;
                        $mostRelevantParameterId = null;
                        $mostRelevantParameterName = null;

                        foreach($parametersNames as $parameterId => $parameterName){

                            $formattedParameterName = trim(mb_strtolower($parameterName, 'UTF-8'));
                            similar_text($formattedParameterName, $title, $percent);

                            if($percent > 75 && $percent > $maxPercent){

                                $maxPercent = $percent;
                                $mostRelevantParameterId = $parameterId;

                            }

                        }

                        if($mostRelevantParameterId){
                            $alias = PriceListAlias::ALIAS_PARAMETER_PREFIX . $mostRelevantParameterId;
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

            /**
             * @var $parameter \Shop\CatalogBundle\Entity\Parameter
             */
            foreach($parameters as $parameter){
                $options[PriceListAlias::ALIAS_PARAMETER_PREFIX . $parameter->getId()] = $parameter->getName() . ' (параметр)';
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

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function parsePriceListAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $priceList = $this->getPriceListRepository()->findOneBy(array(
            'id' => (int)$id,
        ));

        if(!$priceList instanceof PriceList){
            throw $this->createNotFoundException('Прайс-лист не найден');
        }

        $parser = new PriceListParser($em, $this->get('weasty_money.currency.code.converter'));
        $priceList = $parser->parse($priceList);

        $priceList->setStatus(PriceList::STATUS_PARSED);
        $priceList->setUpdateDate(new \DateTime());

        $em->flush();

        return $this->redirect($this->generateUrl('price_lists'));

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
