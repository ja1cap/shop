<?php

namespace Shop\MainBundle\Controller;

use Shop\MainBundle\Entity\Address;
use Shop\MainBundle\Entity\Settings;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
            'request_form' => $request_form->createView(),
//            'footer_request_form' => $footer_request_form->createView(),
//            'why_us_items' => $this->getWhyUsItems(),
            'benefits' => $this->getBenefits(),
            'reviews' => $this->getReviews(),
//            'how_we_items' => $this->getHowWeItems(),
//            'problems' => $this->getProblems(),
//            'solutions' => $this->getSolutions(),
            'addresses' => $this->getAddresses(),
        ));

    }

    /**
     * @return BinaryFileResponse
     */
    public function faviconAction(){

        $settings = $this->get('shop_main.settings.resource')->getSettings();
        if(!$settings instanceof Settings){
            throw $this->createNotFoundException('Settings not found');
        }

        $logo = $settings->getLogo();
        if (!$logo instanceof MediaInterface) {
            throw $this->createNotFoundException('Favicon not found');
        }

        /**
         * @var $mediaService \Sonata\MediaBundle\Provider\Pool
         */
        $mediaService = $this->get('sonata.media.pool');
        $provider = $mediaService->getProvider($logo->getProviderName());

        $format = 'favicon';
        $format = $provider->getFormatName($logo, $format);

        /**
         * @var $adapter \Sonata\MediaBundle\Filesystem\Local
         */
        $adapter = $provider->getFilesystem()->getAdapter();
        $filename = sprintf('%s/%s',
          $adapter->getDirectory(),
          $provider->generatePrivateUrl($logo, $format)
        );

        $response = new BinaryFileResponse($filename);
        return $response;

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

        /**
         * @var $settings \Shop\MainBundle\Entity\Settings
         * @var $contacts \Shop\MainBundle\Data\ShopContactsResource
         */
        $settings = $this->get('shop_main.settings.resource')->getSettings();
        $contacts = $this->get('shop_main.contacts.resource');

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
            'order_information' => $proposal_information,
            'shop_name' => $settings->getName(),
            'shop_address' => $settings->getAddress(),
            'shop_phone' => implode(', ', $contacts->getPhones()),
            'shop_email' => $settings->getEmail(),
        );

        if($data['customer_email']){

            $message = \Swift_Message::newInstance()
                ->setSubject($settings->getName())
                ->setFrom($settings->getEmail())
                ->setTo($data['customer_email'])
                ->setBody($twig->render($settings->getCustomerEmailTemplate(), $data), 'text/html');

            $this->get('mailer')->send($message);

        }

        if($settings->getManagerEmail()){

            $message = \Swift_Message::newInstance()
                ->setSubject($settings->getName())
                ->setFrom($settings->getEmail())
                ->setTo($settings->getManagerEmail())
                ->setBody($twig->render($settings->getManagerEmailTemplate(), $data), 'text/html');

            $this->get('mailer')->send($message);

        }

        if($settings->getAdminEmail()){

            $message = \Swift_Message::newInstance()
                ->setSubject($settings->getName())
                ->setFrom($settings->getEmail())
                ->setTo($settings->getAdminEmail())
                ->setBody($twig->render($settings->getAdminEmailTemplate(), $data), 'text/html');

            $this->get('mailer')->send($message);

        }

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

}
