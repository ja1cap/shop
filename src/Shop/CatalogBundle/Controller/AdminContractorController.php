<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Entity\Contractor;
use Shop\CatalogBundle\Entity\ContractorCurrency;
use Shop\CatalogBundle\Form\Type\ContractorCurrencyType;
use Shop\CatalogBundle\Form\Type\ContractorType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Shop\CatalogBundle\Entity\ContractorRepository;

/**
 * Class AdminContractorController
 * @package Shop\CatalogBundle\Controller
 */
class AdminContractorController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contractorsAction()
    {

        $contractors = $this->getDoctrine()->getRepository('ShopCatalogBundle:Contractor')->findBy(array(), array(
            'name' => 'ASC',
        ));

        return $this->render('ShopCatalogBundle:AdminContractor:contractors.html.twig', array(
            'contractors' => $contractors,
        ));

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function contractorAction($id, Request $request)
    {

        $repository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Contractor');
        $contractor = $repository->findOneBy(array(
            'id' => $id
        ));

        if(!$contractor instanceof Contractor){
            $contractor = new Contractor;
        }

        $isNew = !$contractor->getId();
        $form = $this->createForm(new ContractorType(), $contractor);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $em->persist($contractor);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('contractors'));

        } else {

            return $this->render('ShopCatalogBundle:AdminContractor:contractor.html.twig', array(
                'title' => $isNew ? 'Добавление контрагента' : 'Изменение контрагента',
                'form' => $form->createView(),
                'contractor' => $contractor,
            ));

        }

    }

    /**
     * @param $contractorId
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function contractorCurrencyAction($contractorId, $id, Request $request){

        $contractor = $this->getContractorRepository()->findOneBy(array(
            'id' => (int)$contractorId
        ));

        if(!$contractor instanceof Contractor){
            throw $this->createNotFoundException('Контрагент не найден');
        }

        $contractorCurrency = $this->getDoctrine()->getRepository('ShopCatalogBundle:ContractorCurrency')->findOneBy(array(
            'id' => $id,
            'contractorId' => $contractor->getId(),
        ));

        if(!$contractorCurrency instanceof ContractorCurrency){
            $contractorCurrency = new ContractorCurrency();
        }

        $isNew = !$contractorCurrency->getId();
        $form = $this->createForm(new ContractorCurrencyType(), $contractorCurrency);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $contractor->addCurrency($contractorCurrency);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('contractor', array(
                'id' => $contractor->getId()
            )));

        } else {

            return $this->render('ShopCatalogBundle:AdminContractor:contractorCurrency.html.twig', array(
                'title' => $isNew ? 'Добавление валюты контрагента' : 'Изменение валюты контрагента',
                'form' => $form->createView(),
                'contractor' => $contractor,
            ));

        }


    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteContractorCurrencyAction($id)
    {

        $contractorCurrency = $this->getDoctrine()->getRepository('ShopCatalogBundle:ContractorCurrency')->findOneBy(array(
            'id' => $id,
        ));

        if(!$contractorCurrency instanceof ContractorCurrency){
            return $this->redirect($this->generateUrl('contractors'));
        }

        $contractorId = $contractorCurrency->getContractorId();

        $em = $this->getDoctrine()->getManager();
        $em->remove($contractorCurrency);
        $em->flush();

        return $this->redirect($this->generateUrl('contractor', array('id' => $contractorId)));

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteContractorAction($id)
    {

        $entity = $this->getContractorRepository()->findOneBy(array(
            'id' => $id
        ));

        if($entity){

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('contractors'));

    }

    /**
     * @return ContractorRepository
     */
    public function getContractorRepository(){
        return $this->getDoctrine()->getRepository('ShopCatalogBundle:Contractor');
    }

}
