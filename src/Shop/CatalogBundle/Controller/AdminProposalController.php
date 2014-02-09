<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\CategoryParameter;
use Shop\CatalogBundle\Entity\Parameter;
use Shop\CatalogBundle\Entity\ParameterOption;
use Shop\CatalogBundle\Entity\ParameterValue;
use Shop\CatalogBundle\Entity\Price;
use Shop\CatalogBundle\Entity\Proposal;
use Shop\CatalogBundle\Form\Type\PriceType;
use Shop\CatalogBundle\Form\Type\ProposalType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminProposalController
 * @package Shop\CatalogBundle\Controller
 */
class AdminProposalController extends Controller
{

    /**
     * @param $categoryId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function proposalsAction($categoryId)
    {

        $category = $this->getDoctrine()->getRepository('ShopCatalogBundle:Category')->findOneBy(array(
            'id' => $categoryId,
        ));

        if(!$category instanceof Category){
            return $this->redirect($this->generateUrl('categories'));
        }

        return $this->render('ShopCatalogBundle:AdminProposal:proposals.html.twig', array(
            'category' => $category,
        ));

    }

    /**
     * @param $categoryId
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function proposalAction($categoryId, $id, Request $request)
    {

        $category = $this->getDoctrine()->getRepository('ShopCatalogBundle:Category')->findOneBy(array(
            'id' => $categoryId,
        ));

        if(!$category instanceof Category){
            return $this->redirect($this->generateUrl('categories'));
        }

        /**
         * @var $proposalRepository \Shop\CatalogBundle\Entity\ProposalRepository
         */
        $proposalRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Proposal');
        $proposal = $proposalRepository->findOneBy(array(
            'id' => $id
        ));

        if(!$proposal instanceof Proposal){
            $proposal = new Proposal();
        }

