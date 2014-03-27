<?php
namespace Shop\UserBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\ArrayCollection;
use Shop\UserBundle\Entity\AbstractUser;
use Shop\UserBundle\Entity\UserGroup;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateUserGroupCommand
 * @package Shop\UserBundle\Command
 */
class CreateUserGroupCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('user:createGroup')
            ->addOption('removeCustom', 'removeCustom', InputOption::VALUE_OPTIONAL, 'Remove groups that does not exits in default groups list')
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
        if(!$doctrine instanceof Registry){
            $output->writeln('<error>Doctrine not found</error>');
            return null;
        }

        $defaultGroups = array(
            UserGroup::SLUG_USERS => array(
                'slug' => UserGroup::SLUG_USERS,
                'name' => 'Пользователи',
                'roles' => array(),
            ),
            UserGroup::SLUG_MANAGERS => array(
                'slug' => UserGroup::SLUG_MANAGERS,
                'name' => 'Менеджеры',
                'roles' => array(
                    AbstractUser::ROLE_MANAGER,
                ),
            ),
            UserGroup::SLUG_ACCOUNTANTS => array(
                'slug' => UserGroup::SLUG_ACCOUNTANTS,
                'name' => 'Бухгалтеры',
                'roles' => array(
                    AbstractUser::ROLE_ACCOUNTANT,
                ),
            ),
            UserGroup::SLUG_CATALOG_ADMINS => array(
                'slug' => UserGroup::SLUG_CATALOG_ADMINS,
                'name' => 'Администраторы каталога',
                'roles' => array(
                    AbstractUser::ROLE_CATALOG_ADMIN,
                ),
            ),
            UserGroup::SLUG_ADMINS => array(
                'slug' => UserGroup::SLUG_ADMINS,
                'name' => 'Администраторы',
                'roles' => array(
                    AbstractUser::ROLE_ADMIN,
                ),
            ),
        );
        $defaultGroupsCollection = new ArrayCollection($defaultGroups);

        $em = $doctrine->getManager();
        $repository = $doctrine->getRepository('ShopUserBundle:UserGroup');

        $groups = $repository->findAll();
        $removeCustomGroups = $input->getOption('removeCustom');

        foreach($groups as $group){

            if($group instanceof UserGroup){

                $defaultGroup = $defaultGroupsCollection->get($group->getSlug());
                if($defaultGroup){

                    foreach($defaultGroup as $name => $value){
                        $group[$name] = $value;
                    }

                    $defaultGroupsCollection->remove($group->getSlug());

                } else {

                    if($removeCustomGroups){
                        $em->remove($group);
                    }

                }

            }

        }

        foreach($defaultGroupsCollection as $defaultGroup){

            $group = new UserGroup();
            foreach($defaultGroup as $name => $value){
                $group[$name] = $value;
            }
            $em->persist($group);

        }

        $em->flush();

    }

} 