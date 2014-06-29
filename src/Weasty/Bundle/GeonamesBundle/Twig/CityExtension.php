<?php
namespace Weasty\Bundle\GeonamesBundle\Twig;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CityExtension
 * @package Weasty\Bundle\GeonamesBundle\Twig
 */
class CityExtension extends \Twig_Extension
    implements ContainerAwareInterface
{

    /**
     * @var \Weasty\Bundle\GeonamesBundle\Entity\CityRepository
     */
    protected $cityRepository;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @param \Weasty\Bundle\GeonamesBundle\Entity\CityRepository $cityRepository
     */
    function __construct($cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    /**
     * @return array
     */
    public function getFunctions(){
        return array(
            new \Twig_SimpleFunction('weasty_geonames_city_locator', array($this, 'cityLocator'), array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('weasty_geonames_city', array($this, 'getCity')),
            new \Twig_SimpleFunction('weasty_geonames_cities', array($this, 'getCities')),
        );
    }

    /**
     * @return string
     */
    public function cityLocator(){

        /**
         * @var $twig \Twig_Environment
         */
        $twig = $this->getContainer()->get('twig');
        return $twig->render('WeastyGeonamesBundle:City:cityLocator.html.twig');

    }

    /**
     * @param $geonameIdentifier
     * @return null|\Weasty\Bundle\GeonamesBundle\Entity\City
     */
    public function getCity($geonameIdentifier){
        return $geonameIdentifier ? $this->getCityRepository()->findOneBy(array(
            'geonameIdentifier' => $geonameIdentifier
        )) : null;
    }

    /**
     * @param $geonameIdentifiers
     * @return array
     */
    public function getCities($geonameIdentifiers){
        return $geonameIdentifiers ? $this->getCityRepository()->findBy(array(
            'geonameIdentifier' => $geonameIdentifiers
        )) : null;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'weasty_geonames_cities';
    }

    /**
     * @return \Weasty\Bundle\GeonamesBundle\Entity\CityRepository
     */
    public function getCityRepository()
    {
        return $this->cityRepository;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

}