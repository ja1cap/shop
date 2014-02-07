<?php

namespace Shop\MainBundle\Controller;

use Shop\MainBundle\Entity\Bed;
use Shop\MainBundle\Entity\BedBase;
use Shop\MainBundle\Entity\BedBasePrice;
use Shop\MainBundle\Entity\BedPrice;
use Shop\MainBundle\Entity\Address;
use Shop\MainBundle\Entity\HitProposal;
use Shop\MainBundle\Entity\Mattress;
use Shop\MainBundle\Entity\MattressPrice;
use Shop\MainBundle\Entity\Pillow;
use Shop\MainBundle\Entity\PillowPrice;
use Shop\MainBundle\Entity\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 * @package Shop\MainBundle\Controller
 */
class DefaultController extends Controller
{
    public function indexAction()
    {

//        $hit_proposal = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:HitProposal')->findOneBy(array());
//
//        $hit_proposal_form = $this->createFormBuilder(null, array(
//                'attr' => array(
//                    'class' => 'order-proposal-form form-box',
//                ),
//            ))
//            ->add('name', 'hidden', array(
//                'required' => true,
//                'attr' => array(
//                    'class' => 'name',
//                    'value' => $hit_proposal ? $hit_proposal['name'] : null,
//                ),
//            ))
//            ->add('price', 'hidden', array(
//                'required' => true,
//                'attr' => array(
//                    'class' => 'price',
//                    'value' => $hit_proposal ? number_format($hit_proposal['discount_price'], 0, '.', ' ') . ' руб' : null,
//                ),
//            ))
//            ->add('customer_name', 'text', array(
//                'label' => 'КАК К ВАМ ОБРАЩАТЬСЯ*',
//                'required' => true,
//                'attr' => array(
//                    'class' => 'customer-name',
//                ),
//            ))
//            ->add('customer_phone', 'text', array(
//                'label' => 'ТЕЛЕФОН*',
//                'attr' => array(
//                    'class' => 'customer-phone',
//                ),
//            ))
//            ->add('customer_email', 'text', array(
//                'label' => 'E-MAIL',
//                'attr' => array(
//                    'class' => 'customer-email',
//                ),
//            ))
//            ->add('save', 'submit', array(
//                'label' => 'КУПИТЬ СЕЙЧАС',
//                'attr' => array(
//                    'class' => 'submit-btn',
//                ),
//            ))
//            ->getForm();

        $request_form = $this->createFormBuilder(null, array(
                'attr' => array(
                    'class' => 'request-form form-box',
                ),
            ))
            ->add('request_customer_name', 'text', array(
                'label' => 'КАК К ВАМ ОБРАЩАТЬСЯ*',
                'required' => true,
                'attr' => array(
                    'class' => 'customer-name',
                ),
            ))
            ->add('request_customer_phone', 'text', array(
                'label' => 'ТЕЛЕФОН*',
                'attr' => array(
                    'class' => 'customer-phone',
                ),
            ))
            ->add('request_customer_email', 'text', array(
                'label' => 'E-MAIL',
                'attr' => array(
                    'class' => 'customer-email',
                ),
            ))
            ->add('save', 'submit', array(
                'label' => 'ОТПРАВИТЬ ЗАЯВКУ',
                'attr' => array(
                    'class' => 'submit-btn',
                ),
            ))
            ->getForm();

//        $footer_request_form = $this->createFormBuilder(null, array(
//                'attr' => array(
//                    'class' => 'request-form form-box',
//                ),
//            ))
//            ->add('footer_request_customer_name', 'text', array(
//                'label' => 'КАК К ВАМ ОБРАЩАТЬСЯ*',
//                'required' => true,
//                'attr' => array(
//                    'class' => 'customer-name',
//                ),
//            ))
//            ->add('footer_request_customer_phone', 'text', array(
//                'label' => 'ТЕЛЕФОН*',
//                'attr' => array(
//                    'class' => 'customer-phone',
//                ),
//            ))
//            ->add('footer_request_customer_email', 'text', array(
//                'label' => 'E-MAIL',
//                'attr' => array(
//                    'class' => 'customer-email',
//                ),
//            ))
//            ->add('save', 'submit', array(
//                'label' => 'ОТПРАВИТЬ ЗАЯВКУ',
//                'attr' => array(
//                    'class' => 'submit-btn',
//                ),
//            ))
//            ->getForm();

        return $this->render('ShopMainBundle:Default:index.html.twig', array(
            'settings' => $this->getSettings(),
//            'hit_proposal' => $hit_proposal,
//            'hit_proposal_form' => $hit_proposal_form->createView(),
            'request_form' => $request_form->createView(),
//            'footer_request_form' => $footer_request_form->createView(),
//            'why_us_items' => $this->getWhyUsItems(),
            'benefits' => $this->getBenefits(),
            'proposals' => $this->getProposals(),
            'actions' => $this->getActions(),
            'reviews' => $this->getReviews(),
//            'images' => $this->getImages(),
//            'how_we_items' => $this->getHowWeItems(),
//            'problems' => $this->getProblems(),
//            'solutions' => $this->getSolutions(),
            'addresses' => $this->getAddresses(),
        ));

    }

