<?php
namespace Shop\CatalogBundle\Twig;

use Shop\CatalogBundle\Entity\Proposal;

/**
 * Class ProposalExtension
 * @package Shop\CatalogBundle\Twig
 */
class ProposalExtension extends \Twig_Extension {

    /**
     * @var \Shop\CatalogBundle\Entity\ProposalRepository
     */
    protected $proposalRepository;

    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var \Shop\CatalogBundle\Filter\FiltersBuilder
     */
    protected $filtersBuilder;

    /**
     * @var \Shop\CatalogBundle\Proposal\Feature\FeaturesBuilder
     */
    protected $featuresBuilder;

    /**
     * @param $proposalRepository
     * @param $urlGenerator
     * @param $filtersBuilder
     * @param $featuresBuilder
     */
    function __construct($proposalRepository, $urlGenerator, $filtersBuilder, $featuresBuilder)
    {
        $this->proposalRepository = $proposalRepository;
        $this->urlGenerator = $urlGenerator;
        $this->filtersBuilder = $filtersBuilder;
        $this->featuresBuilder = $featuresBuilder;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('shop_catalog_proposal_url', array($this, 'getProposalUrl')),
            new \Twig_SimpleFunction('shop_catalog_new_proposals', array($this, 'getNewProposals')),
            new \Twig_SimpleFunction('shop_catalog_bestsellers', array($this, 'getBestsellers')),
            new \Twig_SimpleFunction('shop_catalog_discount_proposals', array($this, 'getDiscountProposals')),
            new \Twig_SimpleFunction('shop_catalog_action_proposals', array($this, 'getActionProposals')),
            new \Twig_SimpleFunction('shop_catalog_proposal_features', array($this, 'getProposalFeatures')),
        );
    }

    /**
     * @param \Shop\CatalogBundle\Category\CategoryInterface $category
     * @param \Shop\CatalogBundle\Price\ProposalPriceInterface[]|\Shop\CatalogBundle\Price\ProposalPriceInterface $prices
     * @return \Weasty\Bundle\CatalogBundle\Feature\FeaturesResourceInterface
     */
    public function getProposalFeatures($category, $prices){
        return $this->featuresBuilder->build($category, $prices);
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
     * @return array
     */
    public function getNewProposals(){

        $filtersResource = $this->filtersBuilder->build(null, null, null, null, null, [
            'isNew' => true,
        ]);

        return $this->proposalRepository->findProposalsByFilters($filtersResource, 1, 8, ['RAND()']);

    }

    /**
     * @return array
     */
    public function getBestsellers(){

        $filtersResource = $this->filtersBuilder->build(null, null, null, null, null, [
            'isBestseller' => true,
        ]);

        return $this->proposalRepository->findProposalsByFilters($filtersResource, 1, 8, ['RAND()']);

    }

    /**
     * @return array
     */
    public function getDiscountProposals(){

        $filtersResource = $this->filtersBuilder->build(null, null, null, null, null, [
            'hasDiscount' => true,
        ]);

        return $this->proposalRepository->findProposalsByFilters($filtersResource, 1, 8, ['RAND()']);

    }

    /**
     * @return array
     */
    public function getActionProposals(){

        $filtersResource = $this->filtersBuilder->build(null, null, null, null, null, [
            'hasAction' => true,
        ]);

        return $this->proposalRepository->findProposalsByFilters($filtersResource, 1, 8, ['RAND()']);

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