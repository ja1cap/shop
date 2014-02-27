<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Entity\PriceList;
use Shop\CatalogBundle\Form\Type\PriceListType;
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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function priceListAction(Request $request)
    {

        $entity = new PriceList();

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

    public function parsePriceListAction($id)
    {

        $priceList = $this->getPriceListRepository()->findOneBy(array(
            'id' => (int)$id,
        ));

        if(!$priceList instanceof PriceList){
            throw $this->createNotFoundException('Прайс-лист не найден');
        }

        $filePath = $priceList->getPriceListFilePath();
        $objPHPExcel = \PHPExcel_IOFactory::load($filePath);
        $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();

        $pricesData = array();

        /**
         * @var $row \PHPExcel_Worksheet_Row
         */
        foreach($rowIterator as $row){

            $cellIterator = $row->getCellIterator();
            $priceData = array(
                'sku' => null,
                'value' => null,
                'currencyNumericCode' => null
            );

            /**
             * @var $cell \PHPExcel_Cell
             */
            foreach($cellIterator as $cell){

                switch($cell->getColumn()){
                    case 'A':
                        $priceData['sku'] = $cell->getValue();
                        break;
                    case 'B':
                        $priceData['value'] = $cell->getValue();
                        break;
                    case 'C':
                        $priceData['currencyNumericCode'] = $cell->getValue();
                        break;
                }
            }

            if(count(array_filter($priceData)) == count($priceData) && $priceData['sku']){

                $pricesData[$priceData['sku']] = $priceData;

            }

        }

        $pricesSku = array_keys($pricesData);
        if($pricesSku){

        }
        $priceRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Price');
        $prices = $priceRepository->findBy(array(
            'sku' => array_unique($pricesSku)
        ));

        /**
         * @var $price Price
         */
        foreach($prices as $price){

            $priceData = $pricesData[$price->getSku()];
            $price->setValue($priceData['value']);
            $price->setCurrencyNumericCode($priceData['currencyNumericCode']);

        }

        $priceList->setStatus(PriceList::STATUS_PARSED);
        $priceList->setUpdateDate(new \DateTime());

        $em = $this->getDoctrine()->getManager();
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
        $content = file_get_contents($path);

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
