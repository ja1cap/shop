<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\CategoryFilters;
use Shop\CatalogBundle\Entity\CategoryParameter;
use Shop\CatalogBundle\Entity\CategoryParameterGroup;
use Shop\CatalogBundle\Form\Type\CategoryParameterType;
use Shop\CatalogBundle\Entity\CategoryParameterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminCategoryController
 * @package Shop\CatalogBundle\Controller
 */
class AdminCategoryController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function categoriesAction()
    {

        $categories = $this->get('shop_catalog.category.repository')->findBy(array(), array(
            'name' => 'ASC',
        ));

        return $this->render('ShopCatalogBundle:AdminCategory:categories.html.twig', array(
            'categories' => $categories,
        ));

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function categoryAction($id, Request $request)
    {

        $repository = $this->get('shop_catalog.category.repository');
        $category = $repository->findOneBy(array(
            'id' => $id
        ));

        if(!$category instanceof Category){
            $category = new Category;
        }

        $isNew = !$category->getId();
        $form = $this->createForm('shop_catalog_category', $category);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $em->persist($category);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('category', array(
                'id' => $category->getId(),
            )));

        } else {

            return $this->render('ShopCatalogBundle:AdminCategory:category.html.twig', array(
                'title' => $isNew ? 'Добавление категории' : 'Изменение категории',
                'form' => $form->createView(),
                'category' => $category,
            ));

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteCategoryAction($id)
    {

        $entity = $this->getDoctrine()->getManager()->getRepository('ShopCatalogBundle:Category')->findOneBy(array(
            'id' => $id
        ));

        if($entity){

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('categories'));

    }

    /**
     * @param $categoryId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function categoryParametersAction($categoryId){

        $category = $this->get('shop_catalog.category.repository')->findOneBy(array(
            'id' => $categoryId,
        ));

        if(!$category instanceof Category){
            return $this->redirect($this->generateUrl('categories'));
        }

        return $this->render('ShopCatalogBundle:AdminCategory:categoryParameters.html.twig', array(
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
    public function categoryParameterAction($categoryId, $id, Request $request)
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

            return $this->redirect($this->generateUrl('category_parameters', array(
                'categoryId' => $category->getId(),
            )));

        } else {

            return $this->render('ShopCatalogBundle:AdminCategory:categoryParameter.html.twig', array(
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
    public function deleteCategoryParameterAction($id)
    {

        $categoryParameter = $this->getDoctrine()->getManager()->getRepository('ShopCatalogBundle:CategoryParameter')->findOneBy(array(
            'id' => $id
        ));

        if($categoryParameter instanceof CategoryParameter){

            $categoryId = $categoryParameter->getCategoryId();

            $em = $this->getDoctrine()->getManager();
            $em->remove($categoryParameter);
            $em->flush();

            return $this->redirect($this->generateUrl('category', array('id' => $categoryId)));

        }

        return $this->redirect($this->generateUrl('categories'));

    }

    /**
     * @param $categoryId
     * @param Request $request
     * @return JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function updateCategoryParametersAction($categoryId, Request $request)
    {

        $repository = $this->get('shop_catalog.category.repository');
        $category = $repository->findOneBy(array(
            'id' => $categoryId
        ));

        if(!$category instanceof Category){
            throw $this->createNotFoundException('Category not found');
        }

        $parameters = $request->get('parameters');
        if(is_array($parameters)){

            $category->getParameters()->map(function(CategoryParameter $categoryParameter) use ($parameters) {

                if(isset($parameters[$categoryParameter->getId()])){

                    $parameter = $parameters[$categoryParameter->getId()];
                    if(is_array($parameter)){

                        if(isset($parameter['position'])){

                            $categoryParameter->setPosition((int)$parameter['position']);

                        }

                    }

                }

            });

            $em = $this->getDoctrine()->getManager();
            $em->flush();

        }

        return new JsonResponse('OK');

    }

    /**
     * @param $categoryId
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function categoryParameterGroupAction($categoryId, $id, Request $request){

        $category = $this->get('shop_catalog.category.repository')->findOneBy(array(
            'id' => $categoryId,
        ));

        if(!$category instanceof Category){
            return $this->redirect($this->generateUrl('categories'));
        }

        $categoryParameterGroupRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:CategoryParameterGroup');
        $categoryParameterGroup = $categoryParameterGroupRepository->findOneBy(array(
            'id' => $id,
        ));

        if(!$categoryParameterGroup instanceof CategoryParameterGroup){
            $categoryParameterGroup = new CategoryParameterGroup();
        }

        $isNew = !$categoryParameterGroup->getId();

        $form = $this->createForm(new CategoryParameterType($category), $categoryParameterGroup);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $category->addParameterGroup($categoryParameterGroup);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('category_parameters', array(
                'categoryId' => $category->getId(),
            )));

        } else {

            return $this->render('ShopCatalogBundle:AdminCategory:categoryParameterGroup.html.twig', array(
                'title' => $isNew ? 'Добавление группы параметров' : 'Изменение группы параметров',
                'form' => $form->createView(),
                'category' => $category,
                'category_parameter_group' => $categoryParameterGroup,
            ));

        }

    }

    /**
     * @param $categoryId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function categoryFiltersListAction($categoryId){

        $category = $this->get('shop_catalog.category.repository')->findOneBy(array(
            'id' => $categoryId,
        ));

        if(!$category instanceof Category){
            return $this->redirect($this->generateUrl('categories'));
        }

        return $this->render('ShopCatalogBundle:AdminCategory:categoryFiltersList.html.twig', array(
            'category' => $category,
        ));

    }

    /**
     * @param $id
     * @param $categoryId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function categoryFiltersAction($id, $categoryId, Request $request){

        $category = $this->get('shop_catalog.category.repository')->findOneBy(array(
            'id' => $categoryId,
        ));

        if(!$category instanceof Category){
            return $this->redirect($this->generateUrl('categories'));
        }

        $categoryFilters = $this->getDoctrine()->getRepository('ShopCatalogBundle:CategoryFilters')->findOneBy(array(
            'id' => $id,
        ));

        if(!$categoryFilters instanceof CategoryFilters){
            $categoryFilters = new CategoryFilters();
        }

        $isNew = !$categoryFilters->getId();
        $form = $this->createForm('shop_catalog_category_filters', $categoryFilters, array(
            'category' => $category,
        ));

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $category->addFilter($categoryFilters);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('category_filters_list', array(
                'categoryId' => $category->getId(),
            )));

        } else {

            return $this->render('ShopCatalogBundle:AdminCategory:categoryFilters.html.twig', array(
                'title' => $isNew ? 'Добавление фильтров' : 'Изменение фильтров',
                'form' => $form->createView(),
                'category' => $category,
                'categoryFilters' => $categoryFilters,
            ));

        }

    }

}
