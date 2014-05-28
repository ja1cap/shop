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

        foreach($proposals as $proposal){

            if(!$proposal->getImages()->isEmpty()){

                /**
                 * @var \Shop\CatalogBundle\Entity\ProposalImage[] $images
                 */
                $images = $proposal->getImages();
                foreach($images as $image){

                    $imagePath = $image->getFilePath($image->getImageFileName());

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
                            'name' => $image->getImageFileName(),
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
                                'name' => $image->getImageFileName(),
                            ));

                            if($media instanceof Media){
                                $proposal->getMediaImages()->add($media);
                            }

                        }

                    }

                }

            }

            $mainImage = $proposal->getMainImage();
            if($mainImage){

                $imagePath = $mainImage->getFilePath($mainImage->getImageFileName());

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
                        'name' => $mainImage->getImageFileName(),
                    ));

                    if($media instanceof Media){

                        if($proposal->getMainMediaImageId() != $media->getId()){
                            $proposal->setMainMediaImage($media);
                        }

                    } else {

                        $mediaCommand->run($mediaCommandInput, $output);
                        $media = $mediaManager->findOneBy(array(
                            'providerName' => $mediaCommandInput->getParameterOption('providerName'),
                            'context' => $mediaCommandInput->getParameterOption('context'),
                            'name' => $mainImage->getImageFileName(),
                        ));

                        if($media instanceof Media){
                            $proposal->setMainMediaImage($media);
                        }

                    }

                }

            }

            $doctrine->getManager()->flush();

        }

    }

}