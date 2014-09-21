<?php

namespace Shop\DiscountBundle\Controller;

use Shop\DiscountBundle\Action\ActionInterface;
use Shop\DiscountBundle\Entity\ActionCategory;
use Shop\DiscountBundle\Form\Type\ActionCategoryType;
use Shop\DiscountBundle\Mapper\ActionConditionMapper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminActionCategoryController extends Controller
{

    /**
     * @param $actionId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function categoriesAction($actionId)
    {

        $action = $this->getAction($actionId);

        return $this->render('ShopDiscountBundle:AdminActionCategory:categories.html.twig', [
            'action' => $action,
        ]);

    }

    /**
     * @param $actionId
     * @param null $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function categoryAction($actionId, $id = null, Request $request)
    {

        $action = $this->getAction($actionId);

        $actionConditionRepository = $this->getDoctrine()->getRepository('ShopDiscountBundle:ActionCategory');
        $actionCategory = $actionConditionRepository->findOneBy(array(
            'actionId' => $action->getId(),
            'id' => $id,
        ));

        if(!$actionCategory instanceof ActionCategory){
            $actionCategory = new ActionCategory();
        }

        $em = $this->getDoctrine()->getManager();
        $mapper = new ActionConditionMapper($actionCategory);
        $mapper->setEntityManager($em);

        $form = $this->createForm(new ActionCategoryType(), $mapper);
        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            if(!$actionCategory->getId()){

                $action->addCondition($actionCategory);

            }

            $em->flush();

            return $this->redirect($this->generateUrl('shop_discount_admin_action_categories', [
                'actionId' => $action->getId(),
            ]));

        } else {

            return $this->render('ShopDiscountBundle:AdminActionCategory:category.html.twig', [
                'title' => $actionCategory->getId() ? 'Изменение условия' : 'Добавление условия',
                'form' => $form->createView(),
                'action' => $action,
                'actionCondition' => $actionCategory,
            ]);

        }

    }

    /**
     * @param $actionId
     * @return ActionInterface
     */
    protected function getAction($actionId)
    {
        $actionRepository = $this->get('shop_discount.action.repository');
        $action = $actionRepository->findOneBy(array(
            'id' => $actionId
        ));

        if (!$action instanceof ActionInterface) {
            throw $this->createNotFoundException('Action not found');
        }
        return $action;
    }

}
