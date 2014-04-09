<?php
namespace Weasty\MoneyBundle\Converter;
use Weasty\MoneyBundle\Data\CurrencyResource;

/**
 * Class CurrencyTypeCodeConverterFactory
 * @package Weasty\MoneyBundle\Converter
 */
class CurrencyTypeCodeConverterFactory {

    /**
     * @var \Weasty\MoneyBundle\Data\CurrencyResource
     */
    protected $currencyResource;

    /**
     * @var array
     */
    protected $converters = array();

    /**
     * @var array
     */
    protected $converterClassNames = array();

    /**
     * @param CurrencyResource $currencyResource
     * @param $converterClassNames
     */
    function __construct(CurrencyResource $currencyResource, $converterClassNames)
    {
        $this->currencyResource = $currencyResource;
        foreach($converterClassNames as $converterClassName){
            $this->addConverterClassName($converterClassName);
        }
    }

    /**
     * @param $converterClassName
     * @return $this
     * @throws \Exception
     */
    public function addConverterClassName($converterClassName){

        $type = call_user_func(array($converterClassName, 'getType'));

        if($type){
            $this->converterClassNames[$type] = $converterClassName;
        } else {
            throw new \Exception(sprintf('Currency converter %s::getType returns null', $converterClassName));
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getConverterTypes(){
        return array_keys($this->converterClassNames);
    }

    /**
     * @param $type
     * @return CurrencyCodeType\CurrencyTypeCodeConverterInterface
     * @throws \Exception
     */
    public function createConverter($type){

        if(isset($this->converters[$type])){
            return $this->converters[$type];
        }

        if(!isset($this->converterClassNames[$type])){
            throw new \Exception(sprintf('Converter with type %s not found', $type));
        }

        $interfaceName = 'Weasty\MoneyBundle\Converter\CurrencyCodeType\CurrencyTypeCodeConverterInterface';
        $className = $this->converterClassNames[$type];
        $reflection = new \ReflectionClass($className);


        if($reflection->implementsInterface($interfaceName)){

            /**
             * @var $converter \Weasty\MoneyBundle\Converter\CurrencyCodeType\CurrencyTypeCodeConverterInterface
             */
            $converter = $reflection->newInstance($this->getCurrencyResource());

            $this->converters[$converter->getType()] = $converter;
            return $converter;

        } else {

            throw new \Exception(sprintf('%s must implement %s', $className, $interfaceName));

        }

    }

    /**
     * @return \Weasty\MoneyBundle\Data\CurrencyResource
     */
    public function getCurrencyResource()
    {
        return $this->currencyResource;
    }

} 