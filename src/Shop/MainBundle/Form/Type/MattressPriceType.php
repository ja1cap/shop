<?php
namespace Shop\MainBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Shop\MainBundle\Entity\Mattress;
use Shop\MainBundle\Entity\MattressPrice;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MattressPriceType
 * @package Shop\MainBundle\Form\Type
 */
class MattressPriceType extends AbstractPriceType {

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $options;

    /**
     * @var \Shop\MainBundle\Entity\Mattress
     */
    protected $mattress;

    /**
     * @param Mattress $mattress
     * @param array $options
     */
    function __construct(Mattress $mattress, array $options = array())
    {
        $this->mattress = $mattress;
        $this->options = new ArrayCollection($options);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return mixed
     */
    public function buildPriceFormElements(FormBuilderInterface $builder, array $options)
    {

        $choices = MattressPrice::$sizes;

        $this->mattress->getPrices()->map(function(MattressPrice $price) use (&$choices){

            if(isset($choices[$price->getSizeId()])){
                unset($choices[$price->getSizeId()]);
            }

        });

        $builder
            ->add('sizeId', 'choice', array(
                'choices' => $choices,
                'label' => 'Размер (см x см)',
            ));


    }

} 