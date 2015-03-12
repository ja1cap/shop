<?php
namespace Weasty\Bundle\AdBundle\Banner;

/**
 * Interface BannerInterface
 * @package Weasty\Bundle\AdBundle\Banner
 */
interface BannerInterface {

  const TYPE_URL = 1;
  const TYPE_PROPOSAL = 2;

  /**
   * @return int
   */
  public function getType();

  /**
   * @return null|\Sonata\MediaBundle\Model\MediaInterface
   */
  public function getImage();

  /**
   * @return string
   */
  public function getTitle();

}