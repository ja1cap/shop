<?php

namespace Shop\MainBundle\Controller;

use Shop\MainBundle\Entity\AbstractPrice;
use Shop\MainBundle\Entity\Action;
use Shop\MainBundle\Entity\Address;
use Shop\MainBundle\Entity\Benefit;
use Shop\MainBundle\Entity\HitProposal;
use Shop\MainBundle\Entity\HowWeItem;
use Shop\MainBundle\Entity\Image;
use Shop\MainBundle\Entity\MattressPrice;
use Shop\MainBundle\Entity\Page;
use Shop\MainBundle\Entity\Problem;
use Shop\MainBundle\Entity\Proposal;
use Shop\MainBundle\Entity\Review;
use Shop\MainBundle\Entity\Settings;
use Shop\MainBundle\Entity\Solution;
use Shop\MainBundle\Entity\WhyUsItem;
use Shop\MainBundle\Form\Type\PageType;
use Shop\MainBundle\Form\Type\SettingsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{
    public function indexAction()
    {
        return $this->render('ShopMainBundle:Admin:index.html.twig');
    }

    public function hitProposalAction(Request $request)
    {

        $hitProposal = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:HitProposal')->findOneBy(array());
        if(!$hitProposal){
            $hitProposal = new HitProposal();
        }

        $form = $this->createFormBuilder($hitProposal)
            ->add('main_title', 'textarea', array(
                'required' => true,
                'label' => 'Заголовок блка',
            ))
            ->add('main_description', 'textarea', array(
                'required' => true,
                'label' => 'Описание блока',
            ))
            ->add('main_img', 'file', array(
                'required' => false,
                'label' => 'Фоновое изображение блока',
            ))
            ->add('name', 'textarea', array(
                'required' => true,
                'label' => 'Наименование товара',
            ))
            ->add('short_description', 'textarea', array(
                'required' => true,
                'label' => 'Краткое описание товара',
            ))
            ->add('description', 'textarea', array(
                'required' => true,
                'label' => 'Описание товара',
            ))
            ->add('image', 'file', array(
                'required' => false,
                'label' => 'Фото товара',
            ))
            ->add('price_legend', 'textarea', array(
                'required' => true,
                'label' => 'Лененда цены',
            ))
            ->add('discount_price', 'text', array(
                'required' => true,
                'label' => 'Цена со скидкой',
            ))
            ->add('regular_price', 'text', array(
                'required' => true,
                'label' => 'Обычная цена',
            ))
            ->add('bonus', 'textarea', array(
                'required' => false,
                'label' => 'Бонус',
            ))
            ->add('bonus_image', 'file', array(
                'required' => false,
                'label' => 'Картинка бонуса',
            ))
            ->add('timer_legend', 'textarea', array(
                'required' => false,
                'label' => 'Легенда таймера',
            ))
            ->add('timer_end_date', 'datetime', array(
                'required' => false,
                'label' => 'Дата окончания таймера',
            ))
            ->add('save', 'submit', array(
                'label' => 'Сохранить',
            ))
            ->getForm();

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if(!$hitProposal->getId()){
                $em->persist($hitProposal);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('index'));

        } else {

            return $this->render('ShopMainBundle:Admin:hitProposal.html.twig', array(
                'form' => $form->createView()
            ));

        }

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

    public function proposalAction($type, $id, Request $request)
    {

        /**
         * @var $proposalRepository \Shop\MainBundle\Entity\ProposalRepository
         */
        $proposalRepository = $this->getDoctrine()->getRepository('ShopMainBundle:Proposal');
        $proposal = $proposalRepository->findOneBy(array(
            'id' => $id
        ));
        $proposalTypes = $proposalRepository->getTypes();
        $proposalType = $proposalTypes[$type];

        if(!$proposal instanceof Proposal){


            $refl = new \ReflectionClass($proposalType['class_name']);
            $proposal = $refl->newInstance();

        }

        $isNew = !$proposal->getId();
        $form = $this->createForm($proposal->getForm(), $proposal);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $em->persist($proposal);
            }

            $em->flush();

            return $this->redirect(
                $isNew
                    ? $this->generateUrl('proposal', array('type' => $proposalType['type'], 'id' => $proposal->getId()))
                    : $this->generateUrl('type_proposals', array('type' => $proposalType['type']))
            );

        } else {

            return $this->render('ShopMainBundle:Admin:proposal.html.twig', array(
                'title' => $isNew ? 'Добавление предложения' : 'Изменение предложения',
                'form' => $form->createView(),
                'proposal' => $proposal,
                'proposal_type' => $proposalType,
            ));

        }

    }

    public function deleteProposalAction($type, $id){

        /**
         * @var $proposalRepository \Shop\MainBundle\Entity\ProposalRepository
         */
        $proposalRepository = $this->getDoctrine()->getRepository('ShopMainBundle:Proposal');
        $proposalTypes = $proposalRepository->getTypes();
        $proposalType = $proposalTypes[$type];

        $item = $proposalRepository->findOneBy(array(
            'id' => $id
        ));

        if($item){

            $em = $this->getDoctrine()->getManager();
            $em->remove($item);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('type_proposals', array('type' => $proposalType['type'])));

    }

    /**
     * @param $proposalId
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function proposalPriceAction($proposalId, $id, Request $request){

        /**
         * @var $proposalRepository \Shop\MainBundle\Entity\ProposalRepository
         * @var $proposal \Shop\MainBundle\Entity\Proposal
         */
        $proposalRepository = $this->getDoctrine()->getRepository('ShopMainBundle:Proposal');
        $proposalTypes = $proposalRepository->getTypes();

        $proposal = $proposalRepository->findOneBy(array(
            'id' => $proposalId
        ));
        $proposalType = $proposalTypes[$proposal::TYPE];

