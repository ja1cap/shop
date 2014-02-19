<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Entity\Action;
use Shop\CatalogBundle\Form\Type\ActionType;
use Shop\MainBundle\Entity\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminActionController
 * @package Shop\CatalogBundle\Controller
 */
class AdminActionController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function actionsAction(Request $request){

        $settings = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Settings')->findOneBy(array());
        if(!$settings){
            $settings = new Settings();
        }

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

            return $this->redirect($this->generateUrl('index'));

        } else {

            $items = $this->getDoctrine()->getManager()->getRepository('ShopCatalogBundle:Action')->findBy(array(), array(
                'position' => 'ASC'
            ));

            return $this->render('ShopCatalogBundle:AdminAction:actions.html.twig', array(
                'form' => $form->createView(),
                'items' => $items,
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

        $actionRepository = $this->getDoctrine()->getManager()->getRepository('ShopCatalogBundle:Action');
        $action = $actionRepository->findOneBy(array(
            'id' => $id
        ));

        if(!$action){
            $action = new Action();
        }

        $form = $this->createForm(new ActionType(), $action);
        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if(!$action->getId()){
                $action->setPosition(count($actionRepository->findAll()) + 1);
                $em->persist($action);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('actions'));

        } else {

            return $this->render('ShopCatalogBundle:AdminAction:action.html.twig', array(
                'title' => $action->getId() ? 'Изменение акции' : 'Добавление акции',
                'form' => $form->createView(),
                'action' => $action,
            ));

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteActionAction($id){

        $item = $this->getDoctrine()->getManager()->getRepository('ShopCatalogBundle:Action')->findOneBy(array(
            'id' => $id
        ));

        if($item){

            $em = $this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('actions'));

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateActionsPositionAction(Request $request){

        $actionRepository = $this->getDoctrine()->getManager()->getRepository('ShopCatalogBundle:Action');;

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

}
