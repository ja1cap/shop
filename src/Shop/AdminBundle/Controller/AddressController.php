<?php

namespace Shop\AdminBundle\Controller;

use Shop\MainBundle\Entity\Address;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AddressController
 * @package Shop\AdminBundle\Controller
 */
class AddressController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addressesAction()
    {

        $addresses = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Address')->findAll();

        return $this->render('ShopAdminBundle:Address:addresses.html.twig', array(
            'addresses' => $addresses,
        ));

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addressAction($id, Request $request)
    {

        $address = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Address')->findOneBy(array(
            'id' => $id
        ));

        if(!$address){
            $address = new Address();
        }

        $form = $this->createFormBuilder($address)
            ->add('name', 'textarea', array(
                'required' => true,
                'label' => 'Адрес',
            ))
            ->add('description', 'textarea', array(
                'required' => true,
                'label' => 'Дополнительная информация',
            ))
            ->add('phones', 'textarea', array(
                'required' => false,
                'label' => 'Телефоны',
            ))
            ->add('work_schedule', 'textarea', array(
                'required' => false,
                'label' => 'Время работы',
            ))
            ->add('latitude', 'text', array(
                'required' => true,
                'label' => 'Широта',
            ))
            ->add('longitude', 'text', array(
                'required' => true,
                'label' => 'Долгота',
            ))
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ))
            ->getForm();

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if(!$address->getId()){
                $em->persist($address);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('shop_admin_address', [
                'id' => $address->getId(),
            ]));

        } else {

            return $this->render('ShopAdminBundle:Address:address.html.twig', array(
                'title' => $address->getId() ? 'Изменение адреса' : 'Добавление адреса',
                'form' => $form->createView(),
            ));

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAddressAction($id){

        $item = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Address')->findOneBy(array(
            'id' => $id
        ));

        if($item){

            $em = $this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('shop_admin_contacts'));

    }

}