//        $json_config = '[{"attributes":{"500":{"id":"500","code":"matt_area","options":[{"id":"94","label":"80x190","oldPrice":"710000.0000","price":710000},{"id":"124","label":"80x195","oldPrice":"710000.0000","price":710000},{"id":"13","label":"80x200","oldPrice":"710000.0000","price":710000},{"id":"99","label":"90x190","oldPrice":"750000.0000","price":750000},{"id":"123","label":"90x195","oldPrice":"750000.0000","price":750000},{"id":"12","label":"90x200","oldPrice":"750000.0000","price":750000},{"id":"98","label":"120x190","oldPrice":"980000.0000","price":980000},{"id":"122","label":"120x195","oldPrice":"980000.0000","price":980000},{"id":"9","label":"120x200","oldPrice":"980000.0000","price":980000},{"id":"97","label":"140x190","oldPrice":"990000.0000","price":990000},{"id":"129","label":"140x195","oldPrice":"990000.0000","price":990000},{"id":"7","label":"140x200","oldPrice":"990000.0000","price":990000},{"id":"93","label":"160x190","oldPrice":"1020000.0000","price":1020000},{"id":"127","label":"160x195","oldPrice":"1020000.0000","price":1020000},{"id":"5","label":"160x200","oldPrice":"1020000.0000","price":1020000},{"id":"92","label":"180x190","oldPrice":"1080000.0000","price":1080000},{"id":"125","label":"180x195","oldPrice":"1080000.0000","price":1080000},{"id":"3","label":"180x200","oldPrice":"1080000.0000","price":1080000},{"id":"1","label":"200x200","oldPrice":"1280000.0000","price":1280000}]}},"template":"#{price} руб.","productId":"481"},{"attributes":{"500":{"id":"500","code":"matt_area","options":[{"id":"94","label":"80x190","oldPrice":"770000.0000","price":770000},{"id":"124","label":"80x195","oldPrice":"770000.0000","price":770000},{"id":"13","label":"80x200","oldPrice":"770000.0000","price":770000},{"id":"99","label":"90x190","oldPrice":"800000.0000","price":800000},{"id":"123","label":"90x195","oldPrice":"800000.0000","price":800000},{"id":"12","label":"90x200","oldPrice":"800000.0000","price":800000},{"id":"98","label":"120x190","oldPrice":"1060000.0000","price":1060000},{"id":"122","label":"120x195","oldPrice":"1060000.0000","price":1060000},{"id":"9","label":"120x200","oldPrice":"1060000.0000","price":1060000},{"id":"97","label":"140x190","oldPrice":"1080000.0000","price":1080000},{"id":"129","label":"140x195","oldPrice":"1080000.0000","price":1080000},{"id":"7","label":"140x200","oldPrice":"1080000.0000","price":1080000},{"id":"93","label":"160x190","oldPrice":"1110000.0000","price":1110000},{"id":"127","label":"160x195","oldPrice":"1110000.0000","price":1110000},{"id":"5","label":"160x200","oldPrice":"1110000.0000","price":1110000},{"id":"92","label":"180x190","oldPrice":"1200000.0000","price":1200000},{"id":"125","label":"180x195","oldPrice":"1200000.0000","price":1200000},{"id":"3","label":"180x200","oldPrice":"1200000.0000","price":1200000},{"id":"1","label":"200x200","oldPrice":"1370000.0000","price":1370000}]}},"template":"#{price} руб.","productId":"482"},{"attributes":{"500":{"id":"500","code":"matt_area","options":[{"id":"94","label":"80x190","oldPrice":"690000.0000","price":690000},{"id":"124","label":"80x195","oldPrice":"690000.0000","price":690000},{"id":"13","label":"80x200","oldPrice":"690000.0000","price":690000},{"id":"99","label":"90x190","oldPrice":"720000.0000","price":720000},{"id":"123","label":"90x195","oldPrice":"720000.0000","price":720000},{"id":"12","label":"90x200","oldPrice":"720000.0000","price":720000},{"id":"98","label":"120x190","oldPrice":"970000.0000","price":970000},{"id":"122","label":"120x195","oldPrice":"970000.0000","price":970000},{"id":"9","label":"120x200","oldPrice":"970000.0000","price":970000},{"id":"97","label":"140x190","oldPrice":"970000.0000","price":970000},{"id":"129","label":"140x195","oldPrice":"970000.0000","price":970000},{"id":"7","label":"140x200","oldPrice":"970000.0000","price":970000},{"id":"93","label":"160x190","oldPrice":"990000.0000","price":990000},{"id":"127","label":"160x195","oldPrice":"990000.0000","price":990000},{"id":"5","label":"160x200","oldPrice":"990000.0000","price":990000},{"id":"92","label":"180x190","oldPrice":"1070000.0000","price":1070000},{"id":"125","label":"180x195","oldPrice":"1070000.0000","price":1070000},{"id":"3","label":"180x200","oldPrice":"1070000.0000","price":1070000},{"id":"1","label":"200x200","oldPrice":"1250000.0000","price":1250000}]}},"template":"#{price} руб.","productId":"531"},{"attributes":{"500":{"id":"500","code":"matt_area","options":[{"id":"94","label":"80x190","oldPrice":"940000.0000","price":940000},{"id":"124","label":"80x195","oldPrice":"940000.0000","price":940000},{"id":"13","label":"80x200","oldPrice":"940000.0000","price":940000},{"id":"99","label":"90x190","oldPrice":"970000.0000","price":970000},{"id":"123","label":"90x195","oldPrice":"970000.0000","price":970000},{"id":"12","label":"90x200","oldPrice":"970000.0000","price":970000},{"id":"98","label":"120x190","oldPrice":"1420000.0000","price":1420000},{"id":"122","label":"120x195","oldPrice":"1420000.0000","price":1420000},{"id":"9","label":"120x200","oldPrice":"1420000.0000","price":1420000},{"id":"97","label":"140x190","oldPrice":"1490000.0000","price":1490000},{"id":"129","label":"140x195","oldPrice":"1490000.0000","price":1490000},{"id":"7","label":"140x200","oldPrice":"1490000.0000","price":1490000},{"id":"93","label":"160x190","oldPrice":"1550000.0000","price":1550000},{"id":"127","label":"160x195","oldPrice":"1550000.0000","price":1550000},{"id":"5","label":"160x200","oldPrice":"1550000.0000","price":1550000},{"id":"92","label":"180x190","oldPrice":"1630000.0000","price":1630000},{"id":"125","label":"180x195","oldPrice":"1630000.0000","price":1630000},{"id":"3","label":"180x200","oldPrice":"1630000.0000","price":1630000}]}},"template":"#{price} руб.","productId":"1130"},{"attributes":{"500":{"id":"500","code":"matt_area","options":[{"id":"13","label":"80x200","oldPrice":"1620000.0000","price":1620000},{"id":"12","label":"90x200","oldPrice":"1640000.0000","price":1640000},{"id":"9","label":"120x200","oldPrice":"1880000.0000","price":1880000},{"id":"7","label":"140x200","oldPrice":"1880000.0000","price":1880000},{"id":"5","label":"160x200","oldPrice":"2010000.0000","price":2010000},{"id":"3","label":"180x200","oldPrice":"2060000.0000","price":2060000},{"id":"1","label":"200x200","oldPrice":"2270000.0000","price":2270000}]}},"template":"#{price} руб.","productId":"1753"}]';
//        $config = json_decode($json_config, true);
//        $options = $config[0]['attributes']['500']['options'];
//
//        foreach($options as $option){
//            $sizeId = array_search($option['label'], BedBasePrice::$sizes);
//            $price = $proposal->createPrice();
//            $price->setSizeId($sizeId);
//            $price->setValue($option['price']);
//            $proposal->addPrice($price);
//        }
//
//        $em = $this->getDoctrine()->getManager();
//        $em->flush();

