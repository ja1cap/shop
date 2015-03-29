<?php

namespace Weasty\Bundle\AdminBundle\Controller;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Weasty\Doctrine\Entity\EntityInterface;
use Weasty\Doctrine\Mapper\AbstractEntityMapper;

/**
 * Class DefaultController
 * @package Weasty\Bundle\AdminBundle\Controller
 */
class DefaultController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws ORMException
     * @throws \Exception
     */
    public function indexAction(Request $request)
    {

        $repository = $this->getRepository($request);
        $repositoryRetrieveMethod = $request->get('_repository_retrieve_method');

        if($repositoryRetrieveMethod){

            if(!method_exists($repository, $repositoryRetrieveMethod)){
                throw new \Exception(sprintf('%s repository does not have %s method', $repository->getClassName(), $repositoryRetrieveMethod));
            }

            $entities = call_user_func(array($repository, $repositoryRetrieveMethod));

        } else {

            $criteria = json_decode($request->get('_repository_retrieve_criteria'), true)?:[];
            $orderBy = json_decode($request->get('_repository_retrieve_order'), true)?:[];
            $entities = $repository->findBy($criteria, $orderBy);

        }

        $view = $request->get('_view', 'WeastyAdminBundle:Default:index.html.twig');
        $viewParameters = [
            'entities' => $entities,
        ];

        return $this->render($view, $viewParameters);

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function addAction(Request $request){

        $repository = $this->getRepository($request);
        $className = $repository->getClassName();
        $entity = new $className;

        if(!$entity instanceof EntityInterface){
            throw new ORMException(sprintf('Entity mast be instance of \Weasty\Doctrine\Entity\EntityInterface'), 500);
        }

        $mapper = $this->createEntityFormMapper($request, $entity);
        $form = $this->createEntityForm($request, $mapper);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $redirectRoute = $request->get('_redirect_route', $request->get('_route'));
            return $this->redirect($this->generateUrl($redirectRoute, ['id' => $entity->getId()]));

        } else {

            $view = $request->get('_view', 'WeastyAdminBundle:Default:add.html.twig');
            $viewParameters = [
                'title' => 'Добавление',
                'form' => $form->createView(),
                'entity' => $entity,
            ];

            return $this->render($view, $viewParameters);

        }

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     */
    public function editAction($id, Request $request){

        $repository = $this->getRepository($request);

        //@TODO add $criteria
        $criteria = [
            'id' => $id,
        ];
        $entity = $repository->findOneBy($criteria);

        if(!$entity){
            throw $this->createNotFoundException('Entity not found');
        }

        if(!$entity instanceof EntityInterface){
            throw new ORMException(sprintf('Entity mast be instance of \Weasty\Doctrine\Entity\EntityInterface'), 500);
        }

        $mapper = $this->createEntityFormMapper($request, $entity);
        $form = $this->createEntityForm($request, $mapper);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $redirectRoute = $request->get('_redirect_route', $request->get('_route'));
            return $this->redirect($this->generateUrl($redirectRoute, ['id' => $entity->getId()]));

        } else {

            $view = $request->get('_view', 'WeastyAdminBundle:Default:edit.html.twig');
            $viewParameters = [
                'title' => 'Изменение',
                'form' => $form->createView(),
                'entity' => $entity,
            ];

            return $this->render($view, $viewParameters);

        }

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\ORMException
     */
    public function deleteAction($id, Request $request)
    {

        $repository = $this->getRepository($request);

        //@TODO add $criteria
        $criteria = [
            'id' => $id,
        ];
        $entity = $repository->findOneBy($criteria);

        if(!$entity){
            throw $this->createNotFoundException('Entity not found');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        $redirectRoute = $request->get('_redirect_route');
        return $this->redirect($this->generateUrl($redirectRoute));

    }

    /**
     * @param Request $request
     * @param $data
     * @return \Symfony\Component\Form\Form
     */
    protected function createEntityForm(Request $request, $data){

        $formType = $request->get('_form_type');
        return $this->createForm($formType, $data);

    }

    /**
     * @param Request $request
     * @param $entity
     * @return AbstractEntityMapper|EntityInterface
     * @throws ORMException
     */
    protected function createEntityFormMapper(Request $request, $entity){
        if($request->get('_form_mapper_service')){
            $mapperServiceKey = $request->get('_form_mapper_service');
            $mapper = $this->get($mapperServiceKey);
            if(!$mapper instanceof AbstractEntityMapper){
                throw new ORMException(sprintf("Service is not valid entity mapper %s", $mapperServiceKey), 500);
            }
            $mapper->setEntity($entity);
            return $mapper;
        } else {
            return $entity;
        }
    }

    /**
     * @param Request $request
     * @return \Doctrine\Common\Persistence\ObjectRepository
     * @throws \Doctrine\ORM\ORMException
     */
    protected function getRepository(Request $request){

        $repositoryServiceKey = $request->get('_repository_service');

        $repository = $this->get($repositoryServiceKey);
        if(!$repository instanceof ObjectRepository){
            throw new ORMException(sprintf("Service is not valid entity repository %s", $repositoryServiceKey), 500);
        }

        return $repository;

    }

}
