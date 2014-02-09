<?php

namespace Shop\MainBundle\Controller;

use Shop\MainBundle\Entity\Action;
use Shop\MainBundle\Entity\Address;
use Shop\MainBundle\Entity\Benefit;
use Shop\MainBundle\Entity\HowWeItem;
use Shop\MainBundle\Entity\Image;
use Shop\MainBundle\Entity\Problem;
use Shop\MainBundle\Entity\Review;
use Shop\MainBundle\Entity\Settings;
use Shop\MainBundle\Entity\Solution;
use Shop\MainBundle\Entity\WhyUsItem;
use Shop\MainBundle\Form\Type\SettingsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminController
 * @package Shop\MainBundle\Controller
 */
class AdminController extends Controller
{
    public function indexAction()
    {
        return $this->render('ShopMainBundle:Admin:index.html.twig');
    }

    public function whyUsAction(Request $request)
    {

        $settings = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Settings')->findOneBy(array());
        if(!$settings){
            $settings = new Settings();
        }

        $form = $this->createFormBuilder($settings)
            ->add('why_us_title', 'textarea', array(
                'required' => true,
                'label' => 'Заголовок блока',
            ))
            ->add('why_us_description', 'textarea', array(
                'required' => true,
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

            $items = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:WhyUsItem')->findAll();

            return $this->render('ShopMainBundle:Admin:whyUs.html.twig', array(
                'form' => $form->createView(),
                'items' => $items,
            ));

        }

    }

    public function whyUsItemAction($id, Request $request)
    {

        $item = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:WhyUsItem')->findOneBy(array(
            'id' => $id
        ));

        if(!$item){
            $item = new WhyUsItem();
        }

