<?php

namespace Shop\DiscountBundle\Controller;

use Shop\DiscountBundle\Action\ActionInterface;
use Shop\DiscountBundle\Entity\Action;
use Shop\DiscountBundle\Entity\BasicActionCondition;
use Shop\DiscountBundle\Form\Type\ActionType;
use Shop\DiscountBundle\Form\Type\BasicActionConditionType;
use Shop\DiscountBundle\Mapper\ActionConditionMapper;
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function actionsAction(){

        $actions = $this->get('shop_discount.action.repository')->findBy(array(), array(
            'position' => 'ASC'
        ));

        return $this->render('ShopDiscountBundle:AdminAction:actions.html.twig', array(
            'actions' => $actions,
        ));

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

        if(!$action instanceof Action){
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
                'form' => $form->createView(),
                'action' => $action,
            ]);

        }

    }

    /**
     * @param $actionId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function actionBasicConditionAction($actionId, Request $request){

        $action = $this->getAction($actionId);

        $basicActionCondition = $action->getBasicCondition();

        if(!$basicActionCondition instanceof BasicActionCondition){
            $basicActionCondition = new BasicActionCondition();
        }

        $em = $this->getDoctrine()->getManager();
        $mapper = new ActionConditionMapper($basicActionCondition);
        $mapper->setEntityManager($em);

        $form = $this->createForm(new BasicActionConditionType(), $mapper);
        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            if(!$basicActionCondition->getId()){
                $action->setBasicCondition($basicActionCondition);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('shop_discount_admin_action_conditions', [
                'actionId' => $action->getId(),
            ]));

        } else {

            return $this->render('ShopDiscountBundle:AdminActionCondition:condition.html.twig', [
                'form' => $form->createView(),
                'action' => $action,
                'actionCondition' => $basicActionCondition,
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

            array_map(function(Action $action) use ($actionsData) {

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

        $action = $this->getAction($actionId);

        return $this->render('ShopDiscountBundle:AdminActionCondition:conditions.html.twig', [
            'action' => $action,
        ]);

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

        $actionConditionRepository = $this->getActionConditionRepository();
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

    /**
     * @return \Shop\DiscountBundle\Entity\ActionConditionRepository
     */
    protected function getActionConditionRepository(){
        return $this->get('shop_discount.action_condition.repository');
    }

    /**
     * @param $actionId
     * @return Action
     */
    protected function getAction($actionId)
    {
        $actionRepository = $this->get('shop_discount.action.repository');
        $action = $actionRepository->findOneBy(array(
            'id' => $actionId
        ));

        if (!$action instanceof Action) {
            throw $this->createNotFoundException('Action not found');
        }
        return $action;
    }
}
