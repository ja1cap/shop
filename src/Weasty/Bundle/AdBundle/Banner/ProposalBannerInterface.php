<?php
namespace Weasty\Bundle\AdBundle\Banner;

/**
 * Interface ProposalBannerInterface
 * @package Weasty\Bundle\AdBundle\Banner
 */
interface ProposalBannerInterface extends BannerInterface {
  /**
   * @return int
   */
  public function getProposalId();

  /**
   * @return \Weasty\Bundle\CatalogBundle\Proposal\ProposalInterface
   */
  public function getProposal();

}