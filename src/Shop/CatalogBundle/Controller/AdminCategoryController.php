<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\CategoryParameterGroup;
use Shop\CatalogBundle\Form\Type\CategoryParameterGroupType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

        return $this->redirect($this->generateUrl('admin_catalog_categories'));

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
            return $this->redirect($this->generateUrl('admin_catalog_categories'));
        }

        $categoryParameterGroupRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:CategoryParameterGroup');
        $categoryParameterGroup = $categoryParameterGroupRepository->findOneBy(array(
            'id' => $id,
        ));

        if(!$categoryParameterGroup instanceof CategoryParameterGroup){
            $categoryParameterGroup = new CategoryParameterGroup();
        }

        $isNew = !$categoryParameterGroup->getId();

        $form = $this->createForm(new CategoryParameterGroupType($category), $categoryParameterGroup);

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
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteCategoryParameterGroupAction($id)
    {

        $categoryParameterGroup = $this->getDoctrine()->getManager()->getRepository('ShopCatalogBundle:CategoryParameterGroup')->findOneBy(array(
            'id' => $id
        ));

        if($categoryParameterGroup instanceof CategoryParameterGroup){

            $categoryId = $categoryParameterGroup->getCategoryId();

            $em = $this->getDoctrine()->getManager();
            $em->remove($categoryParameterGroup);
            $em->flush();

            return $this->redirect($this->generateUrl('category_parameters', array('categoryId' => $categoryId)));

        }

        return $this->redirect($this->generateUrl('admin_catalog_categories'));

    }

}
