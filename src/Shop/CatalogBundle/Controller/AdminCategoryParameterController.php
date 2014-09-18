<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\CategoryParameter;
use Shop\CatalogBundle\Entity\CategoryParameterGroup;
use Shop\CatalogBundle\Form\Type\CategoryParameterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Shop\CatalogBundle\Entity\CategoryParameterRepository;

/**
 * Class AdminCategoryParameterController
 * @package Shop\CatalogBundle\Controller
 */
class AdminCategoryParameterController extends Controller
{

    /**
     * @param $categoryId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function parametersAction($categoryId){

        $category = $this->get('shop_catalog.category.repository')->findOneBy(array(
            'id' => $categoryId,
        ));

        if(!$category instanceof Category){
            return $this->redirect($this->generateUrl('categories'));
        }

        return $this->render('ShopCatalogBundle:AdminCategoryParameter:parameters.html.twig', array(
            'category' => $category,
            'total_parameters_amount' => count($this->getDoctrine()->getRepository('ShopCatalogBundle:Parameter')->findAll()),
        ));

    }

    /**
     * @param $categoryId
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function parameterAction($categoryId, $id, Request $request)
    {

        $category = $this->get('shop_catalog.category.repository')->findOneBy(array(
            'id' => $categoryId,
        ));

        if(!$category instanceof Category){
            return $this->redirect($this->generateUrl('categories'));
        }

        /**
         * @var $categoryParameterRepository CategoryParameterRepository
         */
        $categoryParameterRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:CategoryParameter');
        $categoryParameter = $categoryParameterRepository->findOneBy(array(
            'id' => $id,
        ));

        if(!$categoryParameter instanceof CategoryParameter){
            $categoryParameter = new CategoryParameter();
        }

        $isNew = !$categoryParameter->getId();

        $availableParameters = $categoryParameterRepository->findCategoryUnusedParameters($category->getId());
        if(!$isNew){
            array_unshift($availableParameters, $categoryParameter->getParameter());
        }

        $form = $this->createForm(new CategoryParameterType($category, $availableParameters), $categoryParameter);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $category->addParameter($categoryParameter);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('category_parameter', array(
                'categoryId' => $category->getId(),
                'id' => $categoryParameter->getId(),
            )));

        } else {

            return $this->render('ShopCatalogBundle:AdminCategoryParameter:parameter.html.twig', array(
                'title' => $isNew ? 'Добавление параметра категории' : 'Изменение параметра категории',
                'form' => $form->createView(),
                'category' => $category,
                'category_parameter' => $categoryParameter,
            ));

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteParameterAction($id)
    {

        $categoryParameter = $this->getDoctrine()->getManager()->getRepository('ShopCatalogBundle:CategoryParameter')->findOneBy(array(
            'id' => $id
        ));

        if($categoryParameter instanceof CategoryParameter){

            $categoryId = $categoryParameter->getCategoryId();

            $em = $this->getDoctrine()->getManager();
            $em->remove($categoryParameter);
            $em->flush();

            return $this->redirect($this->generateUrl('category_parameters', array('categoryId' => $categoryId)));

        }

        return $this->redirect($this->generateUrl('categories'));

    }

    /**
     * @param $categoryId
     * @param Request $request
     * @return JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function updateParametersAction($categoryId, Request $request)
    {

        $repository = $this->get('shop_catalog.category.repository');
        $category = $repository->findOneBy(array(
            'id' => $categoryId
        ));

        if(!$category instanceof Category){
            throw $this->createNotFoundException('Category not found');
        }

        $categoryParameterGroupRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:CategoryParameterGroup');

        $parameters = $request->get('parameters');
        if(is_array($parameters)){

            $category->getParameters()->map(function(CategoryParameter $categoryParameter) use ($parameters, $categoryParameterGroupRepository) {

                if(isset($parameters[$categoryParameter->getId()])){

                    $parameter = $parameters[$categoryParameter->getId()];
                    if(is_array($parameter)){

                        if(isset($parameter['position'])){

                            $categoryParameter->setPosition((int)$parameter['position']);

                        }

                        if(isset($parameter['group_id'])){

                            /**
                             * @var $categoryParameterGroup \Shop\CatalogBundle\Entity\CategoryParameterGroup|null
                             */
                            if($parameter['group_id']){

                                $categoryParameterGroup = $categoryParameterGroupRepository->findOneBy(array(
                                    'id' => $parameter['group_id'],
                                ));

                            } else {

                                $categoryParameterGroup = null;

                            }

                            $categoryParameter->setGroup($categoryParameterGroup);

                        }

                    }

                }

            });

            $em = $this->getDoctrine()->getManager();
            $em->flush();

        }
        $parameterGroups = $request->get('parameter_groups');
        if(is_array($parameterGroups)){

            $category->getParameterGroups()->map(function(CategoryParameterGroup $categoryParameterGroup) use ($parameterGroups) {

                if(isset($parameterGroups[$categoryParameterGroup->getId()])){

                    $parameterGroup = $parameterGroups[$categoryParameterGroup->getId()];
                    if(is_array($parameterGroup)){

                        if(isset($parameterGroup['position'])){

                            $categoryParameterGroup->setPosition((int)$parameterGroup['position']);

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
