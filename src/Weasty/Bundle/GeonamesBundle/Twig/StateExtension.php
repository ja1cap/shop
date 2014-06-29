<?php
namespace Weasty\Bundle\GeonamesBundle\Twig;

/**
 * Class StateExtension
 * @package Weasty\Bundle\GeonamesBundle\Twig
 */
class StateExtension extends \Twig_Extension{

    /**
     * @var \Weasty\Bundle\GeonamesBundle\Entity\StateRepository
     */
    protected $stateRepository;

    /**
     * @param \Weasty\Bundle\GeonamesBundle\Entity\StateRepository $stateRepository
     */
    function __construct($stateRepository)
    {
        $this->stateRepository = $stateRepository;
    }

    /**
     * @return array
     */
    public function getFunctions(){
        return array(
            new \Twig_SimpleFunction('weasty_geonames_state', array($this, 'getState')),
            new \Twig_SimpleFunction('weasty_geonames_states', array($this, 'getStates')),
        );
    }

    /**
     * @param $geonameIdentifier
     * @return null|\Weasty\Bundle\GeonamesBundle\Entity\State
     */
    public function getState($geonameIdentifier){
        return $geonameIdentifier ? $this->getStateRepository()->findOneBy(array(
            'geonameIdentifier' => $geonameIdentifier
        )) : null;
    }

    /**
     * @param $geonameIdentifiers
     * @return array
     */
    public function getStates($geonameIdentifiers){
        return $geonameIdentifiers ? $this->getStateRepository()->findBy(array(
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
        return 'weasty_geonames_states';
    }

    /**
     * @return \Weasty\Bundle\GeonamesBundle\Entity\StateRepository
     */
    public function getStateRepository()
    {
        return $this->stateRepository;
    }

} 