        $form = $this->createFormBuilder($item)
            ->add('title', 'textarea', array(
                'required' => true,
                'label' => 'Заголовок',
            ))
            ->add('description', 'textarea', array(
                'required' => true,
                'label' => 'Описание',
            ))
            ->add('image', 'file', array(
                'required' => false,
                'label' => 'Картинка',
            ))
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ))
            ->getForm();

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if(!$item->getId()){
                $em->persist($item);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('why_us'));

        } else {

            return $this->render('ShopMainBundle:Admin:form.html.twig', array(
                'title' => $item->getId() ? 'Изменение элемента' : 'Добавление елемента',
                'form' => $form->createView(),
            ));

        }

    }

    public function deleteWhyUsItemAction($id){

        $item = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:WhyUsItem')->findOneBy(array(
            'id' => $id
        ));

        if($item){

            $em = $this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('why_us'));

    }

    public function benefitsAction(Request $request)
    {

        $settings = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Settings')->findOneBy(array());
        if(!$settings){
            $settings = new Settings();
        }

        $form = $this->createFormBuilder($settings)
            ->add('benefits_title', 'textarea', array(
                'required' => true,
                'label' => 'Заголовок блока',
            ))
            ->add('benefits_description', 'textarea', array(
                'required' => true,
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

            $items = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Benefit')->findAll();

            return $this->render('ShopMainBundle:Admin:benefits.html.twig', array(
                'form' => $form->createView(),
                'items' => $items,
            ));

        }

    }

    public function benefitAction($id, Request $request)
    {

        $item = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Benefit')->findOneBy(array(
            'id' => $id
        ));

        if(!$item){
            $item = new Benefit();
        }

        $form = $this->createFormBuilder($item)
            ->add('title', 'textarea', array(
                'required' => true,
                'label' => 'Заголовок',
            ))
            ->add('description', 'textarea', array(
                'required' => true,
                'label' => 'Описание',
            ))
            ->add('image', 'file', array(
                'required' => false,
                'label' => 'Картинка',
            ))
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ))
            ->getForm();

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if(!$item->getId()){
                $em->persist($item);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('benefits'));

        } else {

            return $this->render('ShopMainBundle:Admin:form.html.twig', array(
                'title' => $item->getId() ? 'Изменение элемента' : 'Добавление елемента',
                'form' => $form->createView(),
            ));

        }

    }

    public function deleteBenefitAction($id){

        $item = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Benefit')->findOneBy(array(
            'id' => $id
        ));

        if($item){

            $em = $this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('why_us'));

    }

    public function reviewsAction(Request $request)
    {

        $settings = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Settings')->findOneBy(array());
        if(!$settings){
            $settings = new Settings();
        }

        $form = $this->createFormBuilder($settings)
            ->add('reviews_title', 'textarea', array(
                'required' => true,
                'label' => 'Заголовок блока',
            ))
            ->add('reviews_description', 'textarea', array(
                'required' => true,
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

            $items = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Review')->findAll();

            return $this->render('ShopMainBundle:Admin:reviews.html.twig', array(
                'form' => $form->createView(),
                'items' => $items,
            ));

        }

    }

    public function reviewAction($id, Request $request)
    {

        $review = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Review')->findOneBy(array(
            'id' => $id
        ));

        if(!$review){
            $review = new Review();
        }

        $form = $this->createFormBuilder($review)
            ->add('customer_name', 'textarea', array(
                'required' => true,
                'label' => 'Имя клиента',
            ))
            ->add('customer_image', 'file', array(
                'required' => false,
                'label' => 'Изображения клиета',
            ))
            ->add('body', 'textarea', array(
                'required' => false,
                'label' => 'Текст отзыва',
            ))
            ->add('video_code', 'textarea', array(
                'required' => false,
                'label' => 'Ссылка на видео',
            ))
            ->add('review_file', 'file', array(
                'required' => false,
                'label' => 'Фотография с отзывом',
            ))
            ->add('company_name', 'textarea', array(
                'required' => false,
                'label' => 'Название компании',
            ))
            ->add('company_image', 'file', array(
                'required' => false,
                'label' => 'Изображение компании',
            ))
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ))
            ->getForm();

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if(!$review->getId()){
                $em->persist($review);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('reviews'));

        } else {

            return $this->render('ShopMainBundle:Admin:form.html.twig', array(
                'title' => $review->getId() ? 'Изменение отзыва' : 'Добавление отзыва',
                'form' => $form->createView(),
            ));

        }
    }

    public function deleteReviewAction($id){

        $item = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Review')->findOneBy(array(
            'id' => $id
        ));

        if($item){

            $em = $this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('reviews'));

    }

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

            $items = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Action')->findAll();

            return $this->render('ShopMainBundle:Admin:actions.html.twig', array(
                'form' => $form->createView(),
                'items' => $items,
            ));

        }

    }

    public function actionAction($id, Request $request)
    {

        $item = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Action')->findOneBy(array(
            'id' => $id
        ));

        if(!$item){
            $item = new Action();
        }

        $form = $this->createFormBuilder($item)
            ->add('title', 'textarea', array(
                'required' => true,
                'label' => 'Название',
            ))
            ->add('description', 'textarea', array(
                'required' => true,
                'label' => 'Описание',
            ))
            ->add('thumbImage', 'file', array(
                'required' => false,
                'label' => 'Маленькая картинка',
            ))
            ->add('image', 'file', array(
                'required' => false,
                'label' => 'Большая картинка',
            ))
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ))
            ->getForm();

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if(!$item->getId()){
                $em->persist($item);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('actions'));

        } else {

            return $this->render('ShopMainBundle:Admin:form.html.twig', array(
                'title' => $item->getId() ? 'Изменение акции' : 'Добавление акции',
                'form' => $form->createView(),
            ));

        }

    }

    public function deleteActionAction($id){

        $item = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Action')->findOneBy(array(
            'id' => $id
        ));

        if($item){

            $em = $this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('actions'));

    }

    public function imagesAction(Request $request)
    {

        $settings = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Settings')->findOneBy(array());
        if(!$settings){
            $settings = new Settings();
        }

        $form = $this->createFormBuilder($settings)
            ->add('images_title', 'textarea', array(
                'required' => true,
                'label' => 'Заголовок блока',
            ))
            ->add('images_description', 'textarea', array(
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

            $items = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Image')->findAll();

            return $this->render('ShopMainBundle:Admin:images.html.twig', array(
                'form' => $form->createView(),
                'items' => $items,
            ));

        }

    }

    public function imageAction($id, Request $request)
    {

        $item = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Image')->findOneBy(array(
            'id' => $id
        ));

        if(!$item){
            $item = new Image();
        }

        $form = $this->createFormBuilder($item)
            ->add('image', 'file', array(
                'required' => false,
                'label' => 'Фотография',
            ))
            ->add('save', 'submit', array(
                'label' => 'Загрузить',
            ))
            ->getForm();

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if(!$item->getId()){
                $em->persist($item);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('images'));

        } else {

            return $this->render('ShopMainBundle:Admin:form.html.twig', array(
                'title' => $item->getId() ? 'Изменение фотографии' : 'Добавление фотографии',
                'form' => $form->createView(),
            ));

        }

    }

    public function deleteImageAction($id){

        $item = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Image')->findOneBy(array(
            'id' => $id
        ));

        if($item){

            $em = $this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('images'));

    }

    public function howWeAction(Request $request)
    {

        $settings = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Settings')->findOneBy(array());
        if(!$settings){
            $settings = new Settings();
        }

        $form = $this->createFormBuilder($settings)
            ->add('how_we_title', 'textarea', array(
                'required' => true,
                'label' => 'Заголовок блока',
            ))
            ->add('how_we_description', 'textarea', array(
                'required' => true,
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

            return $this->redirect($this->generateUrl('how_we'));

        } else {

            $items = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:HowWeItem')->findAll();

            return $this->render('ShopMainBundle:Admin:howWe.html.twig', array(
                'form' => $form->createView(),
                'items' => $items,
            ));

        }

    }

    public function howWeItemAction($id, Request $request)
    {

        $item = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:HowWeItem')->findOneBy(array(
            'id' => $id
        ));

        if(!$item){
            $item = new HowWeItem();
        }

        $form = $this->createFormBuilder($item)
            ->add('title', 'textarea', array(
                'required' => true,
                'label' => 'Заголовок',
            ))
            ->add('description', 'textarea', array(
                'required' => true,
                'label' => 'Описание',
            ))
            ->add('image', 'file', array(
                'required' => false,
                'label' => 'Картинка',
            ))
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ))
            ->getForm();

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if(!$item->getId()){
                $em->persist($item);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('how_we'));

        } else {

            return $this->render('ShopMainBundle:Admin:form.html.twig', array(
                'title' => $item->getId() ? 'Изменение элемента' : 'Добавление елемента',
                'form' => $form->createView(),
            ));

        }

    }

    public function deleteHowWeItemAction($id){

        $item = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:HowWeItem')->findOneBy(array(
            'id' => $id
        ));

        if($item){

            $em = $this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('why_us'));

    }

    public function problemsAction(Request $request)
    {

        $settings = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Settings')->findOneBy(array());
        if(!$settings){
            $settings = new Settings();
        }

        $form = $this->createFormBuilder($settings)
            ->add('problems_solutions_title', 'textarea', array(
                'required' => true,
                'label' => 'Заголовок блока',
            ))
            ->add('problems_solutions_description', 'textarea', array(
                'required' => true,
                'label' => 'Описание блока',
            ))
            ->add('problems_title', 'textarea', array(
                'required' => true,
                'label' => 'Заголовок проблем',
            ))
            ->add('solutions_title', 'textarea', array(
                'required' => true,
                'label' => 'Заголовок решений',
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

            return $this->redirect($this->generateUrl('problems'));

        } else {

            $problems = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Problem')->findAll();
            $solutions = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Solution')->findAll();

            return $this->render('ShopMainBundle:Admin:problems.html.twig', array(
                'form' => $form->createView(),
                'problems' => $problems,
                'solutions' => $solutions,
            ));

        }

    }

    public function problemAction($id, Request $request)
    {

        $problem = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Problem')->findOneBy(array(
            'id' => $id
        ));

        if(!$problem){
            $problem = new Problem();
        }

        $form = $this->createFormBuilder($problem)
            ->add('description', 'textarea', array(
                'required' => true,
                'label' => 'Описание',
            ))
            ->add('image', 'file', array(
                'required' => false,
                'label' => 'Картинка',
            ))
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ))
            ->getForm();

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if(!$problem->getId()){
                $em->persist($problem);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('problems'));

        } else {

            return $this->render('ShopMainBundle:Admin:form.html.twig', array(
                'title' => $problem->getId() ? 'Изменение проблемы' : 'Добавление проблемы',
                'form' => $form->createView(),
            ));

        }

    }

    public function deleteProblemAction($id){

        $item = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Problem')->findOneBy(array(
            'id' => $id
        ));

        if($item){

            $em = $this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('problems'));

    }

    public function solutionAction($id, Request $request)
    {

        $solution = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Solution')->findOneBy(array(
            'id' => $id
        ));

        if(!$solution){
            $solution = new Solution();
        }

        $form = $this->createFormBuilder($solution)
            ->add('description', 'textarea', array(
                'required' => true,
                'label' => 'Описание',
            ))
            ->add('image', 'file', array(
                'required' => false,
                'label' => 'Картинка',
            ))
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ))
            ->getForm();

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if(!$solution->getId()){
                $em->persist($solution);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('problems'));

        } else {

            return $this->render('ShopMainBundle:Admin:form.html.twig', array(
                'title' => $solution->getId() ? 'Изменение решения' : 'Добавление решения',
                'form' => $form->createView(),
            ));

        }

    }

    public function deleteSolutionAction($id){

        $item = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Solution')->findOneBy(array(
            'id' => $id
        ));

        if($item){

            $em = $this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('problems'));

    }

    public function contactsAction(Request $request)
    {

        $settings = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Settings')->findOneBy(array());
        if(!$settings){
            $settings = new Settings();
        }

        $form = $this->createFormBuilder($settings)
            ->add('contacts_title', 'textarea', array(
                'required' => true,
                'label' => 'Заголовок блока',
            ))
            ->add('phone', 'textarea', array(
                'required' => true,
                'label' => 'Телефон',
            ))
            ->add('email', 'textarea', array(
                'required' => false,
                'label' => 'Email',
            ))
            ->add('address', 'textarea', array(
                'required' => true,
                'label' => 'Основной адрес',
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

            $items = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Address')->findAll();

            return $this->render('ShopMainBundle:Admin:contacts.html.twig', array(
                'form' => $form->createView(),
                'items' => $items,
            ));

        }

    }

    public function addressAction($id, Request $request)
    {

        $address = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Address')->findOneBy(array(
            'id' => $id
        ));

        if(!$address){
            $address = new Address();
        }

        $form = $this->createFormBuilder($address)
            ->add('name', 'textarea', array(
                'required' => true,
                'label' => 'Адрес',
            ))
            ->add('description', 'textarea', array(
                'required' => true,
                'label' => 'Дополнительная информация',
            ))
            ->add('phones', 'textarea', array(
                'required' => false,
                'label' => 'Телефоны',
            ))
            ->add('work_schedule', 'textarea', array(
                'required' => false,
                'label' => 'Время работы',
            ))
            ->add('latitude', 'text', array(
                'required' => true,
                'label' => 'Широта',
            ))
            ->add('longitude', 'text', array(
                'required' => true,
                'label' => 'Долгота',
            ))
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ))
            ->getForm();

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if(!$address->getId()){
                $em->persist($address);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('contacts'));

        } else {

            return $this->render('ShopMainBundle:Admin:address.html.twig', array(
                'title' => $address->getId() ? 'Изменение адреса' : 'Добавление адреса',
                'form' => $form->createView(),
            ));

        }

    }

    public function deleteAddressAction($id){

        $item = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Address')->findOneBy(array(
            'id' => $id
        ));

        if($item){

            $em = $this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('contacts'));

    }

    public function mailTemplatesAction(Request $request){

        $settings = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Settings')->findOneBy(array());
        if(!$settings){
            $settings = new Settings();
        }

        $form = $this->createFormBuilder($settings)
            ->add('customer_email_template', 'textarea', array(
                'required' => true,
                'label' => 'Письмо покупателю',
            ))
            ->add('manager_email', 'email', array(
                'required' => true,
                'label' => 'Email менеджера',
            ))
            ->add('manager_email_template', 'textarea', array(
                'required' => true,
                'label' => 'Письмо менеджеру',
            ))
            ->add('admin_email', 'email', array(
                'required' => true,
                'label' => 'Email администратора',
            ))
            ->add('admin_email_template', 'textarea', array(
                'required' => true,
                'label' => 'Письмо администратору',
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

            return $this->render('ShopMainBundle:Admin:mailTemplates.html.twig', array(
                'form' => $form->createView()
            ));

        }

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function settingsAction(Request $request)
    {

        $settings = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Settings')->findOneBy(array());
        if(!$settings){
            $settings = new Settings();
        }

        $form = $this->createForm(new SettingsType(), $settings);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if(!$settings->getId()){
                $em->persist($settings);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('index'));

        } else {

            return $this->render('ShopMainBundle:Admin:settings.html.twig', array(
                'form' => $form->createView()
            ));

        }

    }

}
