<?php

namespace Shop\AdminBundle\Controller;

use Shop\MainBundle\Entity\Phone;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PhoneController
 * @package Shop\AdminBundle\Controller
 */
class PhoneController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function phonesAction()
    {

        $phones = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Phone')->findAll();

        return $this->render('ShopAdminBundle:Phone:phones.html.twig', array(
            'phones' => $phones,
        ));

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function phoneAction($id, Request $request)
    {

        $phone = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Phone')->findOneBy(array(
            'id' => $id
        ));

        if(!$phone){
            $phone = new Phone();
        }

        $form = $this->createForm('weasty_admin_phone_type', $phone);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if(!$phone->getId()){
                $em->persist($phone);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('shop_admin_phones'));

        } else {

            return $this->render('ShopAdminBundle:Phone:phone.html.twig', array(
                'title' => $phone->getId() ? 'Изменение телефона' : 'Добавление телефона',
                'form' => $form->createView(),
            ));

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePhoneAction($id){

        $item = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Phone')->findOneBy(array(
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
