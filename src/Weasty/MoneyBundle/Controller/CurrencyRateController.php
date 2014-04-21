<?php

namespace Weasty\MoneyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Weasty\MoneyBundle\Entity\CurrencyRate;
use Weasty\MoneyBundle\Mapper\CurrencyRateMapper;

/**
 * Class CurrencyRateController
 * @package Weasty\MoneyBundle\Controller
 */
class CurrencyRateController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ratesAction()
    {

        $repository = $this->getCurrencyRateRepository();
        $currencyRates = $repository->findBy(
            array(),
            array(
                'sourceAlphabeticCode' => 'ASC'
            )
        );

        return $this->render('WeastyMoneyBundle:CurrencyRate:rates.html.twig', array(
            'currencyRates' => $currencyRates,
        ));

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function rateAction($id, Request $request)
    {

        $entity = $id ? $this->getCurrencyRateRepository()->findOneBy(array(
            'id' => $id,
        )) : null;

        if(!$entity instanceof CurrencyRate){
            $entity = new CurrencyRate();
        }

        $isNew = !$entity->getId();
        $mapper = new CurrencyRateMapper($entity, $this->getCurrencyCodeConverter());
        $form = $this->createForm('weasty_money_currency_rate', $mapper);

        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            if($isNew){
                $em->persist($entity);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('weasty_money_currency_rates'));

        } else {

            return $this->render('WeastyMoneyBundle:CurrencyRate:rate.html.twig', array(
                'title' => $isNew ? 'Добавление курса валют' : 'Изменение курса валют',
                'form' => $form->createView(),
                'currencyRate' => $entity,
            ));

        }

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteCategoryAction($id)
    {

        $entity = $this->getCurrencyRateRepository()->findOneBy(array(
            'id' => $id
        ));

        if($entity){

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

        }

        return $this->redirect($this->generateUrl('weasty_money_currency_rates'));

    }


    /**
     * @return \Weasty\MoneyBundle\Entity\CurrencyRateRepository
     */
    protected function getCurrencyRateRepository(){
        return $this->get('weasty_money.currency.rate.repository');
    }

    /**
     * @return \Weasty\MoneyBundle\Converter\CurrencyCodeConverterInterface
     */
    protected function getCurrencyCodeConverter(){
        return $this->get('weasty_money.currency.code.converter');
    }

}