    /**
     * @param $id
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function hitProposalAction($id){

        $hitProposal = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:HitProposal')->findOneBy(array(
            'id' => (int)$id,
        ));

        if(!$hitProposal instanceof HitProposal){
            return new JsonResponse(false, 404);
        }

        $settings = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Settings')->findOneBy(array());
        if(!$settings){
            $settings = new Settings();
        }

        $proposal = array(
            'id' => $hitProposal->getId(),
            'title' => $hitProposal->getName(),
            'image_url' => $hitProposal->getImageUrl(),
            'price' => $hitProposal->getDiscountPrice(),
            'short_description' => $hitProposal->getShortDescription(),
            'description' => $hitProposal->getDescription(),
        );

        return $this->render('ShopMainBundle:Default:proposal.html.twig', array(
            'settings' => $settings,
            'proposal' => $proposal,
        ));

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function processRequestAction(Request $request){

        $this->sendEmail($request);
        return new JsonResponse('Спасибо, заявка принята! В ближайшее время с Вами свяжется наш менеджер для уточнения деталей.');

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function orderProposalAction(Request $request){

        $this->sendEmail($request);
        return new JsonResponse('Спасибо, заявка принята! В ближайшее время с Вами свяжется наш менеджер для уточнения деталей.');

    }

    /**
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function callbackAction(Request $request){

        if($request->getMethod() == 'POST'){

            $this->sendEmail($request);
            return new JsonResponse('Спасибо, заявка принята! В ближайшее время с Вами свяжется наш менеджер для уточнения деталей.');

        } else{

            return $this->render('ShopMainBundle:Default:callback.html.twig');

        }

    }

    protected function sendEmail(Request $request){

        $settings = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Settings')->findOneBy(array());
        if(!$settings){
            $settings = new Settings();
        }

        $proposal_information = null;
        $proposal_name = $request->get('name');
        $proposal_price = $request->get('price');

        if($proposal_name && $proposal_price){
            $proposal_information = $proposal_name . ', ' . $proposal_price;
        }

        $twig = clone $this->get('twig');
        $twig->setLoader(new \Twig_Loader_String());
        $data = array(
            'customer_name' => $request->get('customer_name'),
            'customer_phone' => $request->get('customer_phone'),
            'customer_email' => $request->get('customer_email'),
            'customer_comment' => $request->get('comment'),
            'proposal_information' => $proposal_information,
            'shop_name' => $settings->getName(),
            'shop_address' => $settings->getAddress(),
            'shop_phone' => $settings->getPhone(),
            'shop_email' => $settings->getEmail(),
        );

        if($data['customer_email']){

            $message = \Swift_Message::newInstance()
                ->setSubject($settings->getName())
                ->setFrom($settings->getEmail())
                ->setTo($data['customer_email'])
                ->setBody($twig->render($settings->getCustomerEmailTemplate(), $data));

            $this->get('mailer')->send($message);

        }

        if($settings->getManagerEmail()){

            $message = \Swift_Message::newInstance()
                ->setSubject($settings->getName())
                ->setFrom($settings->getEmail())
                ->setTo($settings->getManagerEmail())
                ->setBody($twig->render($settings->getManagerEmailTemplate(), $data));

            $this->get('mailer')->send($message);

        }

        if($settings->getAdminEmail()){

            $message = \Swift_Message::newInstance()
                ->setSubject($settings->getName())
                ->setFrom($settings->getEmail())
                ->setTo($settings->getAdminEmail())
                ->setBody($twig->render($settings->getAdminEmailTemplate(), $data));

            $this->get('mailer')->send($message);

        }

    }

    /**
     * @return array
     */
    protected function getActions(){
        return $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Action')->findAll();
    }

