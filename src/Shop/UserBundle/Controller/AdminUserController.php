<?php

namespace Shop\UserBundle\Controller;

use Shop\UserBundle\Entity\User;
use Shop\UserBundle\Form\Type\AdminUserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminUserController
 * @package Shop\UserBundle\Controller
 */
class AdminUserController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function usersAction()
    {

        $users = $this->getDoctrine()->getRepository('ShopUserBundle:User')->findBy(
            array(),
            array(
                'username' => 'ASC'
            )
        );

        return $this->render('ShopUserBundle:AdminUser:users.html.twig', array(
            'users' => $users
        ));

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function userAction($id, Request $request)
    {
        /**
         * @var $userManager \FOS\UserBundle\Doctrine\UserManager
         */
        $userManager = $this->get('fos_user.user_manager');
        $em = $this->getDoctrine()->getManager();

        $user = $id ? $em->getRepository('ShopUserBundle:User')->findOneBy(array('id' => $id)) : null;

        if(!$user instanceof User){
            $user = $userManager->createUser();
            $user->setEnabled(true);
        }

        $form = $this->createForm(new AdminUserType(), $user);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $userManager->updateUser($user);

            return $this->redirect($this->generateUrl('admin_users'));

        }

        return $this->render(
            'ShopUserBundle:AdminUser:user.html.twig',
            array(
                'title' => $user->getId() ? 'Изменение пользователя' : 'Добавление пользователя',
                'form' => $form->createView()
            )
        );

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUserAction($id)
    {

        $entity = $this->getDoctrine()->getManager()->getRepository('ShopUserBundle:User')->findOneBy(array(
            'id' => $id
        ));

        if($entity){

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('admin_users'));

    }

}
