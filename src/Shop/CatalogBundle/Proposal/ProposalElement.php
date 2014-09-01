<?php
namespace Shop\CatalogBundle\Proposal;

use Shop\CatalogBundle\Entity\Proposal;
use Weasty\Doctrine\Cache\Collection\CacheCollection;
use Weasty\Doctrine\Cache\Collection\CacheCollectionElement;
use Weasty\Doctrine\Entity\EntityInterface;

/**
 * Class ProposalElement
 * @package Shop\CatalogBundle\Proposal
 */
class ProposalElement extends CacheCollectionElement
    implements ProposalInterface
{

    /**
     * @var \Sonata\MediaBundle\Model\MediaInterface
     */
    public $image;

    /**
     * @var \Sonata\MediaBundle\Model\MediaInterface[]
     */
    public $images = [];

    /**
     * @param CacheCollection $collection
     * @param EntityInterface $entity
     * @return $this
     */
    protected function buildData(CacheCollection $collection, EntityInterface $entity)
    {

        $data = parent::buildData($collection, $entity);

        if($entity instanceof Proposal){

            /**
             * @var \Weasty\Doctrine\Cache\Collection\CacheCollection $mediaCollection
             */
            $mediaCollection = $collection->getCollectionManager()->getCollection('ApplicationSonataMediaBundle:Media');

            $data['slug'] = $entity->getSlug();
            $data['routeParameters'] = $entity->getRouteParameters();

            /**
             * @var \Sonata\MediaBundle\Model\MediaInterface[]
             */
            $images = $entity->getImages();
            foreach($images as $image){

                $imageElement = $mediaCollection->saveElement($image);
                if(!$imageElement){
                    continue;
                }

                if($imageElement->getIdentifier() == $entity->getImageId()){
                    $this->image = $imageElement;
                }

                $this->images[] = $imageElement;

            }

        }

        return $data;

    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->data['id'];
    }

    /**
     * @return integer
     */
    public function getCategoryId()
    {
        return $this->data['categoryId'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->data['title'];
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->data['slug'];
    }

    /**
     * @return int|null
     */
    public function getImageId()
    {
        return $this->data['mainMediaImageId'];
    }

    /**
     * @return \Sonata\MediaBundle\Model\MediaInterface
     */
    public function getImage(){
        return $this->image;
    }

    /**
     * @return \Sonata\MediaBundle\Model\MediaInterface[]
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param \Sonata\MediaBundle\Model\MediaInterface[] $images
     * @return $this
     */
    public function setImages($images)
    {
        $this->images = $images;
        return $this;
    }

    /**
     * @return array
     */
    public function getRouteParameters()
    {
        return $this->data['routeParameters'];
    }

    /**
     * @return \Shop\CatalogBundle\Category\CategoryInterface
     */
    public function getCategory()
    {
        // TODO: Implement getCategory() method.
    }

} 