<?php

namespace Acl\Form;

use Acl\Entity\Role;
use Acl\Form\Element\ObjectSelect;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

use Zend\Form\Element\Checkbox;
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
class RoleForm extends Form implements InputFilterProviderInterface
{
    protected $objectManager = null;

    /**
     * Constructor.
     */
    public function __construct(ObjectManager $objectManager)
    {
        // Define form name
        parent::__construct('role-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal form-label-left');

        $this->setHydrator(new DoctrineObject($objectManager, 'User\Entity\Role'));
        $this->setObject(new Role());

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
                'label' => 'Titulo',
            ],
        ]);

        // Add "email" field
        $this->add([
            'type'  => Checkbox::class,
            'name' => 'isAdmin',
            'options' => [
                'label' => 'Admin',
            ],
            'attributes'=> [
                'class' => 'form-control',
            ],

        ]);


        $this->add([
            'type' => \DoctrineModule\Form\Element\ObjectSelect::class,
            'name' => 'parent',
            'attributes'=> [
                'class' => 'form-control',
            ],
            'options' => [
                'label'			 => 'Herda',
                'object_manager' => $this->getObjectManager(),
                'target_class'   => Role::class,
                'property'       => 'nome',
                'empty_option' => 'Selecione',
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
                        'min' => 4,
                        'max' => 80,
                        'messages' => [
                            StringLength::TOO_SHORT => 'Necessário mais de 4 caracteres!'
                        ]
                    ],
                ],
            ],
        ]);


        return $inputFilter;
    }
}