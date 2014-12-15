<?php
namespace Shop\CatalogBundle\Command;

use Application\Sonata\MediaBundle\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateProposalsMediaCommand
 * @package Shop\CatalogBundle\Command
 */
class UpdateProposalsMediaCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('shop:catalog:update-proposals-media')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        /**
         * @var \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
         */
        $doctrine = $this->getContainer()->get('doctrine');

        $mediaCommand = $this->getApplication()->get('sonata:media:add');

        /**
         * @var \Shop\CatalogBundle\Entity\ProposalRepository $proposalRepository
         */
        $proposalRepository = $this->getContainer()->get('shop_catalog.proposal.repository');

        /**
         * @var \Sonata\MediaBundle\Entity\MediaManager $mediaManager
         */
        $mediaManager = $this->getContainer()->get('sonata.media.manager.media');

        /**
         * @var \Shop\CatalogBundle\Entity\Proposal[] $proposals
         */
        $proposals = $proposalRepository->findAll();

        $uploadDirectoryPath = realpath(__DIR__ . '/../../../../web/uploads');

        foreach($proposals as $proposal){

            /**
             * @var \Doctrine\DBAL\Connection $connection
             */
            $connection = $doctrine->getConnection();
            $sql = "
                SELECT
                  i.imageFileName,
                  i.thumbImageFileName
                FROM ProposalImage AS pi
                JOIN Image AS i ON i.id = pi.id
                JOIN Proposal AS p ON p.id = pi.proposalId
                WHERE p.id = :proposal_id
                GROUP BY pi.id
            ";
            $images = $connection->fetchAll($sql, ['proposal_id'=>$proposal->getId()]);

            if($images){

                foreach($images as $image){

                    $thumbImagePath = $uploadDirectoryPath . '/' . $image['thumbImageFileName'];
                    $imagePath = file_exists($thumbImagePath) ? $thumbImagePath : ($uploadDirectoryPath . '/' . $image['imageFileName']);

                    if(file_exists($imagePath)){

                        $mediaCommandInput = new ArrayInput(array(
                            'command' => 'sonata:media:add',
                            'providerName' => 'sonata.media.provider.image',
                            'context' => 'image',
                            'binaryContent' => $imagePath,
                        ));

                        $media = $mediaManager->findOneBy(array(
                            'providerName' => $mediaCommandInput->getParameterOption('providerName'),
                            'context' => $mediaCommandInput->getParameterOption('context'),
                            'name' => pathinfo($imagePath, PATHINFO_BASENAME),
                        ));

                        if($media instanceof Media){

                            if(!$proposal->getMediaImages()->contains($media)){
                                $proposal->getMediaImages()->add($media);
                            }

                        } else {

                            $mediaCommand->run($mediaCommandInput, $output);
                            $media = $mediaManager->findOneBy(array(
                                'providerName' => $mediaCommandInput->getParameterOption('providerName'),
                                'context' => $mediaCommandInput->getParameterOption('context'),
                                'name' => pathinfo($imagePath, PATHINFO_BASENAME),
                            ));

                            if($media instanceof Media){
                                $proposal->getMediaImages()->add($media);
                            }

                        }

                    }

                }

            }

            $doctrine->getManager()->flush();

        }

    }

}