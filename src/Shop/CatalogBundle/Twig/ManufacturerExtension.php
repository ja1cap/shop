<?php
namespace Shop\CatalogBundle\Twig;

/**
 * Class ManufacturerExtension
 * @package Shop\CatalogBundle\Twig
 */
class ManufacturerExtension extends \Twig_Extension {

    /**
     * @TODO refactor
     * @var \Shop\CatalogBundle\Entity\Manufacturer[]
     */
    private $manufacturers;

    /**
     * @var \Shop\CatalogBundle\Entity\ManufacturerRepository
     */
    protected $manufacturerRepository;

    /**
     * @param \Shop\CatalogBundle\Entity\ManufacturerRepository $manufacturerRepository
     */
    function __construct($manufacturerRepository)
    {
        $this->manufacturerRepository = $manufacturerRepository;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('shop_catalog_manufacturers', array($this, 'getManufacturers')),
        );
    }

    /**
     * @return \Shop\CatalogBundle\Entity\Manufacturer[]
     */
    public function getManufacturers(){

        if($this->manufacturers === null){

            $this->manufacturers = $this->manufacturerRepository->getManufacturesWithImages();

        }

        return $this->manufacturers;

    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'shop_catalog_manufacturer';
    }

} 