<?php
namespace Application\Sonata\MediaBundle\Element;

use Sonata\MediaBundle\Model\MediaInterface;
use Weasty\Doctrine\Cache\Collection\CacheCollectionElement;

/**
 * Class MediaElement
 * @package Application\Sonata\MediaBundle\Element
 */
class MediaElement extends CacheCollectionElement implements MediaInterface {

    /**
     * @param $methodName
     * @return string
     */
    private function _methodOffsetGet($methodName){
        
        $offset = lcfirst(substr($methodName, 3));

        if(!isset($this->data[$offset])){
            return null;
        }

        return $this->data[$offset];

    }

    /**
     * @param mixed $binaryContent
     */
    public function setBinaryContent($binaryContent)
    {
        // TODO: Implement setBinaryContent() method.
    }

    /**
     * @return mixed
     */
    public function getBinaryContent()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * @param string $name
     * @param null $default
     */
    public function getMetadataValue($name, $default = null)
    {
        // TODO: Implement getMetadataValue() method.
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setMetadataValue($name, $value)
    {
        // TODO: Implement setMetadataValue() method.
    }

    /**
     * Remove a named data from the metadata
     *
     * @param string $name
     */
    public function unsetMetadataValue($name)
    {
        // TODO: Implement unsetMetadataValue() method.
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        // TODO: Implement setName() method.
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        // TODO: Implement setDescription() method.
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        // TODO: Implement setEnabled() method.
    }

    /**
     * Get enabled
     *
     * @return boolean $enabled
     */
    public function getEnabled()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * Set provider_name
     *
     * @param string $providerName
     */
    public function setProviderName($providerName)
    {
        // TODO: Implement setProviderName() method.
    }

    /**
     * Get provider_name
     *
     * @return string $providerName
     */
    public function getProviderName()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * Set provider_status
     *
     * @param integer $providerStatus
     */
    public function setProviderStatus($providerStatus)
    {
        // TODO: Implement setProviderStatus() method.
    }

    /**
     * Get provider_status
     *
     * @return integer $providerStatus
     */
    public function getProviderStatus()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * Set provider_reference
     *
     * @param string $providerReference
     */
    public function setProviderReference($providerReference)
    {
        // TODO: Implement setProviderReference() method.
    }

    /**
     * Get provider_reference
     *
     * @return string $providerReference
     */
    public function getProviderReference()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * Set provider_metadata
     *
     * @param array $providerMetadata
     */
    public function setProviderMetadata(array $providerMetadata = array())
    {
        // TODO: Implement setProviderMetadata() method.
    }

    /**
     * Get provider_metadata
     *
     * @return array $providerMetadata
     */
    public function getProviderMetadata()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * Set width
     *
     * @param integer $width
     */
    public function setWidth($width)
    {
        // TODO: Implement setWidth() method.
    }

    /**
     * Get width
     *
     * @return integer $width
     */
    public function getWidth()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * Set height
     *
     * @param integer $height
     */
    public function setHeight($height)
    {
        // TODO: Implement setHeight() method.
    }

    /**
     * Get height
     *
     * @return integer $height
     */
    public function getHeight()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * Set length
     *
     * @param float $length
     */
    public function setLength($length)
    {
        // TODO: Implement setLength() method.
    }

    /**
     * Get length
     *
     * @return float $length
     */
    public function getLength()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * Set copyright
     *
     * @param string $copyright
     */
    public function setCopyright($copyright)
    {
        // TODO: Implement setCopyright() method.
    }

    /**
     * Get copyright
     *
     * @return string $copyright
     */
    public function getCopyright()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * Set authorName
     *
     * @param string $authorName
     */
    public function setAuthorName($authorName)
    {
        // TODO: Implement setAuthorName() method.
    }

    /**
     * Get authorName
     *
     * @return string $authorName
     */
    public function getAuthorName()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * Set context
     *
     * @param string $context
     */
    public function setContext($context)
    {
        // TODO: Implement setContext() method.
    }

    /**
     * Get context
     *
     * @return string $context
     */
    public function getContext()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * Set cdnIsFlushable
     *
     * @param boolean $cdnIsFlushable
     */
    public function setCdnIsFlushable($cdnIsFlushable)
    {
        // TODO: Implement setCdnIsFlushable() method.
    }

    /**
     * Get cdn_is_flushable
     *
     * @return boolean $cdnIsFlushable
     */
    public function getCdnIsFlushable()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * Set cdn_flush_at
     *
     * @param \Datetime $cdnFlushAt
     */
    public function setCdnFlushAt(\Datetime $cdnFlushAt = null)
    {
        // TODO: Implement setCdnFlushAt() method.
    }

    /**
     * Get cdn_flush_at
     *
     * @return \Datetime $cdnFlushAt
     */
    public function getCdnFlushAt()
    {
        // TODO: Implement getCdnFlushAt() method.
    }

    /**
     * Set updated_at
     *
     * @param \Datetime $updatedAt
     */
    public function setUpdatedAt(\Datetime $updatedAt = null)
    {
        // TODO: Implement setUpdatedAt() method.
    }

    /**
     * Get updated_at
     *
     * @return \Datetime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * Set created_at
     *
     * @param \Datetime $createdAt
     */
    public function setCreatedAt(\Datetime $createdAt = null)
    {
        // TODO: Implement setCreatedAt() method.
    }

    /**
     * Get created_at
     *
     * @return \Datetime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * Set content_type
     *
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        // TODO: Implement setContentType() method.
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return pathinfo($this->getProviderReference(), PATHINFO_EXTENSION);
    }

    /**
     * Get content_type
     *
     * @return string $contentType
     */
    public function getContentType()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * Set size
     *
     * @param integer $size
     */
    public function setSize($size)
    {
        // TODO: Implement setSize() method.
    }

    /**
     * Get size
     *
     * @return integer $size
     */
    public function getSize()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * Set cdn_status
     *
     * @param integer $cdnStatus
     */
    public function setCdnStatus($cdnStatus)
    {
        // TODO: Implement setCdnStatus() method.
    }

    /**
     *
     * Get cdn_status
     *
     * @return integer $cdnStatus
     */
    public function getCdnStatus()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

    /**
     * @return \Imagine\Image\Box
     */
    public function getBox()
    {
        // TODO: Implement getBox() method.
    }

    /**
     * @param array $galleryHasMedias
     *
     * @return void
     */
    public function setGalleryHasMedias($galleryHasMedias)
    {
        // TODO: Implement setGalleryHasMedias() method.
    }

    /**
     * @return array
     */
    public function getGalleryHasMedias()
    {
        // TODO: Implement getGalleryHasMedias() method.
    }

    /**
     * @return string
     */
    public function getPreviousProviderReference()
    {
        return $this->_methodOffsetGet(__FUNCTION__);
    }

} 