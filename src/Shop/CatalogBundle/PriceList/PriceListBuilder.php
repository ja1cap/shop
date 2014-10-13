<?php
namespace Shop\CatalogBundle\PriceList;

use Doctrine\Common\Persistence\ObjectManager;
use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\CategoryParameter;
use Shop\CatalogBundle\Entity\ParameterValue;
use Shop\CatalogBundle\Entity\Price;
use Shop\CatalogBundle\Entity\PriceList;
use Shop\CatalogBundle\Entity\PriceListAlias;
use Shop\CatalogBundle\Entity\Proposal;

/**
 * Class PriceListBuilder
 * @package Shop\CatalogBundle\PriceList
 */
class PriceListBuilder {

    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * @var PriceList
     */
    protected $priceList;

    /**
     * @var \PHPExcel
     */
    protected $objPHPExcel;

    /**
     * @param ObjectManager|object $em
     */
    function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * @param $categoryId
     * @param $contractorIds
     * @param $manufacturersIds
     * @return $this
     * @throws \Exception
     */
    public function build($categoryId, $contractorIds, $manufacturersIds){

        $categoryRepository = $this->em->getRepository('ShopCatalogBundle:Category');
        $category = $categoryRepository->findOneBy(array(
            'id' => (int)$categoryId,
        ));

        if(!$category instanceof Category){
            throw new \Exception('Category not found', 404);
        }

        $priceList = new PriceList();
        $priceList
            ->setStatus(PriceList::STATUS_CREATED)
            ->setName('Прайс лист')
            ->setCreateDate(new \DateTime())
            ->setUpdateDate(new \DateTime())
        ;

        /**
         * @var $proposalRepository \Shop\CatalogBundle\Entity\ProposalRepository
         */
        $proposalRepository = $this->em->getRepository('ShopCatalogBundle:Proposal');
        $proposals = $proposalRepository->findProposals($category->getId(), $contractorIds, $manufacturersIds);

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $activeSheet = $objPHPExcel->getActiveSheet();

        $aliasesEntitiesMap = PriceListAlias::getEntitiesAliasesMap();

        $identifiersRowIndex = 1;
        $i = 0;

        foreach($aliasesEntitiesMap as $alias => $aliasEntityMap){

            $aliasTitle = PriceListAlias::getAliasesTitle($alias);
            $cell = $activeSheet->setCellValueByColumnAndRow($i, $identifiersRowIndex, $aliasTitle, true);

            $priceListAlias = new PriceListAlias();
            $priceListAlias
                ->setAliasName($alias)
                ->setColumnName($cell->getColumn())
            ;

            $priceList->addAlias($priceListAlias);
            $i++;

        }

        $categoryParameterColumns = array();
        $categoryParameterColumnIndex = count($aliasesEntitiesMap);

        foreach($category->getParameters() as $categoryParameter){

            if($categoryParameter instanceof CategoryParameter){

                $cell = $activeSheet->setCellValueByColumnAndRow($categoryParameterColumnIndex, $identifiersRowIndex, $categoryParameter->getParameter()->getName(), true);

                $categoryParameterColumns[$categoryParameter->getParameterId()] = $categoryParameterColumnIndex;
                $categoryParameterColumnIndex++;

                $priceListAlias = new PriceListAlias();
                $priceListAlias
                    ->setAliasName(PriceListAlias::ALIAS_PARAMETER_PREFIX . $categoryParameter->getParameterId())
                    ->setColumnName($cell->getColumn())
                ;

                $priceList->addAlias($priceListAlias);

            }

        }

        foreach($proposals as $proposal){

            if(!$proposal instanceof Proposal){
                continue;
            }

            $prices = $proposal->getPrices();

            foreach($prices as $price){

                if(!$price instanceof Price){
                    continue;
                }

                $identifiersRowIndex++;

                $i = 0;

                foreach($aliasesEntitiesMap as $aliasEntityMap){

                    $value = null;
                    $property = $aliasEntityMap['property'];

                    switch($aliasEntityMap['entity']){
                        case 'proposal':

                            if(isset($proposal[$property])){
                                $value = $proposal[$property];
                            }

                            break;

                        case 'price':

                            if(isset($aliasEntityMap[$property])){

                                switch($aliasEntityMap[$property]){
                                    default:
                                        $value = $price[$property];
                                }

                            }

                            break;

                    }

                    $activeSheet->setCellValueByColumnAndRow($i, $identifiersRowIndex, $value);

                    $i++;

                }

                foreach($price->getParameterValues() as $parameterValue){

                    if($parameterValue instanceof ParameterValue){

                        if(isset($categoryParameterColumns[$parameterValue->getParameterId()])){

                            $parameterColumn = $categoryParameterColumns[$parameterValue->getParameterId()];
                            $activeSheet->setCellValueByColumnAndRow($parameterColumn, $identifiersRowIndex, $parameterValue->getOption()->getName());

                        }

                    }

                }

            }

        }

        $fileName = md5('priceList' . microtime()) . '.xlsx';
        $priceList->setFileName($fileName);

        $this->priceList = $priceList;
        $this->objPHPExcel = $objPHPExcel;

        return $this;

    }

    /**
     * @return \PHPExcel
     */
    public function getObjPHPExcel()
    {
        return $this->objPHPExcel;
    }

    /**
     * @return \Shop\CatalogBundle\Entity\PriceList
     */
    public function getPriceList()
    {
        return $this->priceList;
    }

} 