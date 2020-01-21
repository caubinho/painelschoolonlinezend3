<?php

namespace Acl;

use Acl\Controller\Factory\PrivilegesControllerFactory;
use Acl\Controller\Factory\ResourcesControllerFactory;
use Acl\Controller\PrivilegesController;
use Acl\Controller\ResourcesController;
use Acl\Controller\RoleController;
use Acl\Controller\Factory\RoleControllerFactory;
use Acl\Entity\Role;

use Acl\Form\Factory\PrivilegeFormFactory;
use Acl\Form\Factory\RoleFormFactory;
use Acl\Form\PrivilegeForm;
use Acl\Form\RoleForm as RoleFrm;
use Acl\Form\RoleForm;
use Acl\Service\Factory\PrivilegeManagerFactory;
use Acl\Service\Factory\ResourceManagerFactory;
use Acl\Service\Factory\RoleManagerFactory;
use Acl\Service\PrivilegeManager;
use Acl\Service\ResourceManager;
use Acl\Service\RoleManager;
use Doctrine\ORM\EntityManager;



/**
 * Class Module
 * @package Acl
 * @author Euclécio Josias Rodrigues <eucjosias@gmail.com>
 */
class Module
{
    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                Permissions\Acl::class => function($container) {
                    /* Get ACL from database */
					$em         = $container->get(\Doctrine\ORM\EntityManager::class);
					$roles      = $em->getRepository(\Acl\Entity\Role::class)->findAll();
					$resources  = $em->getRepository(\Acl\Entity\Resource::class)->findAll();
					$privileges = $em->getRepository(\Acl\Entity\Privilege::class)->findAll();

                    return new Permissions\Acl($roles, $resources, $privileges);
                },

                RoleForm::class => RoleFormFactory::class,
                RoleManager::class => RoleManagerFactory::class,

                ResourceManager::class => ResourceManagerFactory::class,
                
                PrivilegeManager::class => PrivilegeManagerFactory::class,
                PrivilegeForm::class => PrivilegeFormFactory::class,
            )
        );
    }
    
    public function getControllerConfig()
    {
        return [
            'factories' => [
                RoleController::class => RoleControllerFactory::class,
                ResourcesController::class => ResourcesControllerFactory::class,
                PrivilegesController::class => PrivilegesControllerFactory::class,


            ],

            'aliases' => [
                'roles'         => RoleController::class,
                'resources'     => ResourcesController::class,
                'privileges'    => PrivilegesController::class,
            ]

        ];
    }
}
