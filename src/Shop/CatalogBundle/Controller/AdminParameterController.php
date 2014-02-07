<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Entity\Parameter;
use Shop\CatalogBundle\Entity\ParameterOption;
use Shop\CatalogBundle\Form\Type\ParameterOptionType;
use Shop\CatalogBundle\Form\Type\ParameterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminParameterController
 * @package Shop\CatalogBundle\Controller
 */
class AdminParameterController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function parametersAction()
    {

        $parameters = $this->getDoctrine()->getRepository('ShopCatalogBundle:Parameter')->findBy(array(), array(
            'name' => 'DESC',
        ));

        return $this->render('ShopCatalogBundle:AdminParameter:parameters.html.twig', array(
            'parameters' => $parameters,
        ));

    }

    /**
     * @param null $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function parameterAction($id = null, Request $request)
    {

        $repository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Parameter');
        $entity = $repository->findOneBy(array(
            'id' => $id
        ));

        if(!$entity instanceof Parameter){
            $entity = new Parameter;
        }

        $isNew = !$entity->getId();
        $form = $this->createForm(new ParameterType(), $entity);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $entity->setType(Parameter::TYPE_SELECT);
                $em->persist($entity);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('parameters'));

        } else {

            return $this->render('ShopCatalogBundle:AdminParameter:parameter.html.twig', array(
                'title' => $isNew ? 'Добавление параметра' : 'Изменение параметра',
                'form' => $form->createView(),
                'parameter' => $entity,
            ));

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteParameterAction($id)
    {

        $entity = $this->getDoctrine()->getManager()->getRepository('ShopCatalogBundle:Parameter')->findOneBy(array(
            'id' => $id
        ));

        if($entity){

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('parameters'));

    }

    public function parameterOptionAction($parameterId, $id = null, Request $request)
    {

        $parameterRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Parameter');
        $parameter = $parameterRepository->findOneBy(array(
            'id' => $parameterId
        ));

        if(!$parameter instanceof Parameter){
            return $this->redirect($this->generateUrl('parameters'));
        }

        $parameterOption = $this->getDoctrine()->getRepository('ShopCatalogBundle:ParameterOption')->findOneBy(array(
            'id' => $id
        ));

        if(!$parameterOption instanceof ParameterOption){
            $parameterOption = new ParameterOption();
        }

        $isNew = !$parameterOption->getId();
        $form = $this->createForm(new ParameterOptionType(), $parameterOption);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $parameter->addOption($parameterOption);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('parameter', array('id' => $parameterId)));

        } else {

            return $this->render('ShopCatalogBundle:AdminParameter:parameterOption.html.twig', array(
                'title' => $isNew ? 'Добавление опции' : 'Изменение опции',
                'form' => $form->createView(),
                'parameter' => $parameter,
            ));

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteParameterOptionAction($id)
    {

        $entity = $this->getDoctrine()->getManager()->getRepository('ShopCatalogBundle:ParameterOption')->findOneBy(array(
            'id' => $id
        ));

        if($entity){

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);

            //@TODO update position of options that has current position greater then position of deleted option

            $em->flush();

        }

        return $this->redirect($this->generateUrl('parameters'));

    }

    /**
     * @param $parameterId
     * @param Request $request
     * @return JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function updateParameterOptionsAction($parameterId, Request $request){

        $parameterRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Parameter');
        $parameter = $parameterRepository->findOneBy(array(
            'id' => $parameterId
        ));

        if(!$parameter instanceof Parameter){
            throw $this->createNotFoundException('Parameter not found');
        }

        $options = $request->get('options');
        if(is_array($options)){

            $parameter->getOptions()->map(function(ParameterOption $parameterOption) use ($options) {

                if(isset($options[$parameterOption->getId()])){

                    $option = $options[$parameterOption->getId()];
                    if(is_array($option)){

                        if(isset($option['position'])){

                            $parameterOption->setPosition((int)$option['position']);

                        }

                    }

                }

            });

            $em = $this->getDoctrine()->getManager();
            $em->flush();

        }

        return new JsonResponse('OK');

    }

}
