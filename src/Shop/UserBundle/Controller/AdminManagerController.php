<?php

namespace Shop\UserBundle\Controller;

use Shop\UserBundle\Entity\Manager;
use Shop\UserBundle\Entity\ManagerContractor;
use Shop\UserBundle\Entity\UserGroup;
use Shop\UserBundle\Form\Type\ManagerContractorType;
use Shop\UserBundle\Mapper\ManagerContractorMapper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminManagerController
 * @package Shop\UserBundle\Controller
 */
class AdminManagerController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function managersAction(Request $request)
    {

        $groupRepository = $this->getDoctrine()->getRepository('ShopUserBundle:UserGroup');
        $groups = $groupRepository->findBy(
            array(),
            array(
                'name' => 'ASC'
            )
        );

        $managersGroup = $this->getManagersGroup();

        $users = $managersGroup->getUsers()->toArray();

        $templateName = 'ShopUserBundle:AdminManager:managers.html.twig';
        $usersContainerTemplateName = 'ShopUserBundle:AdminManager:managersContainer.html.twig';

        if($request->get('ajax_tab')){
            $templateName = $usersContainerTemplateName;
        }

        return $this->render($templateName, array(
            'users' => $users,
            'groups' => $groups,
            'currentGroup' => $managersGroup,
            'usersContainerTemplateName' => $usersContainerTemplateName,
        ));

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function managerContractorsAction($id){

        $em = $this->getDoctrine()->getManager();

        $managersGroup = $this->getManagersGroup();

        $manager = $em->getRepository($managersGroup->getUserClassName())->findOneBy(array(
            'id' => $id,
        ));

        if(!$manager instanceof Manager){
            throw $this->createNotFoundException('Manager not found');
        }

        return $this->render('ShopUserBundle:AdminManager:managerContractors.html.twig', array(
            'currentGroup' => $managersGroup,
            'manager' => $manager,
        ));

    }

    /**
     * @param $id
     * @param $manager_id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function managerContractorAction($id, $manager_id, Request $request){

        $em = $this->getDoctrine()->getManager();

        $managersGroup = $this->getManagersGroup();

        $manager = $em->getRepository($managersGroup->getUserClassName())->findOneBy(array(
            'id' => $manager_id,
        ));

        if(!$manager instanceof Manager){
            throw $this->createNotFoundException('Manager not found');
        }

        $managerContractor = $id ? $em->getRepository('ShopUserBundle:ManagerContractor')->findOneBy(array(
            'id' => $id,
            'managerId' => $manager->getId()
        )) : null;;

        if(!$managerContractor instanceof ManagerContractor){
            $managerContractor = new ManagerContractor();
        }

        $categoryRepository = $em->getRepository('ShopCatalogBundle:Category');
        $contractorRepository = $em->getRepository('ShopCatalogBundle:Contractor');

        $form = $this->createForm(
            new ManagerContractorType($categoryRepository, $contractorRepository),
            new ManagerContractorMapper($managerContractor, $categoryRepository, $contractorRepository)
        );

        $form->handleRequest($request);

        if ($form->isValid()) {

            if(!$managerContractor->getManagerId()){
                $manager->addContractor($managerContractor);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('admin_manager_contractors', array('id' => $manager->getId())));

        }

        return $this->render('ShopUserBundle:AdminManager:managerContractor.html.twig', array(
            'currentGroup' => $managersGroup,
            'manager' => $manager,
            'managerContractor' => $managerContractor,
            'form' => $form->createView(),
        ));

    }

    /**
     * @param $manager_id
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function managerDeleteContractorAction($manager_id, $id){

        $em = $this->getDoctrine()->getManager();

        $managersGroup = $this->getManagersGroup();

        $manager = $em->getRepository($managersGroup->getUserClassName())->findOneBy(array(
            'id' => $manager_id,
        ));

        if(!$manager instanceof Manager){
            throw $this->createNotFoundException('Manager not found');
        }

        $managerContractor = $id ? $em->getRepository('ShopUserBundle:ManagerContractor')->findOneBy(array(
            'id' => $id,
            'managerId' => $manager->getId()
        )) : null;;

        if(!$managerContractor instanceof ManagerContractor){
            throw $this->createNotFoundException('Manager contractor not found');
        }

        $manager->removeContractor($managerContractor);
        $em->remove($managerContractor);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_manager_contractors', array('id' => $manager->getId())));

    }

    /**
     * @return UserGroup
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getManagersGroup(){

        $groupRepository = $this->getDoctrine()->getRepository('ShopUserBundle:UserGroup');

        $managersGroup = $groupRepository->findOneBy(array(
            'slug' => UserGroup::SLUG_MANAGERS,
        ));

        if(!$managersGroup instanceof UserGroup){
            throw $this->createNotFoundException('Managers group not found');
        }

        return $managersGroup;

    }

}
