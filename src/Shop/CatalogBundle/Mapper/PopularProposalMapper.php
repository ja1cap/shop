<?php
namespace Shop\CatalogBundle\Mapper;

use Shop\CatalogBundle\Entity\PopularProposal;
use Shop\CatalogBundle\Entity\Proposal;

/**
 * Class PopularProposalMapper
 * @package Shop\CatalogBundle\Mapper
 */
class PopularProposalMapper {

    /**
     * @var \Shop\CatalogBundle\Entity\PopularProposal
     */
    protected $popularProposal;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $proposalRepository;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $popularProposalRepository;

    /**
     * @param \Shop\CatalogBundle\Entity\PopularProposal $popularProposal
     * @param \Doctrine\Common\Persistence\ObjectRepository $popularProposalRepository
     * @param \Doctrine\Common\Persistence\ObjectRepository $proposalRepository
     */
    function __construct(PopularProposal $popularProposal, $popularProposalRepository, $proposalRepository)
    {

        $this->popularProposal = $popularProposal;
        $this->popularProposalRepository = $popularProposalRepository;
        $this->proposalRepository = $proposalRepository;

        if($this->popularProposal->getPosition() === null){
            $this->popularProposal->setPosition(count($this->popularProposalRepository->findAll()));
        }

    }

    /**
     * @param Proposal $proposal
     * @return $this
     */
    public function setProposal(Proposal $proposal){
        $this->popularProposal->setProposalId($proposal->getId());
        return $this;
    }

    /**
     * @return null|Proposal
     */
    public function getProposal(){
        if(!$this->popularProposal->getProposalId()){
            return null;
        }
        return $this->proposalRepository->findOneBy(array(
            'id' => $this->popularProposal->getProposalId(),
        ));
    }

} 