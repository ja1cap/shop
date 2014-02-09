<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Entity\Manufacturer;
use Shop\CatalogBundle\Form\Type\ManufacturerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminManufacturerController
 * @package Shop\CatalogBundle\Controller
 */
class AdminManufacturerController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function manufacturersAction()
    {

        $manufacturers = $this->getDoctrine()->getRepository('ShopCatalogBundle:Manufacturer')->findBy(array(), array(
            'name' => 'ASC',
        ));

        return $this->render('ShopCatalogBundle:AdminManufacturer:manufacturers.html.twig', array(
            'manufacturers' => $manufacturers,
        ));

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function manufacturerAction($id, Request $request)
    {

        $repository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Manufacturer');
        $entity = $repository->findOneBy(array(
            'id' => $id
        ));

        if(!$entity instanceof Manufacturer){
            $entity = new Manufacturer;
        }

        $isNew = !$entity->getId();
        $form = $this->createForm(new ManufacturerType(), $entity);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $em->persist($entity);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('manufacturers'));

        } else {

            return $this->render('ShopCatalogBundle:AdminManufacturer:manufacturer.html.twig', array(
                'title' => $isNew ? 'Добавление производителя' : 'Изменение производителя',
                'form' => $form->createView(),
                'manufacturer' => $entity,
            ));

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteManufacturerAction($id)
    {

        $entity = $this->getDoctrine()->getManager()->getRepository('ShopCatalogBundle:Manufacturer')->findOneBy(array(
            'id' => $id
        ));

        if($entity){

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('manufacturers'));

    }

}
