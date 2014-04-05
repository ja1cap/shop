<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Entity\ShippingMethod;
use Shop\CatalogBundle\Mapper\ShippingMethodMapper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminShippingMethodController
 * @package Shop\CatalogBundle\Controller
 */
class AdminShippingMethodController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function shippingMethodsAction()
    {

        $shippingMethods = $this->getDoctrine()->getRepository('ShopCatalogBundle:ShippingMethod')->findBy(array(), array(
            'name' => 'ASC',
        ));

        return $this->render('ShopCatalogBundle:AdminShippingMethod:shippingMethods.html.twig', array(
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

        $repository = $this->getDoctrine()->getRepository('ShopCatalogBundle:ShippingMethod');
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

            return $this->render('ShopCatalogBundle:AdminShippingMethod:shippingMethod.html.twig', array(
                'title' => $isNew ? 'Добавление способа доставки' : 'Изменение способа доставки',
                'form' => $form->createView(),
                'shippingMethod' => $shippingMethod,
            ));

        }

    }

    public function deleteShippingMethodAction($id)
    {

        $entity = $this->getDoctrine()->getManager()->getRepository('ShopCatalogBundle:ShippingMethod')->findOneBy(array(
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
