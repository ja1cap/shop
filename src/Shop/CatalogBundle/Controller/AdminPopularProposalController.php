<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Entity\PopularProposal;
use Shop\CatalogBundle\Form\Type\PopularProposalType;
use Shop\CatalogBundle\Mapper\PopularProposalMapper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminPopularProposalController
 * @package Shop\CatalogBundle\Controller
 */
class AdminPopularProposalController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function proposalsAction()
    {

        return $this->render('ShopCatalogBundle:AdminPopularProposal:proposals.html.twig', array(
            'popularProposals' => $this->getPopularProposalRepository()->findProposals(),
        ));

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function proposalAction($id, Request $request)
    {

        $repository = $this->getPopularProposalRepository();
        $entity = $repository->findOneBy(array(
            'id' => $id
        ));

        if(!$entity instanceof PopularProposal){
            $entity = new PopularProposal;
        }

        $isNew = !$entity->getId();
        $mapper = new PopularProposalMapper($entity, $repository, $this->getDoctrine()->getRepository('ShopCatalogBundle:Proposal'));
        $form = $this->createForm(new PopularProposalType(), $mapper);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $em->persist($entity);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('popular_proposals'));

        } else {

            return $this->render('ShopCatalogBundle:AdminPopularProposal:proposal.html.twig', array(
                'title' => $isNew ? 'Добавление популярного товара' : 'Изменение популярного товара',
                'form' => $form->createView(),
            ));

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteProposalAction($id)
    {

        $entity = $this->getPopularProposalRepository()->findOneBy(array(
            'id' => $id
        ));

        if($entity){

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('popular_proposals'));

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProposalsAction(Request $request){

        $popularProposalsData = $request->get('popularProposals');
        if(is_array($popularProposalsData)){

            /**
             * @var $popularProposal PopularProposal
             */
            foreach($this->getPopularProposalRepository()->findAll() as $popularProposal){

                if(isset($popularProposalsData[$popularProposal->getId()])){

                    $popularProposalData = $popularProposalsData[$popularProposal->getId()];
                    if(is_array($popularProposalData)){

                        if(isset($popularProposalData['position'])){

                            $popularProposal->setPosition((int)$popularProposalData['position']);

                        }

                    }

                }

            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

        }

        return new JsonResponse('OK');

    }

    /**
     * @return \Shop\CatalogBundle\Entity\PopularProposalRepository
     * @throws \LogicException
     */
    protected function getPopularProposalRepository(){
        return $this->getDoctrine()->getRepository('ShopCatalogBundle:PopularProposal');
    }

}
