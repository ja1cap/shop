<?php

namespace Shop\UserBundle\Controller;

use Shop\UserBundle\Entity\AbstractUser;
use Shop\UserBundle\Entity\UserGroup;
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
     * @param $group_id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function usersAction($group_id, Request $request)
    {

        $groupRepository = $this->getDoctrine()->getRepository('ShopUserBundle:UserGroup');

        $groups = $groupRepository->findBy(
            array(),
            array(
                'name' => 'ASC'
            )
        );

        $currentGroup = $group_id ? $groupRepository->findOneBy(array('id' => $group_id)) : null;

        if($currentGroup instanceof UserGroup){

            $groupRoute = 'admin_' . $currentGroup->getRoutePrefix();

            if($request->get('_route') != $groupRoute){
                $this->redirect($this->generateUrl($groupRoute, array('group_id' => $currentGroup->getId())));
            }

            $users = $currentGroup->getUsers()->toArray();

        } else {

            $users = $this->getDoctrine()->getRepository('ShopUserBundle:User')->findBy(
                array(),
                array(
                    'username' => 'ASC'
                )
            );

        }

        $templateName = 'ShopUserBundle:AdminUser:users.html.twig';
        $usersContainerTemplateName = 'ShopUserBundle:AdminUser:usersContainer.html.twig';

        if($request->get('ajax_tab')){
            $templateName = $usersContainerTemplateName;
        }

        return $this->render($templateName, array(
            'users' => $users,
            'groups' => $groups,
            'currentGroup' => $currentGroup,
            'usersContainerTemplateName' => $usersContainerTemplateName,
        ));

    }

    /**
     * @param $group_id
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function userAction($group_id, $id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $groupRoute = 'admin_users';
        $groupUserClassName = '\Shop\UserBundle\Entity\User';

        $groupRepository = $this->getDoctrine()->getRepository('ShopUserBundle:UserGroup');
        $currentGroup = $group_id ? $groupRepository->findOneBy(array('id' => $group_id)) : null;

        if($currentGroup instanceof UserGroup){

            $groupRoute = 'admin_' . $currentGroup->getRoutePrefix();
            $groupUserRoute =  $groupRoute . '_user';
            $groupUserClassName = $currentGroup->getUserClassName();

            if($request->get('_route') != $groupUserRoute){
                $this->redirect($this->generateUrl($groupUserRoute, array('group_id' => $currentGroup->getId())));
            }

        }

        $usersRepository = $em->getRepository($groupUserClassName);

        /**
         * @var $userManager \FOS\UserBundle\Doctrine\UserManager
         */
        $userManager = $this->get('fos_user.user_manager');

        $user = $id ? $usersRepository->findOneBy(array('id' => $id)) : null;

        if(!$id && !$user instanceof AbstractUser){

            /**
             * @var $user AbstractUser
             */
            $user = new $groupUserClassName;
            $user->setEnabled(true);

            $user->addGroup($currentGroup);

        }

        $form = $this->createForm(new AdminUserType(), $user);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $userManager->updateUser($user);
            return $this->redirect($this->generateUrl($groupRoute, array('group_id' => $group_id)));

        }

        return $this->render(
            'ShopUserBundle:AdminUser:user.html.twig',
            array(
                'title' => $user->getId() ? 'Изменение пользователя' : 'Добавление пользователя',
                'form' => $form->createView(),
                'user' => $user,
                'currentGroup' => $currentGroup,
            )
        );

    }

    /**
     * @param $group_id
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUserAction($group_id, $id, Request $request)
    {

        $groupRoute = 'admin_users';
        $groupUserClassName = '\Shop\UserBundle\Entity\User';

        $groupRepository = $this->getDoctrine()->getRepository('ShopUserBundle:UserGroup');
        $currentGroup = $group_id ? $groupRepository->findOneBy(array('id' => $group_id)) : null;

        if($currentGroup instanceof UserGroup){

            $groupRoute = 'admin_' . $currentGroup->getRoutePrefix();
            $groupUserClassName = $currentGroup->getUserClassName();

            if($request->get('_route') != $groupRoute){
                $this->redirect($this->generateUrl($groupRoute, array('group_id' => $currentGroup->getId())));
            }

        }

        $entity = $this->getDoctrine()->getManager()->getRepository($groupUserClassName)->findOneBy(array(
            'id' => $id
        ));

        if($entity){

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

        }

        return $this->redirect($this->generateUrl($groupRoute, array('group_id' => $group_id)));

    }

}
