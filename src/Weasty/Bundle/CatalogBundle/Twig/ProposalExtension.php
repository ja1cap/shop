<?php
namespace Weasty\Bundle\CatalogBundle\Twig;

use Doctrine\Common\Persistence\ObjectRepository;
use Weasty\Bundle\CatalogBundle\Data\ProposalInterface;

/**
 * Class ProposalExtension
 * @package Weasty\Bundle\CatalogBundle\Twig
 */
class ProposalExtension extends \Twig_Extension {

    /**
     * @var ObjectRepository
     */
    protected $proposalRepository;

    /**
     * @param ObjectRepository $proposalRepository
     */
    function __construct(ObjectRepository $proposalRepository)
    {
        $this->proposalRepository = $proposalRepository;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('weasty_catalog_proposal_names', array($this, 'getProposalNames')),
        );
    }

    /**
     * @return array
     */
    public function getFunctions(){
        return array(
            new \Twig_SimpleFunction('weasty_catalog_proposal', array($this, 'getProposal')),
            new \Twig_SimpleFunction('weasty_catalog_proposals', array($this, 'getProposals')),
            new \Twig_SimpleFunction('weasty_catalog_proposal_names', array($this, 'getProposalNames')),
        );
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getProposalRepository()
    {
        return $this->proposalRepository;
    }

    /**
     * @param $id
     * @return null|object
     */
    public function getProposal($id){
        return $id ? $this->getProposalRepository()->findOneBy(array('id' => $id)) : null;
    }

    /**
     * @param $ids
     * @return array|null
     */
    public function getProposals($ids){
        return $ids ? $this->getProposalRepository()->findBy(array('id' => $ids)) : null;
    }

    /**
     * @param $proposals
     * @return null|string
     */
    public function getProposalNames($proposals){

        if(is_numeric(current($proposals))){
            $proposals = $this->getProposals(array_filter($proposals, function($id){
                return is_numeric($id);
            }));
        }

        if(!$proposals){
            return null;
        }

        $names = array_filter(array_map(function($proposal){
            return $proposal instanceof ProposalInterface ? $proposal->getName() : null;
        }, $proposals));

        asort($names);

        return implode(', ', $names);

    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'weasty_catalog_proposal';
    }

} 