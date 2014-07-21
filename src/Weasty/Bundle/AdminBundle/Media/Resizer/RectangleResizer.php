<?php
namespace Weasty\Bundle\AdminBundle\Media\Resizer;


use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Gaufrette\File;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Metadata\MetadataBuilderInterface;
use Sonata\MediaBundle\Resizer\ResizerInterface;

/**
 * Class RectangleResizer
 * @package Weasty\Bundle\AdminBundle\Media\Resizer
 */
class RectangleResizer implements ResizerInterface {

    /**
     * ImagineInterface
     */
    protected $adapter;

    /**
     * string
     */
    protected $mode;

    /**
     * @param ImagineInterface $adapter
     * @param string $mode
     * @param MetadataBuilderInterface $metadata
     */
    public function __construct(ImagineInterface $adapter, $mode, MetadataBuilderInterface $metadata)
    {
        $this->adapter = $adapter;
        $this->mode    = $mode;
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function resize(MediaInterface $media, File $in, File $out, $format, array $settings)
    {

        $shape = $this->getShape($media, $settings);

        switch($shape){
            case 'square':
                $this->resizeSquare($media, $in, $out, $format, $settings);
                break;
            default:
                $this->resizeRectangle($media, $in, $out, $format, $settings);
        }

    }

    /**
     * @param MediaInterface $media
     * @param array $settings
     * @return string
     */
    private function getShape(MediaInterface $media, array $settings){

        if($settings['width'] != null && $settings['height'] != null && $settings['width'] != $settings['height']){
            $shape = 'rectangle';
        } else {
            $shape = 'square';
        }

        return $shape;

    }

    /**
     * @param MediaInterface $media
     * @param File $in
     * @param File $out
     * @param $format
     * @param array $settings
     */
    private function resizeRectangle(MediaInterface $media, File $in, File $out, $format, array $settings){

        if (!isset($settings['width'])) {
            throw new \RuntimeException(sprintf('Width parameter is missing in context "%s" for provider "%s"', $media->getContext(), $media->getProviderName()));
        }

        $image = $this->adapter->load($in->getContent());

        if(!$settings['height']){
            $settings['height'] = $settings['width'];
        }

        $thumbnailSize = new Box($settings['width'], $settings['height']);
        $thumbnail = $image->thumbnail($thumbnailSize, ImageInterface::THUMBNAIL_OUTBOUND);
        $content = $thumbnail->get($format, array('quality' => $settings['quality']));

        $out->setContent($content, $this->metadata->get($media, $out->getName()));

    }

    /**
     * @param MediaInterface $media
     * @param File $in
     * @param File $out
     * @param $format
     * @param array $settings
     */
    private function resizeSquare(MediaInterface $media, File $in, File $out, $format, array $settings)
    {
        if (!isset($settings['width'])) {
            throw new \RuntimeException(sprintf('Width parameter is missing in context "%s" for provider "%s"', $media->getContext(), $media->getProviderName()));
        }

        $image = $this->adapter->load($in->getContent());
        $size  = $media->getBox();

        if (null != $settings['height']) {
            if ($size->getHeight() > $size->getWidth()) {
                $higher = $size->getHeight();
                $lower  = $size->getWidth();
            } else {
                $higher = $size->getWidth();
                $lower  = $size->getHeight();
            }

            $crop = $higher - $lower;

            if ($crop > 0) {
                $point = $higher == $size->getHeight() ? new Point(0, 0) : new Point($crop / 2, 0);
                $image->crop($point, new Box($lower, $lower));
                $size = $image->getSize();
            }
        }

        $settings['height'] = (int) ($settings['width'] * $size->getHeight() / $size->getWidth());

        if ($settings['height'] < $size->getHeight() && $settings['width'] < $size->getWidth()) {
            $content = $image
                ->thumbnail(new Box($settings['width'], $settings['height']), $this->mode)
                ->get($format, array('quality' => $settings['quality']));
        } else {
            $content = $image->get($format, array('quality' => $settings['quality']));
        }

        $out->setContent($content, $this->metadata->get($media, $out->getName()));
    }

    /**
     * {@inheritdoc}
     */
    public function getBox(MediaInterface $media, array $settings)
    {

        $shape = $this->getShape($media, $settings);

        switch($shape){
            case 'square':
                return $this->getSquareBox($media, $settings);
                break;
            default:
                return $this->getRectangleBox($media, $settings);
        }

    }

    /**
     * @param MediaInterface $media
     * @param array $settings
     * @return Box
     */
    private function getRectangleBox(MediaInterface $media, array $settings){

        $size = $media->getBox();

        if ($settings['width'] == null && $settings['height'] == null) {
            throw new \RuntimeException(sprintf('Width/Height parameter is missing in context "%s" for provider "%s". Please add at least one parameter.', $media->getContext(), $media->getProviderName()));
        }

        if ($settings['height'] == null) {
            $settings['height'] = (int) ($settings['width'] * $size->getHeight() / $size->getWidth());
        }

        if ($settings['width'] == null) {
            $settings['width'] = (int) ($settings['height'] * $size->getWidth() / $size->getHeight());
        }

        return new Box($settings['width'], $settings['height']);

    }

    /**
     * @param MediaInterface $media
     * @param array $settings
     * @return Box
     */
    private function getSquareBox(MediaInterface $media, array $settings){

        $size = $media->getBox();

        if (null != $settings['height']) {

            if ($size->getHeight() > $size->getWidth()) {
                $higher = $size->getHeight();
                $lower  = $size->getWidth();
            } else {
                $higher = $size->getWidth();
                $lower  = $size->getHeight();
            }

            if ($higher - $lower > 0) {
                return new Box($lower, $lower);
            }
        }

        $settings['height'] = (int) ($settings['width'] * $size->getHeight() / $size->getWidth());

        if ($settings['height'] < $size->getHeight() && $settings['width'] < $size->getWidth()) {
            return new Box($settings['width'], $settings['height']);
        }

        return $size;

    }

} 