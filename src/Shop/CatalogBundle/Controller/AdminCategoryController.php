<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\CategoryParameter;
use Shop\CatalogBundle\Form\Type\CategoryParameterType;
use Shop\CatalogBundle\Form\Type\CategoryType;
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

        $categories = $this->getDoctrine()->getRepository('ShopCatalogBundle:Category')->findBy(array(), array(
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

        $repository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Category');
        $entity = $repository->findOneBy(array(
            'id' => $id
        ));

        if(!$entity instanceof Category){
            $entity = new Category;
        }

        $isNew = !$entity->getId();
        $form = $this->createForm(new CategoryType(), $entity);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $em->persist($entity);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('categories'));

        } else {

            return $this->render('ShopCatalogBundle:AdminCategory:category.html.twig', array(
                'title' => $isNew ? 'Добавление категории' : 'Изменение категории',
                'form' => $form->createView(),
                'category' => $entity,
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

        $category = $this->getDoctrine()->getRepository('ShopCatalogBundle:Category')->findOneBy(array(
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

        $category = $this->getDoctrine()->getRepository('ShopCatalogBundle:Category')->findOneBy(array(
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

        $repository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Category');
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

}