        $isNew = !$proposal->getId();
        $form = $this->createForm(new ProposalType($category), $proposal);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $em->persist($proposal);
            }

            $formData = $request->get($form->getName());
            $parameterValuesData = array();

            $category->getParameters()->map(function(CategoryParameter $categoryParameter) use($formData, &$parameterValuesData) {

                $parameterElementName = 'parameter' . $categoryParameter->getParameterId();
                if(isset($formData[$parameterElementName])){

                    $parameterValuesData[$categoryParameter->getParameterId()] = $formData[$parameterElementName];

                }

            });

            $optionsRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:ParameterOption');

            /**
             * @var $parameterValue ParameterValue
             */
            foreach($proposal->getParameterValues() as $parameterValue){

                if(isset($parameterValuesData[$parameterValue ->getParameterId()])){

                    $optionId = $parameterValuesData[$parameterValue->getParameterId()];
                    $parameterOption = $optionId ? $optionsRepository->findOneBy(array(
                        'id' => (int)$optionId,
                    )) : null;

                    if($parameterOption instanceof ParameterOption){
                        $parameterValue->setOption($parameterOption);
                    } else {
                        $proposal->removeParameterValue($parameterValue);
                    }

                    unset($parameterValuesData[$parameterValue->getParameterId()]);

                }

            }

            if($parameterValuesData){

                $parametersRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Parameter');

                foreach($parameterValuesData as $parameterId => $optionId){

                    $parameter = $parametersRepository->findOneBy(array(
                        'id' => (int)$parameterId,
                    ));

                    $parameterOption = $optionId ? $optionsRepository->findOneBy(array(
                        'id' => (int)$optionId,
                    )) : null;

                    if($parameter instanceof Parameter && $parameterOption instanceof ParameterOption){

                        $parameterValue = new ParameterValue();
                        $parameterValue
                            ->setParameter($parameter)
                            ->setOption($parameterOption);

                        $proposal->addParameterValue($parameterValue);

                    }

                }

            }

            $em->flush();

            return $this->redirect($this->generateUrl('proposals', array('categoryId' => $category->getId())));

        } else {

            if(!$isNew){

                $proposal->getParameterValues()->map(function(ParameterValue $value) use ($form) {

                    try{

                        $element = $form->get('parameter' . $value->getParameterId());
                        $element->setData($value->getOptionId());

                    } catch(\Exception $e){};

                });

            }

            return $this->render('ShopCatalogBundle:AdminProposal:proposal.html.twig', array(
                'title' => $isNew ? 'Добавление товара' : 'Изменение товара',
                'form' => $form->createView(),
                'category' => $category,
                'proposal' => $proposal,
            ));

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteProposalAction($id)
    {

        /**
         * @var $proposalRepository \Shop\CatalogBundle\Entity\ProposalRepository
         */
        $proposalRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Proposal');
        $proposal = $proposalRepository->findOneBy(array(
            'id' => $id
        ));

        if(!$proposal instanceof Proposal){
            return $this->redirect($this->generateUrl('categories'));
        }

        $categoryId = $proposal->getCategoryId();

        $em = $this->getDoctrine()->getManager();
        $em->remove($proposal);
        $em->flush();

        return $this->redirect($this->generateUrl('proposals', array(
            'categoryId' => $categoryId,
        )));

    }

    /**
     * @param $proposalId
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function proposalPriceAction($proposalId, $id, Request $request)
    {

        /**
         * @var $proposalRepository \Shop\CatalogBundle\Entity\ProposalRepository
         */
        $proposalRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Proposal');
        $proposal = $proposalRepository->findOneBy(array(
            'id' => $proposalId
        ));

        if(!$proposal instanceof Proposal){
            return $this->redirect($this->generateUrl('categories'));
        }

        $category = $proposal->getCategory();

        $price = $this->getDoctrine()->getRepository('ShopCatalogBundle:Price')->findOneBy(array(
            'id' => $id,
        ));
        if(!$price instanceof Price){
            $price = new Price();
        }

        $isNew = !$price->getId();
        $form = $this->createForm(new PriceType($category), $price);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            $formData = $request->get($form->getName());
            $parameterValuesData = array();

            $category->getParameters()->map(function(CategoryParameter $categoryParameter) use($formData, &$parameterValuesData) {

                $parameterElementName = 'parameter' . $categoryParameter->getParameterId();
                if(isset($formData[$parameterElementName])){

                    $parameterValuesData[$categoryParameter->getParameterId()] = $formData[$parameterElementName];

                }

            });

            $optionsRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:ParameterOption');

            /**
             * @var $parameterValue ParameterValue
             */
            foreach($price->getParameterValues() as $parameterValue){

                if(isset($parameterValuesData[$parameterValue ->getParameterId()])){

                    $optionId = $parameterValuesData[$parameterValue->getParameterId()];
                    $parameterOption = $optionId ? $optionsRepository->findOneBy(array(
                        'id' => (int)$optionId,
                    )) : null;

                    if($parameterOption instanceof ParameterOption){
                        $parameterValue->setOption($parameterOption);
                    } else {
                        $price->removeParameterValue($parameterValue);
                    }

                    unset($parameterValuesData[$parameterValue->getParameterId()]);

                }

            }

            if($parameterValuesData){

                $parametersRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Parameter');

                foreach($parameterValuesData as $parameterId => $optionId){

                    $parameter = $parametersRepository->findOneBy(array(
                        'id' => (int)$parameterId,
                    ));

                    $parameterOption = $optionId ? $optionsRepository->findOneBy(array(
                        'id' => (int)$optionId,
                    )) : null;

                    if($parameter instanceof Parameter && $parameterOption instanceof ParameterOption){

                        $parameterValue = new ParameterValue();
                        $parameterValue
                            ->setParameter($parameter)
                            ->setOption($parameterOption);

                        $price->addParameterValue($parameterValue);

                    }

                }

            }

            $em->flush();

            return $this->redirect($this->generateUrl('proposal', array('categoryId' => $category->getId(), 'id' => $proposal->getId())));

        } else {

            if(!$isNew){

                $price->getParameterValues()->map(function(ParameterValue $value) use ($form) {

                    try{

                        $element = $form->get('parameter' . $value->getParameterId());
                        $element->setData($value->getOptionId());

                    } catch(\Exception $e){};

                });

            }

            return $this->render('ShopCatalogBundle:AdminProposal:proposalPrice.html.twig', array(
                'title' => $isNew ? 'Добавление цены' : 'Изменение цены',
                'form' => $form->createView(),
                'category' => $category,
                'proposal' => $proposal,
                'price' => $price,
            ));

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteProposalPriceAction($id)
    {

        $price = $this->getDoctrine()->getRepository('ShopCatalogBundle:Price')->findOneBy(array(
            'id' => $id,
        ));

        if(!$price instanceof Price){
            return $this->redirect($this->generateUrl('categories'));
        }

        $proposalId = $price->getProposalId();
        $categoryId = $price->getProposal()->getCategoryId();

        $em = $this->getDoctrine()->getManager();
        $em->remove($price);
        $em->flush();

        return $this->redirect($this->generateUrl('proposal', array('categoryId' => $categoryId, 'id' => $proposalId)));

    }

}
