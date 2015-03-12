<?php

namespace Shop\DiscountBundle\Controller;

use Shop\DiscountBundle\Entity\Action;
use Shop\DiscountBundle\Entity\ActionProposal;
use Shop\DiscountBundle\Form\Type\ActionProposalType;
use Shop\DiscountBundle\Mapper\ActionConditionMapper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AdminActionProposalController extends Controller
{

    /**
     * @param $actionId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function proposalsAction($actionId)
    {

        $action = $this->getAction($actionId);

        return $this->render('ShopDiscountBundle:AdminActionProposal:proposals.html.twig', [
            'action' => $action,
        ]);

    }

    /**
     * @param $actionId
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function proposalAction($actionId, $id, Request $request)
    {

        $action = $this->getAction($actionId);

        $actionConditionRepository = $this->getDoctrine()->getRepository('ShopDiscountBundle:ActionProposal');
        $actionProposal = $actionConditionRepository->findOneBy(array(
            'actionId' => $action->getId(),
            'id' => $id,
        ));

        if(!$actionProposal instanceof ActionProposal){
            $actionProposal = new ActionProposal();
        }

        $em = $this->getDoctrine()->getManager();
        $mapper = new ActionConditionMapper($actionProposal);
        $mapper->setEntityManager($em);

        $form = $this->createForm(new ActionProposalType(), $mapper);
        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            if(!$actionProposal->getId()){
                $action->addCondition($actionProposal);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('shop_discount_admin_action_proposals', [
                'actionId' => $action->getId(),
            ]));

        } else {

            return $this->render('ShopDiscountBundle:AdminActionProposal:proposal.html.twig', [
                'form' => $form->createView(),
                'action' => $action,
                'actionCondition' => $actionProposal,
            ]);

        }

    }

    /**
     * @param $actionId
     * @param Request $request
     * @return JsonResponse
     */
    public function addProposalsAction($actionId, Request $request){

        $action = $this->getAction($actionId);
        $proposalIds = $request->get('proposalIds');

        if(is_array($proposalIds)){

            $currentProposalIds = $action->getProposalIds();
            $proposalIds = array_filter($proposalIds, function($proposalId) use ($currentProposalIds){
                return (is_int((int)$proposalId) && !in_array($proposalId, $currentProposalIds));
            });

            if($proposalIds){

                $em = $this->getDoctrine()->getManager();
                $proposalRepository = $this->getDoctrine()->getRepository('ShopCatalogBundle:Proposal');
                $proposals = $proposalRepository->findBy(['id' => $proposalIds]);

                foreach($proposals as $proposal){

                    $actionProposal = new ActionProposal();
                    $actionProposal
                        ->setProposal($proposal)
                    ;

                    $action->addCondition($actionProposal);

                }

                $em->flush();

            }

        }

        return new JsonResponse('OK');

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
