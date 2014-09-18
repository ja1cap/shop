<?php
namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Entity\ProposalCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminProposalCollectionController
 * @package Shop\CatalogBundle\Controller
 */
class AdminProposalCollectionController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function collectionsAction(){

        $collections = $this->getDoctrine()->getRepository('ShopCatalogBundle:ProposalCollection')->findAll();

        return $this->render('ShopCatalogBundle:AdminProposalCollection:collections.html.twig', array(
            'collections' => $collections,
        ));

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function collectionAction($id, Request $request){

        $collection = $this->getDoctrine()->getRepository('ShopCatalogBundle:ProposalCollection')->findOneBy(array(
            'id' => $id,
        ));

        if(!$collection instanceof ProposalCollection){
            $collection = new ProposalCollection();
        }

        $isNew = !$collection->getId();
        $form = $this->createForm('shop_catalog_proposal_collection', $collection);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $em->persist($collection);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('proposal_collection', array(
                'id' => $collection->getId(),
            )));

        } else {

            return $this->render('ShopCatalogBundle:AdminProposalCollection:collection.html.twig', array(
                'title' => $isNew ? 'Добавление колекции' : 'Изменение колекции',
                'form' => $form->createView(),
                'collection' => $collection,
            ));

        }

    }

    public function deleteCollectionAction(){
        //@TODO create deleteCollectionAction
    }

} 