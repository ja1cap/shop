<?php

namespace Shop\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ContactsController
 * @package Shop\AdminBundle\Controller
 */
class ContactsController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request){

        /**
         * @var $settings \Shop\MainBundle\Entity\Settings
         */
        $settings = $this->get('shop_main.settings.resource')->getSettings();

        $form = $this->createFormBuilder($settings)
            ->add('contacts_title', 'textarea', array(
                'required' => true,
                'label' => 'Заголовок блока',
            ))
            ->add('email', 'textarea', array(
                'required' => false,
                'label' => 'Email',
            ))
            ->add('address', 'textarea', array(
                'required' => true,
                'label' => 'Основной адрес',
            ))
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ))
            ->getForm();

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if(!$settings->getId()){
                $em->persist($settings);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('shop_admin'));

        } else {

            return $this->render('ShopAdminBundle:Contacts:index.html.twig', array(
                'form' => $form->createView(),
            ));

        }

    }

}
