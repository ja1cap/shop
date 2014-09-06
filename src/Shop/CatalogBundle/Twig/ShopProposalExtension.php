<?php
namespace Shop\CatalogBundle\Twig;

use Shop\CatalogBundle\Entity\Proposal;

/**
 * Class ShopProposalExtension
 * @package Shop\CatalogBundle\Twig
 */
class ShopProposalExtension extends \Twig_Extension {

    /**
     * @var \Shop\CatalogBundle\Entity\ProposalRepository
     */
    protected $proposalRepository;

    /**
     * @var \Shop\CatalogBundle\Entity\PopularProposalRepository
     */
    protected $popularProposalRepository;

    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var \Shop\CatalogBundle\Filter\FiltersBuilder
     */
    protected $filtersBuilder;

    /**
     * @param $proposalRepository
     * @param $popularProposalRepository
     * @param $urlGenerator
     * @param $filtersBuilder
     */
    function __construct($proposalRepository, $popularProposalRepository, $urlGenerator, $filtersBuilder)
    {
        $this->proposalRepository = $proposalRepository;
        $this->popularProposalRepository = $popularProposalRepository;
        $this->urlGenerator = $urlGenerator;
        $this->filtersBuilder = $filtersBuilder;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('shop_catalog_proposal_url', array($this, 'getProposalUrl')),
            new \Twig_SimpleFunction('shop_catalog_popular_proposals', array($this, 'getPopularProposals')),
            new \Twig_SimpleFunction('shop_catalog_new_proposals', array($this, 'getNewProposals')),
            new \Twig_SimpleFunction('shop_catalog_bestsellers', array($this, 'getBestsellers')),
            new \Twig_SimpleFunction('shop_catalog_discount_proposals', array($this, 'getDiscountProposals')),
            new \Twig_SimpleFunction('shop_catalog_action_proposals', array($this, 'getActionProposals')),
        );
    }

    /**
     * @param $proposal
     * @return null|string
     */
    public function getProposalUrl($proposal){

        $url = null;

        if($proposal instanceof Proposal){

            $url = $this->urlGenerator->generate('shop_catalog_proposal', $proposal->getRouteParameters());

        }

        return $url;

    }

    /**
     * @return \Shop\CatalogBundle\Entity\Proposal[]
     */
    public function getPopularProposals(){
        $proposals = array_filter(
            array_map(
                function($popularProposal){
                    $proposal = $popularProposal['proposal'];
                    if($proposal instanceof Proposal && $proposal->getStatus() == Proposal::STATUS_ON){
                        return $proposal;
                    }
                    return null;
                },
                $this->popularProposalRepository->findProposals()
            )
        );

        return $proposals;
    }

    /**
     * @return array
     */
    public function getNewProposals(){

        $filtersResource = $this->filtersBuilder->build(null, null, null, null, null, [
            'isNew' => true,
        ]);

        return $this->proposalRepository->findProposalsByFilters($filtersResource, 1, 8);

    }

    /**
     * @return array
     */
    public function getBestsellers(){

        $filtersResource = $this->filtersBuilder->build(null, null, null, null, null, [
            'isBestseller' => true,
        ]);

        return $this->proposalRepository->findProposalsByFilters($filtersResource, 1, 8);

    }

    /**
     * @return array
     */
    public function getDiscountProposals(){

        $filtersResource = $this->filtersBuilder->build(null, null, null, null, null, [
            'hasDiscount' => true,
        ]);

        return $this->proposalRepository->findProposalsByFilters($filtersResource, 1, 8);

    }

    /**
     * @return array
     */
    public function getActionProposals(){

        $filtersResource = $this->filtersBuilder->build(null, null, null, null, null, [
            'hasAction' => true,
        ]);

        return $this->proposalRepository->findProposalsByFilters($filtersResource, 1, 8);

    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'shop_catalog_proposal';
    }

} 