    /**
     * @return array
     */
    protected function getProblems(){
        return $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Problem')->findAll();
    }

    /**
     * @return array
     */
    protected function getSolutions(){
        return $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Solution')->findAll();
    }

    /**
     * @return array
     */
    protected function getWhyUsItems(){
        return $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:WhyUsItem')->findAll();
    }

    /**
     * @return array
     */
    protected function getHowWeItems(){
        return $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:HowWeItem')->findAll();
    }

    /**
     * @return array
     */
    protected function getReviews(){
        return $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Review')->findAll();
    }

    /**
     * @return array
     */
    protected function getImages(){
        return $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Image')->findAll();
    }

    /**
     * @return array
     */
    protected function getBenefits(){
        return $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Benefit')->findAll();
    }

    /**
     * @return array
     */
    protected function getAddresses(){
        $addresses = array();
        $entities = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Address')->findAll();
        foreach($entities as $entity){
            if($entity instanceof Address){
                $addresses[] = array(
                    'name' => $entity->getName(),
                    'description' => $entity->getDescription(),
                    'phones' => $entity->getPhones(),
                    'work_schedule' => $entity->getWorkSchedule(),
                    'latitude' => $entity->getLatitude(),
                    'longitude' => $entity->getLongitude(),
                );
            }
        }
        return $addresses;
    }