//        $json_config = @file_get_contents('json_config.json');
//        $options = json_decode($json_config, true);
//
//        foreach($options as $option){
//            $size = $option['size'];
//            $value = $option['value'];
//            $sizeId = array_search($size, MattressPrice::$sizes);
//            if($sizeId){
//                $price = $proposal->createPrice();
//                $price->setSizeId($sizeId);
//                $price->setValue($value);
//                $proposal->addPrice($price);
//            } else {
//                var_dump($size);die;
//            }
//        }
//
//        $em = $this->getDoctrine()->getManager();
//        $em->flush();
//        return $this->redirect($this->generateUrl('proposal', array('type' => $proposal::TYPE, 'id' => $proposal->getId())));

        $pricesRepository = $this->getDoctrine()->getRepository('ShopMainBundle:AbstractPrice');
        $price = $pricesRepository->findOneBy(array(
            'id' => $id
        ));

        if(!$price instanceof AbstractPrice){
            $price = $proposal->createPrice();
        }

        $isNew = !$price->getId();
        $form = $this->createForm($price->getForm($proposal), $price);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $proposal->addPrice($price);
            }

            $em->flush();

//            return $this->redirect($this->generateUrl('proposal_price', array('proposalId' => $proposal->getId())));
            return $this->redirect($this->generateUrl('proposal', array('type' => $proposal::TYPE, 'id' => $proposal->getId())));

        } else {

            return $this->render('ShopMainBundle:Admin:proposalPrice.html.twig', array(
                'title' => $isNew ? 'Добавление цены' : 'Изменение цены',
                'form' => $form->createView(),
                'price' => $price,
                'proposal' => $proposal,
                'proposal_type' => $proposalType,
            ));

        }

    }

    /**
     * @param $proposalId
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteProposalPriceAction($proposalId, $id){

        /**
         * @var $proposalRepository \Shop\MainBundle\Entity\ProposalRepository
         * @var $proposal \Shop\MainBundle\Entity\Proposal
         */
        $proposalRepository = $this->getDoctrine()->getRepository('ShopMainBundle:Proposal');
        $proposal = $proposalRepository->findOneBy(array(
            'id' => $proposalId
        ));

        $pricesRepository = $this->getDoctrine()->getRepository('ShopMainBundle:AbstractPrice');
        $price = $pricesRepository->findOneBy(array(
            'id' => $id
        ));

        if($price){

            $em = $this->getDoctrine()->getManager();
            $em->remove($price);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('proposal', array('type' => $proposal::TYPE, 'id' => $proposal->getId())));

    }

    public function proposalsAction(Request $request)
    {

        $settings = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Settings')->findOneBy(array());
        if(!$settings){
            $settings = new Settings();
        }

        $form = $this->createFormBuilder($settings)
            ->add('proposals_title', 'textarea', array(
                'required' => true,
                'label' => 'Заголовок блока',
            ))
            ->add('proposals_description', 'textarea', array(
                'required' => true,
                'label' => 'Описание блока',
            ))
            ->add('proposals_image', 'file', array(
                'required' => false,
                'label' => 'Фоновое изображение блока',
            ))
//            ->add('catalog_file', 'file', array(
//                'required' => false,
//                'label' => 'Фаил с каталогом',
//            ))
//            ->add('catalog_download_title', 'text', array(
//                'required' => false,
//                'label' => 'Название кнопки "Скачать каталог"',
//            ))
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

            /**
             * @var $proposalRepository \Shop\MainBundle\Entity\ProposalRepository
             */
            $proposalRepository = $this->getDoctrine()->getRepository('ShopMainBundle:Proposal');
            $proposalTypes = $proposalRepository->getTypes();

            return $this->render('ShopMainBundle:Admin:proposals.html.twig', array(
                'form' => $form->createView(),
                'proposal_types' => $proposalTypes,
            ));

        }

    }

    public function typeProposalsAction($type){

        /**
         * @var $proposalRepository \Shop\MainBundle\Entity\ProposalRepository
         */
        $proposalRepository = $this->getDoctrine()->getRepository('ShopMainBundle:Proposal');
        $proposalTypes = $proposalRepository->getTypes();
        $proposalType = $proposalTypes[$type];

        $proposalTypeRepository = $this->getDoctrine()->getRepository($proposalType['class_name']);
        $proposals = $proposalTypeRepository->findBy(array(
//            'manufacturerId' => 3
        ));

        return $this->render('ShopMainBundle:Admin:typeProposals.html.twig', array(
            'proposal_type' => $proposalType,
            'proposals' => $proposals
        ));

    }

    public function typeProposalsPageAction($type, Request $request){

        /**
         * @var $proposalRepository \Shop\MainBundle\Entity\ProposalRepository
         */
        $proposalRepository = $this->getDoctrine()->getRepository('ShopMainBundle:Proposal');
        $proposalTypes = $proposalRepository->getTypes();
        $proposalType = $proposalTypes[$type];

        $pageHash = 'type_proposals_page_' . $type;
        $page = $this->getDoctrine()->getRepository('ShopMainBundle:Page')->findOneBy(array(
            'hash' => $pageHash,
        ));

        if(!$page instanceof Page){
            $page = new Page();
            $page->setHash($pageHash);
        }

        $isNew = !$page->getId();
        $form = $this->createForm(new PageType(array(
            'seoSlug' => false
        )), $page);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $em->persist($page);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('type_proposals', array('type' => $proposalType['type'])));

        } else {

            return $this->render('ShopMainBundle:Admin:typeProposalsPage.html.twig', array(
                'title' => 'Редакитрование',
                'form' => $form->createView(),
                'proposal_type' => $proposalType,
            ));

        }


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
