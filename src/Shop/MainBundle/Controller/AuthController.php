<?php

namespace Shop\MainBundle\Controller;

use Shop\MainBundle\Form\Type\RegistrationType;
use Shop\UserBundle\Model\UserModel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class AuthController
 * @package Shop\MainBundle\Controller
 */
class AuthController extends Controller
{
    public function loginAction(Request $request)
    {

        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'ShopMainBundle:Auth:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
            )
        );

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(){

        /**
         * @var $factory \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
         */
        $factory = $this->get('security.encoder_factory');
        $model = new UserModel($factory);

        $form = $this->createForm(new RegistrationType(), $model, array(
            'action' => $this->generateUrl('account_create'),
        ));

        return $this->render(
            'ShopMainBundle:Auth:register.html.twig',
            array('form' => $form->createView())
        );

    }

    /**
     * @param Request $request
     * @return string|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        /**
         * @var $factory \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
         */
        $factory = $this->get('security.encoder_factory');
        $model = new UserModel($factory);

        $form = $this->createForm(new RegistrationType(), $model);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $model = $form->getData();

            $em->persist($model->getUser());
            $em->flush();

            return $this->redirect($this->generateUrl('login'));

        }

        return $this->render(
            'ShopMainBundle:Auth:register.html.twig',
            array('form' => $form->createView())
        );

    }

    public function loginCheckAction(){}

    public function logoutAction(){}

}
