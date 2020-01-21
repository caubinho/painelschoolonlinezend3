<?php

namespace Acl\Form;

use Acl\Entity\Privilege;
use Acl\Entity\Resource;
use Acl\Entity\Role;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Form\Element\ObjectSelect;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;


use Zend\InputFilter\InputFilterProviderInterface;


use Zend\Form\Element\Hidden;
use Zend\Form\Form;

use Zend\InputFilter\InputFilter;

use Zend\Validator\Hostname;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;


/**
 * This form is used to collect user's email, full name, password and status. The form
 * can work in two scenarios - 'create' and 'update'. In 'create' scenario, user
 * enters password, in 'update' scenario he/she doesn't enter password.
 */
class PrivilegeForm extends Form implements InputFilterProviderInterface
{
    protected $objectManager = null;

    /**
     * Constructor.
     */
    public function __construct(ObjectManager $objectManager)
    {
        // Define form name
        parent::__construct('privilege-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal form-label-left');

        $this->setHydrator(new DoctrineObject($objectManager, 'Acl\Entity\Privilege'));
        $this->setObject(new Privilege());

        $this->objectManager = $objectManager;


        $this->add([
            'type'  => Hidden::class,
            'name' => 'id',
        ]);

        // Add "email" field
        $this->add([
            'type'  => 'text',
            'name' => 'nome',
            'options' => [
                'label' => 'Privilegio',
            ],
        ]);



        $this->add([
            'type' => ObjectSelect::class,
            'name' => 'role',
            'options' => [
                'label'			 => 'Role',
                'object_manager' => $this->getObjectManager(),
                'target_class'   => Role::class,
                'property'       => 'nome',
                'empty_option' => 'Selecione',
            ]
        ]);

        $this->add([
            'type' => ObjectSelect::class,
            'name' => 'resource',
            'options' => [
                'label'			 => 'Resource',
                'object_manager' => $this->getObjectManager(),
                'target_class'   => Resource::class,
                'property'       => 'nome',
                'empty_option' => 'Selecione',
                'orderBy'  => ['nome' => 'ASC'],

            ]
        ]);



        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Create'
            ],
        ]);
    }


    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        // Create main input filter
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);


        // Add input for "full_name" field
        $inputFilter->add([
            'name'     => 'nome',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [


                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Preencha este campo!'
                        ]
                    ]
                ],
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 3,
                        'max' => 80,
                        'messages' => [
                            StringLength::TOO_SHORT => 'Necessário mais de 3 caracteres!'
                        ]
                    ],
                ],
            ],
        ]);

        
        return $inputFilter;
    }
}