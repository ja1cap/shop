<?php

namespace Shop\ShippingBundle\Controller;

use Shop\ShippingBundle\Entity\ShippingMethod;
use Shop\ShippingBundle\Entity\ShippingMethodLiftingPrice;
use Shop\ShippingBundle\Entity\ShippingMethodPrice;
use Shop\ShippingBundle\Mapper\ShippingLiftingPriceMapper;
use Shop\ShippingBundle\Mapper\ShippingMethodMapper;
use Shop\ShippingBundle\Mapper\ShippingPriceMapper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminShippingMethodController
 * @package Shop\ShippingBundle\Controller
 */
class AdminShippingMethodController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function shippingMethodsAction()
    {

        $shippingMethods = $this->getDoctrine()->getRepository('ShopShippingBundle:ShippingMethod')->findBy(array(), array(
            'name' => 'ASC',
        ));

        return $this->render('ShopShippingBundle:AdminShippingMethod:shippingMethods.html.twig', array(
            'shippingMethods' => $shippingMethods,
        ));

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function shippingMethodAction($id, Request $request)
    {

        $repository = $this->getDoctrine()->getRepository('ShopShippingBundle:ShippingMethod');
        $shippingMethod = $repository->findOneBy(array(
            'id' => $id
        ));

        if(!$shippingMethod instanceof ShippingMethod){
            $shippingMethod = new ShippingMethod;
        }

        $isNew = !$shippingMethod->getId();
        $mapper = new ShippingMethodMapper($this->container, $shippingMethod);
        $form = $this->createForm('shipping_method', $mapper);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $em->persist($shippingMethod);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('shipping_methods'));

        } else {

            return $this->render('ShopShippingBundle:AdminShippingMethod:shippingMethod.html.twig', array(
                'title' => $isNew ? 'Добавление способа доставки' : 'Изменение способа доставки',
                'form' => $form->createView(),
                'shippingMethod' => $shippingMethod,
            ));

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteShippingMethodAction($id)
    {

        $entity = $this->getDoctrine()->getManager()->getRepository('ShopShippingBundle:ShippingMethod')->findOneBy(array(
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
     * @param $shippingMethodId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function shippingMethodPriceAction($id, $shippingMethodId, Request $request){

        $shippingMethodRepository = $this->getDoctrine()->getRepository('ShopShippingBundle:ShippingMethod');
        $shippingMethod = $shippingMethodRepository->findOneBy(array(
            'id' => $shippingMethodId
        ));

        if(!$shippingMethod instanceof ShippingMethod){
            throw $this->createNotFoundException('Shipping method not found');
        }

        $shippingMethodPriceRepository = $this->getDoctrine()->getRepository('ShopShippingBundle:ShippingMethodPrice');
        $shippingMethodPrice = $shippingMethodPriceRepository->findOneBy(array(
            'id' => $id,
        ));

        if(!$shippingMethodPrice instanceof ShippingMethodPrice){
            $shippingMethodPrice = new ShippingMethodPrice();
        }

        $isNew = !$shippingMethodPrice->getId();
        $mapper = new ShippingPriceMapper($shippingMethodPrice);
        $form = $this->createForm('shipping_price', $mapper);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $shippingMethod->addPrice($shippingMethodPrice);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('shipping_methods'));

        } else {

            return $this->render('ShopShippingBundle:AdminShippingMethod:shippingPrice.html.twig', array(
                'title' => 'Стоимость доставки',
                'form' => $form->createView(),
                'shippingMethod' => $shippingMethod,
                'shippingMethodPrice' => $shippingMethodPrice,
            ));

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteShippingMethodPriceAction($id)
    {

        $entity = $this->getDoctrine()->getManager()->getRepository('ShopShippingBundle:ShippingMethodPrice')->findOneBy(array(
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
     * @param $shippingMethodId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function shippingMethodLiftingPriceAction($id, $shippingMethodId, Request $request){

        $shippingMethodRepository = $this->getDoctrine()->getRepository('ShopShippingBundle:ShippingMethod');
        $shippingMethod = $shippingMethodRepository->findOneBy(array(
            'id' => $shippingMethodId
        ));

        if(!$shippingMethod instanceof ShippingMethod){
            throw $this->createNotFoundException('Shipping method not found');
        }

        $shippingMethodLiftingPriceRepository = $this->getDoctrine()->getRepository('ShopShippingBundle:ShippingMethodLiftingPrice');
        $shippingMethodLiftingPrice = $shippingMethodLiftingPriceRepository->findOneBy(array(
            'id' => $id,
        ));

        if(!$shippingMethodLiftingPrice instanceof ShippingMethodLiftingPrice){
            $shippingMethodLiftingPrice = new ShippingMethodLiftingPrice();
        }

        $isNew = !$shippingMethodLiftingPrice->getId();
        $mapper = new ShippingLiftingPriceMapper($shippingMethodLiftingPrice);
        $form = $this->createForm('shipping_lifting_price', $mapper);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $shippingMethod->addLiftingPrice($shippingMethodLiftingPrice);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('shipping_methods'));

        } else {

            return $this->render('ShopShippingBundle:AdminShippingMethod:shippingLiftingPrice.html.twig', array(
                'title' => 'Стоимость доставки',
                'form' => $form->createView(),
                'shippingMethod' => $shippingMethod,
                'shippingMethodLiftingPrice' => $shippingMethodLiftingPrice,
            ));

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteShippingMethodLiftingPriceAction($id)
    {

        $entity = $this->getDoctrine()->getManager()->getRepository('ShopShippingBundle:ShippingMethodLiftingPrice')->findOneBy(array(
            'id' => $id
        ));

        if($entity){

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('shipping_methods'));

    }

}
