<?php

namespace Shop\DiscountBundle\Controller;

use Shop\DiscountBundle\Entity\ActionConditionInterface;
use Shop\DiscountBundle\Entity\ActionInterface;
use Shop\DiscountBundle\Form\Type\ActionConditionType;
use Shop\DiscountBundle\Form\Type\ActionType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class AdminActionController
 * @package Shop\Discount\Controller
 */
class AdminActionController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function actionsAction(Request $request){

        /**
         * @var $settings \Shop\MainBundle\Entity\Settings
         */
        $settings = $this->get('shop_main.settings.resource')->getSettings();

        $form = $this->createFormBuilder($settings)
            ->add('actions_title', 'textarea', array(
                'required' => true,
                'label' => 'Заголовок блока',
            ))
            ->add('actions_description', 'textarea', array(
                'required' => false,
                'label' => 'Описание блока',
            ))
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ))
            ->getForm();

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if(!$settings->getId()){
                $em->persist($settings);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('shop_discount_admin_actions'));

        } else {

            $actions = $this->get('shop_discount.action.repository')->findBy(array(), array(
                'position' => 'ASC'
            ));

            return $this->render('ShopDiscountBundle:AdminAction:actions.html.twig', array(
                'form' => $form->createView(),
                'actions' => $actions,
            ));

        }

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function actionAction($id, Request $request)
    {

        $actionRepository = $this->get('shop_discount.action.repository');
        $action = $actionRepository->findOneBy(array(
            'id' => $id
        ));

        if(!$action instanceof ActionInterface){
            $action = $actionRepository->create();
        }

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new ActionType(), $action);
        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            if(!$action->getId()){
                $action->setPosition(count($actionRepository->findAll()) + 1);
                $em->persist($action);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('shop_discount_admin_action', ['id' => $action->getId()]));

        } else {

            return $this->render('ShopDiscountBundle:AdminAction:action.html.twig', [
                'title' => $action->getId() ? 'Изменение акции' : 'Добавление акции',
                'form' => $form->createView(),
                'action' => $action,
            ]);

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteActionAction($id){

        $item = $this->get('shop_discount.action.repository')->findOneBy(array(
            'id' => $id
        ));

        if($item){

            $em = $this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('shop_discount_admin_actions'));

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateActionsPositionAction(Request $request){

        $actionRepository = $this->get('shop_discount.action.repository');

        $actionsData = $request->get('actions');
        if(is_array($actionsData)){

            array_map(function(ActionInterface $action) use ($actionsData) {

                if(isset($actionsData[$action->getId()])){

                    $actionData = $actionsData[$action->getId()];
                    if(is_array($actionData)){

                        if(isset($actionData['position'])){

                            $action->setPosition((int)$actionData['position']);

                        }

                    }

                }

            }, $actionRepository->findAll());

            $em = $this->getDoctrine()->getManager();
            $em->flush();

        }

        return new JsonResponse('OK');

    }

    /**
     * @param $actionId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function actionConditionsAction($actionId)
    {

        $actionRepository = $this->get('shop_discount.action.repository');
        $action = $actionRepository->findOneBy(array(
            'id' => $actionId
        ));

        if(!$action){
            throw $this->createNotFoundException('Action not found');
        }

        return $this->render('ShopDiscountBundle:AdminAction:actionConditions.html.twig', [
            'action' => $action,
        ]);

    }

    /**
     * @param $actionId
     * @param null $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function actionConditionAction($actionId, $id = null, Request $request)
    {

        $actionRepository = $this->get('shop_discount.action.repository');
        $action = $actionRepository->findOneBy(array(
            'id' => $actionId
        ));

        if(!$action instanceof ActionInterface){
            throw $this->createNotFoundException('Action not found');
        }

        $actionConditionRepository = $this->get('shop_discount.action_condition.repository');
        $actionCondition = $actionConditionRepository->findOneBy(array(
            'id' => $id
        ));

        if(!$actionCondition instanceof ActionConditionInterface){
            $actionCondition = $actionConditionRepository->create();
        }

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new ActionConditionType(), $actionCondition);
        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            if(!$actionCondition->getId()){
                $action->addCondition($actionCondition);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('shop_discount_admin_action_condition', [
                'actionId' => $action->getId(),
                'id' => $actionCondition->getId(),
            ]));

        } else {

            return $this->render('ShopDiscountBundle:AdminAction:actionCondition.html.twig', [
                'title' => $actionCondition->getId() ? 'Изменение условия' : 'Добавление условия',
                'form' => $form->createView(),
                'action' => $action,
                'actionCondition' => $actionCondition,
            ]);

        }

    }

    /**
     * @param $actionId
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteActionConditionAction($actionId, $id){

        $actionRepository = $this->get('shop_discount.action.repository');
        $action = $actionRepository->findOneBy(array(
            'id' => $actionId
        ));

        if(!$action instanceof ActionInterface){
            return $this->redirect($this->generateUrl('shop_discount_admin_actions'));
        }

        $actionConditionRepository = $this->get('shop_discount.action_condition.repository');
        $actionCondition = $actionConditionRepository->findOneBy(array(
            'id' => $id
        ));

        if($actionCondition){

            $em = $this->getDoctrine()->getManager();
            $em->remove($actionCondition);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('shop_discount_admin_action_conditions', ['actionId'=>$action->getId()]));

    }

}