    /**
     * @return array
     */
    protected function getProposals(){

        $proposals = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Proposal')->findBy(array(
            'showOnHomePage' => true
        ));

        return $proposals;

    }

    public function bedsAction(Request $request){

        $sizeIdParameterName = 'sizeId';
        $sizeId = $request->get($sizeIdParameterName, $request->cookies->get($sizeIdParameterName));

        $pageHash = 'type_proposals_page_' . Bed::TYPE;
        $page = $this->getDoctrine()->getRepository('ShopMainBundle:Page')->findOneBy(array(
            'hash' => $pageHash,
        ));

        /**
         * @var $bedRepository \Shop\MainBundle\Entity\BedRepository
         */
        $bedRepository = $this->getDoctrine()->getRepository('ShopMainBundle:Bed');
        $beds = $bedRepository->getBeds($sizeId);

        $viewParameters = array(
            'sizes' => BedPrice::$sizes,
            'sizeId' => $sizeId,
            'page' => $page,
            'proposals' => $beds,
            'settings' => $this->getSettings(),
            'proposalRoute' => 'shop_bed',
        );

        $response = $this->render('ShopMainBundle:Default:proposals/beds.html.twig', $viewParameters);
        $response->headers->setCookie(new Cookie($sizeIdParameterName, $sizeId));

        return $response;

    }

    public function bedAction($slug, Request $request){

        $criteria = array();

        if(is_numeric($slug)){
            $criteria['id'] = (int)$slug;
        } else if(is_string($slug)) {
            $criteria['seoSlug'] = (string)$slug;
        }

        $proposal = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Bed')->findOneBy($criteria);

        if(!$proposal instanceof Bed){
            throw $this->createNotFoundException();
        }

        $sizeIdParameterName = 'sizeId';
        $currentSizeId = $request->get($sizeIdParameterName, $request->cookies->get($sizeIdParameterName));
        $prices = array();
        $lowestPrice = null;
        $currentPrice = null;

        $proposal->getPrices()->map(function(BedPrice $price) use (&$prices, &$lowestPrice){

            if(!$lowestPrice || ($lowestPrice instanceof BedPrice && $lowestPrice->getValue() > $price->getValue())){
                $lowestPrice = $price;
            }

            $prices[$price->getSizeId()] = $price;

        });

        if($prices && $lowestPrice instanceof BedPrice){

            if(isset($prices[$currentSizeId])){
                $currentPrice = $prices[$currentSizeId];
            } else {
                $currentSizeId = $lowestPrice->getSizeId();
                $currentPrice = $lowestPrice;
            }

        }

        return $this->render('ShopMainBundle:Default:proposal/bed.html.twig', array(
            'settings' => $this->getSettings(),
            'proposal' => $proposal,
            'currentSizeId' => $currentSizeId,
            'currentPrice' => $currentPrice,
            'prices' => $prices,
            'currentCategoryPath' => 'shop_beds'
        ));

    }

    public function mattressesAction(Request $request){

        $pageHash = 'type_proposals_page_' . Mattress::TYPE;
        $page = $this->getDoctrine()->getRepository('ShopMainBundle:Page')->findOneBy(array(
            'hash' => $pageHash,
        ));

        $sizeIdParameterName = 'sizeId';
        $sizeId = $request->get($sizeIdParameterName, $request->cookies->get($sizeIdParameterName));

        $manufacturerIdParameterName = 'manufacturerId';
        $manufacturerId = $request->get($manufacturerIdParameterName, $request->cookies->get($manufacturerIdParameterName));

        /**
         * @var $mattressRepository \Shop\MainBundle\Entity\MattressRepository
         */
        $mattressRepository = $this->getDoctrine()->getRepository('ShopMainBundle:Mattress');
        $mattresses = $mattressRepository->getMattresses($sizeId, $manufacturerId);

        $viewParameters = array(
            'manufacturers' => Mattress::$manufacturers,
            'manufacturerId' => $manufacturerId,
            'sizes' => MattressPrice::$sizes,
            'sizeId' => $sizeId,
            'proposals' => $mattresses,
            'settings' => $this->getSettings(),
            'page' => $page,
            'proposalRoute' => 'shop_mattress',
        );

        $response = $this->render('ShopMainBundle:Default:proposals/mattresses.html.twig', $viewParameters);
        $response->headers->setCookie(new Cookie($sizeIdParameterName, $sizeId));
        $response->headers->setCookie(new Cookie($manufacturerIdParameterName, $manufacturerId));

        return $response;

    }

    public function mattressAction($slug, Request $request){

        $criteria = array();

        if(is_numeric($slug)){
            $criteria['id'] = (int)$slug;
        } else if(is_string($slug)) {
            $criteria['seoSlug'] = (string)$slug;
        }

        $proposal = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Mattress')->findOneBy($criteria);

        if(!$proposal instanceof Mattress){
            throw $this->createNotFoundException();
        }

        $sizeIdParameterName = 'sizeId';
        $currentSizeId = $request->get($sizeIdParameterName, $request->cookies->get($sizeIdParameterName));
        $prices = array();
        $lowestPrice = null;
        $currentPrice = null;

        $proposal->getPrices()->map(function(MattressPrice $price) use (&$prices, &$lowestPrice){

            if(!$lowestPrice || ($lowestPrice instanceof MattressPrice && $lowestPrice->getValue() > $price->getValue())){
                $lowestPrice = $price;
            }

            $prices[$price->getSizeId()] = $price;

        });

        if($prices && $lowestPrice instanceof MattressPrice){

            if(isset($prices[$currentSizeId])){
                $currentPrice = $prices[$currentSizeId];
            } else {
                $currentSizeId = $lowestPrice->getSizeId();
                $currentPrice = $lowestPrice;
            }

        }

        return $this->render('ShopMainBundle:Default:proposal/mattress.html.twig', array(
            'settings' => $this->getSettings(),
            'proposal' => $proposal,
            'currentSizeId' => $currentSizeId,
            'currentPrice' => $currentPrice,
            'prices' => $prices,
            'currentCategoryPath' => 'shop_mattresses'
        ));

    }

    public function bedBasesAction(Request $request){

        $pageHash = 'type_proposals_page_' . BedBase::TYPE;
        $page = $this->getDoctrine()->getRepository('ShopMainBundle:Page')->findOneBy(array(
            'hash' => $pageHash,
        ));

        $sizeIdParameterName = 'sizeId';
        $sizeId = $request->get($sizeIdParameterName, $request->cookies->get($sizeIdParameterName));

//        $manufacturerIdParameterName = 'manufacturerId';
//        $manufacturerId = $request->get($manufacturerIdParameterName, $request->cookies->get($manufacturerIdParameterName));
        $manufacturerId = null;

        /**
         * @var $bedBaseRepository \Shop\MainBundle\Entity\BedBaseRepository
         */
        $bedBaseRepository = $this->getDoctrine()->getRepository('ShopMainBundle:BedBase');
        $bedBases = $bedBaseRepository->getBases($sizeId, $manufacturerId);

        $viewParameters = array(
            'manufacturers' => BedBase::$manufacturers,
            'manufacturerId' => $manufacturerId,
            'sizes' => BedBasePrice::$sizes,
            'sizeId' => $sizeId,
            'proposals' => $bedBases,
            'settings' => $this->getSettings(),
            'page' => $page,
            'proposalRoute' => 'shop_bed_base',
        );

        $response = $this->render('ShopMainBundle:Default:proposals/bedBases.html.twig', $viewParameters);
        $response->headers->setCookie(new Cookie($sizeIdParameterName, $sizeId));
//        $response->headers->setCookie(new Cookie($manufacturerIdParameterName, $manufacturerId));

        return $response;

    }

    public function bedBaseAction($slug, Request $request){

        $criteria = array();

        if(is_numeric($slug)){
            $criteria['id'] = (int)$slug;
        } else if(is_string($slug)) {
            $criteria['seoSlug'] = (string)$slug;
        }

        $proposal = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:BedBase')->findOneBy($criteria);

        if(!$proposal instanceof BedBase){
            throw $this->createNotFoundException();
        }

        $sizeIdParameterName = 'sizeId';
        $currentSizeId = $request->get($sizeIdParameterName, $request->cookies->get($sizeIdParameterName));
        $prices = array();
        $lowestPrice = null;
        $currentPrice = null;

        $proposal->getPrices()->map(function(BedBasePrice $price) use (&$prices, &$lowestPrice){

            if(!$lowestPrice || ($lowestPrice instanceof BedBasePrice && $lowestPrice->getValue() > $price->getValue())){
                $lowestPrice = $price;
            }

            $prices[$price->getSizeId()] = $price;

        });

        if($prices && $lowestPrice instanceof BedBasePrice){

            if(isset($prices[$currentSizeId])){
                $currentPrice = $prices[$currentSizeId];
            } else {
                $currentSizeId = $lowestPrice->getSizeId();
                $currentPrice = $lowestPrice;
            }

        }

        return $this->render('ShopMainBundle:Default:proposal/bedBase.html.twig', array(
            'settings' => $this->getSettings(),
            'proposal' => $proposal,
            'currentSizeId' => $currentSizeId,
            'currentPrice' => $currentPrice,
            'prices' => $prices,
            'currentCategoryPath' => 'shop_bed_bases'
        ));

    }

    public function pillowsAction(){

        $pageHash = 'type_proposals_page_' . Pillow::TYPE;
        $page = $this->getDoctrine()->getRepository('ShopMainBundle:Page')->findOneBy(array(
            'hash' => $pageHash,
        ));

        /**
         * @var $repository \Shop\MainBundle\Entity\PillowRepository
         */
        $repository = $this->getDoctrine()->getRepository('ShopMainBundle:Pillow');
        $proposals = $repository->getPillows();

        $viewParameters = array(
            'proposals' => $proposals,
            'settings' => $this->getSettings(),
            'page' => $page,
            'proposalRoute' => 'shop_pillow',
        );

        $response = $this->render('ShopMainBundle:Default:proposals/pillows.html.twig', $viewParameters);

        return $response;

    }

    public function pillowAction($slug, Request $request){

        $criteria = array();

        if(is_numeric($slug)){
            $criteria['id'] = (int)$slug;
        } else if(is_string($slug)) {
            $criteria['seoSlug'] = (string)$slug;
        }

        $proposal = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Pillow')->findOneBy($criteria);

        if(!$proposal instanceof Pillow){
            throw $this->createNotFoundException();
        }

        $prices = array();
        $lowestPrice = null;
        $currentPrice = null;

        $proposal->getPrices()->map(function(PillowPrice $price) use (&$prices, &$lowestPrice){

            if(!$lowestPrice || ($lowestPrice instanceof PillowPrice && $lowestPrice->getValue() > $price->getValue())){
                $lowestPrice = $price;
            }

            $prices[$price->getId()] = $price;

        });

        if($prices && $lowestPrice instanceof PillowPrice){
            $currentPrice = $lowestPrice;
        }

        return $this->render('ShopMainBundle:Default:proposal/pillow.html.twig', array(
            'settings' => $this->getSettings(),
            'proposal' => $proposal,
            'currentPrice' => $currentPrice,
            'prices' => $prices,
            'currentCategoryPath' => 'shop_pillows'
        ));

    }

    /**
     * @return object|Settings
     */
    protected function getSettings()
    {
        $settings = $this->getDoctrine()->getManager()->getRepository('ShopMainBundle:Settings')->findOneBy(array());
        if (!$settings) {
            $settings = new Settings();
        }
        return $settings;
    }

}
