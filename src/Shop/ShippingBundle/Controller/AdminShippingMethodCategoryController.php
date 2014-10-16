<?php

namespace Shop\ShippingBundle\Controller;

use Shop\ShippingBundle\Entity\ShippingMethod;
use Shop\ShippingBundle\Entity\ShippingMethodCategory;
use Shop\ShippingBundle\Entity\ShippingMethodCategoryAssemblyPrice;
use Shop\ShippingBundle\Entity\ShippingMethodCategoryLiftingPrice;
use Shop\ShippingBundle\Entity\ShippingMethodCategoryPrice;
use Shop\ShippingBundle\Mapper\ShippingAssemblyPriceMapper;
use Shop\ShippingBundle\Mapper\ShippingLiftingPriceMapper;
use Shop\ShippingBundle\Mapper\ShippingPriceMapper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminShippingMethodCategoryController
 * @package Shop\ShippingBundle\Controller
 */
class AdminShippingMethodCategoryController extends Controller
{

    /**
     * @param $shippingMethodId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function shippingCategoriesAction($shippingMethodId){

        $shippingMethod = $this->getShippingMethod($shippingMethodId);

        return $this->render('ShopShippingBundle:AdminShippingMethodCategory:shippingCategories.html.twig', array(
            'shippingMethod' => $shippingMethod,
            'shippingCategories' => $shippingMethod->getCategories(),
        ));

    }

    /**
     * @param $id
     * @param $shippingMethodId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function shippingCategoryAction($id, $shippingMethodId, Request $request){

        $shippingMethod = $this->getShippingMethod($shippingMethodId);

        $shippingCategoryRepository = $this->getDoctrine()->getRepository('ShopShippingBundle:ShippingMethodCategory');
        $shippingCategory = $shippingCategoryRepository->findOneBy(array(
            'id' => $id,
        ));

        if(!$shippingCategory instanceof ShippingMethodCategory){
            $shippingCategory = new ShippingMethodCategory();
        }

        $isNew = !$shippingCategory->getId();
        $form = $this->createForm('shipping_category', $shippingCategory);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $shippingMethod->addCategory($shippingCategory);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('shipping_categories', [
                'shippingMethodId' => $shippingMethod->getId(),
                'id' => $shippingCategory->getId(),
            ]));

        } else {

            return $this->render('ShopShippingBundle:AdminShippingMethodCategory:shippingCategory.html.twig', array(
                'form' => $form->createView(),
                'shippingMethod' => $shippingMethod,
                'shippingCategory' => $shippingCategory,
            ));

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteShippingCategoryAction($id)
    {

        $entity = $this->getDoctrine()->getManager()->getRepository('ShopShippingBundle:ShippingMethodCategory')->findOneBy(array(
            'id' => $id
        ));

        if($entity){

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('shipping_methods'));

    }

    /**
     * @param $shippingCategoryId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function shippingCategoryPricesAction($shippingCategoryId){

        $shippingCategory = $this->getShippingCategory($shippingCategoryId);

        return $this->render('ShopShippingBundle:AdminShippingMethod:shippingPrices.html.twig', array(
            'shippingMethod' => $shippingCategory->getShippingMethod(),
            'shippingCategory' => $shippingCategory,
            'shippingCategoryPrice' => $shippingCategory->getPrices(),
        ));

    }

    /**
     * @param $id
     * @param $shippingCategoryId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function shippingCategoryPriceAction($id, $shippingCategoryId, Request $request){

        $shippingCategory = $this->getShippingCategory($shippingCategoryId);

        $shippingCategoryPriceRepository = $this->getDoctrine()->getRepository('ShopShippingBundle:ShippingMethodCategoryPrice');
        $shippingCategoryPrice = $shippingCategoryPriceRepository->findOneBy(array(
            'id' => $id,
        ));

        if(!$shippingCategoryPrice instanceof ShippingMethodCategoryPrice){
            $shippingCategoryPrice = new ShippingMethodCategoryPrice();
        }

        $isNew = !$shippingCategoryPrice->getId();
        $mapper = new ShippingPriceMapper($shippingCategoryPrice);
        $form = $this->createForm('shipping_price', $mapper);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $shippingCategory->addPrice($shippingCategoryPrice);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('shipping_methods'));

        } else {

            return $this->render('ShopShippingBundle:AdminShippingMethod:shippingPrice.html.twig', array(
                'form' => $form->createView(),
                'shippingMethod' => $shippingCategory->getShippingMethod(),
                'shippingCategory' => $shippingCategory,
                'shippingCategoryPrice' => $shippingCategoryPrice,
            ));

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteShippingCategoryPriceAction($id)
    {

        $entity = $this->getDoctrine()->getManager()->getRepository('ShopShippingBundle:ShippingMethodCategoryPrice')->findOneBy(array(
            'id' => $id
        ));

        if($entity){

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('shipping_methods'));

    }

    /**
     * @param $id
     * @param $shippingCategoryId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function shippingCategoryLiftingPriceAction($id, $shippingCategoryId, Request $request){

        $shippingCategoryRepository = $this->getDoctrine()->getRepository('ShopShippingBundle:ShippingMethodCategory');
        $shippingCategory = $shippingCategoryRepository->findOneBy(array(
            'id' => $shippingCategoryId
        ));

        if(!$shippingCategory instanceof ShippingMethodCategory){
            throw $this->createNotFoundException('Shipping category not found');
        }

        $shippingCategoryLiftingPriceRepository = $this->getDoctrine()->getRepository('ShopShippingBundle:ShippingMethodCategoryLiftingPrice');
        $shippingCategoryLiftingPrice = $shippingCategoryLiftingPriceRepository->findOneBy(array(
            'id' => $id,
        ));

        if(!$shippingCategoryLiftingPrice instanceof ShippingMethodCategoryLiftingPrice){
            $shippingCategoryLiftingPrice = new ShippingMethodCategoryLiftingPrice();
        }

        $isNew = !$shippingCategoryLiftingPrice->getId();
        $mapper = new ShippingLiftingPriceMapper($shippingCategoryLiftingPrice);
        $form = $this->createForm('shipping_lifting_price', $mapper);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $shippingCategory->addLiftingPrice($shippingCategoryLiftingPrice);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('shipping_methods'));

        } else {

            return $this->render('ShopShippingBundle:AdminShippingMethod:shippingLiftingPrice.html.twig', array(
                'form' => $form->createView(),
                'shippingMethod' => $shippingCategory->getShippingMethod(),
                'shippingCategory' => $shippingCategory,
                'shippingCategoryLiftingPrice' => $shippingCategoryLiftingPrice,
            ));

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteShippingCategoryLiftingPriceAction($id)
    {

        $entity = $this->getDoctrine()->getManager()->getRepository('ShopShippingBundle:ShippingMethodCategoryLiftingPrice')->findOneBy(array(
            'id' => $id
        ));

        if($entity){

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('shipping_methods'));

    }

    /**
     * @param $id
     * @param $shippingCategoryId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function shippingCategoryAssemblyPriceAction($id, $shippingCategoryId, Request $request){

        $shippingCategoryRepository = $this->getDoctrine()->getRepository('ShopShippingBundle:ShippingMethodCategory');
        $shippingCategory = $shippingCategoryRepository->findOneBy(array(
            'id' => $shippingCategoryId
        ));

        if(!$shippingCategory instanceof ShippingMethodCategory){
            throw $this->createNotFoundException('Shipping category not found');
        }

        $shippingCategoryAssemblyPriceRepository = $this->getDoctrine()->getRepository('ShopShippingBundle:ShippingMethodCategoryAssemblyPrice');
        $shippingCategoryAssemblyPrice = $shippingCategoryAssemblyPriceRepository->findOneBy(array(
            'id' => $id,
        ));

        if(!$shippingCategoryAssemblyPrice instanceof ShippingMethodCategoryAssemblyPrice){
            $shippingCategoryAssemblyPrice = new ShippingMethodCategoryAssemblyPrice();
        }

        $isNew = !$shippingCategoryAssemblyPrice->getId();
        $mapper = new ShippingAssemblyPriceMapper($shippingCategoryAssemblyPrice);
        $form = $this->createForm('shipping_assembly_price', $mapper);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $shippingCategory->addAssemblyPrice($shippingCategoryAssemblyPrice);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('shipping_methods'));

        } else {

            return $this->render('ShopShippingBundle:AdminShippingMethod:shippingAssemblyPrice.html.twig', array(
                'form' => $form->createView(),
                'shippingMethod' => $shippingCategory->getShippingMethod(),
                'shippingCategory' => $shippingCategory,
                'shippingCategoryAssemblyPrice' => $shippingCategoryAssemblyPrice,
            ));

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteShippingCategoryAssemblyPriceAction($id)
    {

        $entity = $this->getDoctrine()->getManager()->getRepository('ShopShippingBundle:ShippingMethodCategoryAssemblyPrice')->findOneBy(array(
            'id' => $id
        ));

        if($entity){

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('shipping_methods'));

    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getShippingMethodRepository()
    {
        $shippingMethodRepository = $this->getDoctrine()->getRepository('ShopShippingBundle:ShippingMethod');
        return $shippingMethodRepository;
    }

    /**
     * @param $shippingMethodId
     * @return ShippingMethod
     */
    protected function getShippingMethod($shippingMethodId)
    {
        $shippingMethodRepository = $this->getShippingMethodRepository();
        $shippingMethod = $shippingMethodRepository->findOneBy(array(
            'id' => $shippingMethodId
        ));

        if (!$shippingMethod instanceof ShippingMethod) {
            throw $this->createNotFoundException('Shipping method not found');
        }
        return $shippingMethod;
    }

    /**
     * @param $shippingCategoryId
     * @return ShippingMethodCategory
     */
    protected function getShippingCategory($shippingCategoryId)
    {
        $shippingCategoryRepository = $this->getDoctrine()->getRepository('ShopShippingBundle:ShippingMethodCategory');
        $shippingCategory = $shippingCategoryRepository->findOneBy(array(
            'id' => $shippingCategoryId
        ));

        if (!$shippingCategory instanceof ShippingMethodCategory) {
            throw $this->createNotFoundException('Shipping category not found');
        }
        return $shippingCategory;
    }


}
