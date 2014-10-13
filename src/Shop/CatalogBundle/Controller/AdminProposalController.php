<?php

namespace Shop\CatalogBundle\Controller;

use Application\Sonata\MediaBundle\Entity\Media;
use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\CategoryParameter;
use Shop\CatalogBundle\Entity\ParameterValue;
use Shop\CatalogBundle\Entity\Price;
use Shop\CatalogBundle\Entity\Proposal;
use Shop\CatalogBundle\Form\Type\PriceType;
use Shop\CatalogBundle\Mapper\PriceMapper;
use Shop\CatalogBundle\Mapper\PriceParameterValuesMapper;
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

        $category = $this->get('shop_catalog.category.repository')->findOneBy(array(
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
     * @param null $categoryId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function proposalsBrowserAction($categoryId = null){

        $categoryRepository = $this->get('shop_catalog.category.repository');
        $category = $categoryId ? $categoryRepository->findOneBy(array('id' => $categoryId)) : null;
        $categories = $categoryRepository->findBy(array(), array('name' => 'ASC'));

        return $this->render('ShopCatalogBundle:AdminProposal:proposalsBrowser.html.twig', array(
            'category' => $category,
            'categories' => $categories,
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

        $category = $this->get('shop_catalog.category.repository')->findOneBy(array(
            'id' => $categoryId,
        ));

        if(!$category instanceof Category){
            return $this->redirect($this->generateUrl('categories'));
        }

        /**
         * @var $proposalRepository \Shop\CatalogBundle\Entity\ProposalRepository
         */
        $proposalRepository = $this->get('shop_catalog.proposal.repository');
        $proposal = $proposalRepository->findOneBy(array(
            'id' => $id,
        ));

        if(!$proposal instanceof Proposal){
            $proposal = new Proposal();
        }

        $isNew = !$proposal->getId();
        $formType = $request->get('_form_type', 'shop_catalog_proposal');
        $form = $this->createForm($formType, $proposal);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $proposal->setCategory($category);
                $em->persist($proposal);
            }

            $em->flush();

            $redirectRoute = $request->get('_redirect_route', $request->get('_route'));
            return $this->redirect($this->generateUrl($redirectRoute, array('categoryId' => $category->getId(), 'id' => $proposal->getId())));

        } else {

            return $this->render('ShopCatalogBundle:AdminProposal:proposal.html.twig', array(
                'title' => $isNew ? 'Добавление товара' : null,
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
        $proposalRepository = $this->get('shop_catalog.proposal.repository');
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
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function proposalPricesAction($id){

        /**
         * @var $proposalRepository \Shop\CatalogBundle\Entity\ProposalRepository
         */
        $proposalRepository = $this->get('shop_catalog.proposal.repository');
        $proposal = $proposalRepository->findOneBy(array(
            'id' => $id
        ));

        if(!$proposal instanceof Proposal){
            throw $this->createNotFoundException('Товар не найден');
        }

        return $this->render('ShopCatalogBundle:AdminProposal:proposalPrices.html.twig', array(
            'category' => $proposal->getCategory(),
            'proposal' => $proposal,
        ));

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
        $proposalRepository = $this->get('shop_catalog.proposal.repository');
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
            $price->setContractor($proposal->getDefaultContractor());
        }

        $isNew = !$price->getId();
        $mapper = new PriceMapper($price);

        $form = $this->createForm(new PriceType($category), $mapper);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            $formData = $request->get($form->getName());
            $parameterValuesData = array();

            $category->getParameters()->map(function(CategoryParameter $categoryParameter) use($formData, &$parameterValuesData) {

                $parameterElementName = 'parameter' . $categoryParameter->getParameterId();
                if(isset($formData[$parameterElementName])){
                    $parameterValuesData[$categoryParameter->getParameterId()] = $formData[$parameterElementName];
                } else {
                    $parameterValuesData[$categoryParameter->getParameterId()] = array();
                }

            });

            $priceParameterValuesMapper = new PriceParameterValuesMapper($this->getDoctrine()->getManager(), $price);
            $priceParameterValuesMapper->mapParameterValues($parameterValuesData);

            if($isNew){
                $proposal->addPrice($price);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('proposal_price', array(
                'proposalId' => $proposal->getId(),
                'id' => $price->getId(),
            )));

        } else {

            if(!$isNew){

                $parameterValuesData = array();

                $price->getParameterValues()->map(function(ParameterValue $value) use (&$parameterValuesData) {

                    $parameterFieldName = 'parameter' . $value->getParameterId();
                    if(!isset($parameterValuesData[$parameterFieldName])){
                        $parameterValuesData[$parameterFieldName] = array();
                    }

                    $parameterValuesData[$parameterFieldName][] = $value->getOptionId();

                });

                foreach($parameterValuesData as $fieldName => $value){

                    try {

                        $field = $form->get($fieldName);

                        if(is_array($value) && !$field->getConfig()->getOption('multiple')){
                            $value = current($value);
                        }

                        $field->setData($value);

                    } catch(\Exception $e){};

                }

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

        $em = $this->getDoctrine()->getManager();
        $em->remove($price);
        $em->flush();

        return $this->redirect($this->generateUrl('proposal_prices', array('id' => $proposalId)));

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function proposalImagesAction($id){

        /**
         * @var $proposalRepository \Shop\CatalogBundle\Entity\ProposalRepository
         */
        $proposalRepository = $this->get('shop_catalog.proposal.repository');
        $proposal = $proposalRepository->findOneBy(array(
            'id' => $id
        ));

        if(!$proposal instanceof Proposal){
            throw $this->createNotFoundException('Товар не найден');
        }

        return $this->render('ShopCatalogBundle:AdminProposal:proposalImages.html.twig', array(
            'category' => $proposal->getCategory(),
            'proposal' => $proposal,
        ));

    }

    /**
     * @param $proposalId
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function proposalImageAction($proposalId, $id, Request $request)
    {

        /**
         * @var $proposalRepository \Shop\CatalogBundle\Entity\ProposalRepository
         */
        $proposalRepository = $this->get('shop_catalog.proposal.repository');
        $proposal = $proposalRepository->findOneBy(array(
            'id' => $proposalId
        ));

        if(!$proposal instanceof Proposal){
            throw $this->createNotFoundException('Товар не найден');
        }

        /**
         * @var \Sonata\MediaBundle\Entity\MediaManager $mediaManager
         */
        $mediaManager = $this->get('sonata.media.manager.media');
        $image = $mediaManager->findOneBy(array(
            'id' => $id,
        ));

        if(!$image instanceof Media){
            $image = $mediaManager->create();
        }

        $isNew = !$image->getId();
        $form = $this->createForm('weasty_admin_media_image_type', $image);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            if($isNew){

                $image->setContext($form->getConfig()->getOption('context'));
                $image->setProviderName($form->getConfig()->getOption('provider'));

                $proposal->addMediaImage($image);

            }

            $mediaManager->save($image);

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirect($this->generateUrl('proposal_images', array('id' => $proposal->getId())));

        } else {

            return $this->render('ShopCatalogBundle:AdminProposal:proposalImage.html.twig', array(
                'title' => $isNew ? 'Добавление' : 'Изменение',
                'form' => $form->createView(),
                'category' => $proposal->getCategory(),
                'proposal' => $proposal,
                'image' => $image,
            ));

        }

    }

    /**
     * @param $proposalId
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function proposalSetMainImageAction($proposalId, $id){

        /**
         * @var $proposalRepository \Shop\CatalogBundle\Entity\ProposalRepository
         */
        $proposalRepository = $this->get('shop_catalog.proposal.repository');
        $proposal = $proposalRepository->findOneBy(array(
            'id' => $proposalId
        ));

        if(!$proposal instanceof Proposal){
            throw $this->createNotFoundException('Товар не найден');
        }

        /**
         * @var \Sonata\MediaBundle\Entity\MediaManager $mediaManager
         */
        $mediaManager = $this->get('sonata.media.manager.media');
        $image = $mediaManager->findOneBy(array(
            'id' => $id,
        ));

        if(!$image instanceof Media){
            throw $this->createNotFoundException('Фотография не найдена');
        }

        $proposal->setMainMediaImage($image);
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirect($this->generateUrl('proposal_images', array('id' => $proposal->getId())));


    }

    /**
     * @param $proposalId
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function deleteProposalImageAction($proposalId, $id)
    {

        /**
         * @var $proposalRepository \Shop\CatalogBundle\Entity\ProposalRepository
         */
        $proposalRepository = $this->get('shop_catalog.proposal.repository');
        $proposal = $proposalRepository->findOneBy(array(
            'id' => $proposalId
        ));

        if(!$proposal instanceof Proposal){
            throw $this->createNotFoundException('Товар не найден');
        }

        /**
         * @var \Sonata\MediaBundle\Entity\MediaManager $mediaManager
         */
        $mediaManager = $this->get('sonata.media.manager.media');
        $image = $mediaManager->findOneBy(array(
            'id' => $id,
        ));

        if(!$image instanceof Media){
            throw $this->createNotFoundException('Фотография не найдена');
        }

        $proposal->removeMediaImage($image);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirect($this->generateUrl('proposal_images', array('id' => $proposal->getId())));

    }

}
