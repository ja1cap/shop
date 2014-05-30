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
     * @param $proposalRepository
     * @param $popularProposalRepository
     * @param $urlGenerator
     */
    function __construct($proposalRepository, $popularProposalRepository, $urlGenerator)
    {
        $this->proposalRepository = $proposalRepository;
        $this->popularProposalRepository = $popularProposalRepository;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('shop_catalog_proposal_url', array($this, 'getProposalUrl')),
            new \Twig_SimpleFunction('shop_catalog_popular_proposals', array($this, 'getPopularProposals')),
        );
    }

    /**
     * @param $proposal
     * @return null|string
     */
    public function getProposalUrl($proposal){

        $url = null;

        if($proposal instanceof Proposal){

            $url = $this->urlGenerator->generate('shop_catalog_proposal', array(
                'categorySlug' => $proposal->getCategory()->getSlug(),
                'slug' => $proposal->getSeoSlug() ?: $proposal->getId(),
            ));

        }

        return $url;

    }

    /**
     * @return array
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
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'shop_catalog_proposal';
    }

} 