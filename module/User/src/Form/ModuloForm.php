<?php

namespace User\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

use User\Entity\User;
use Zend\InputFilter\InputFilterProviderInterface;

use Zend\Form\Element\Hidden;
use Zend\Form\Element\Textarea;
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
class ModuloForm extends Form implements InputFilterProviderInterface
{
    protected $objectManager = null;

    /**
     * Constructor.
     */
    public function __construct(ObjectManager $objectManager)
    {
        // Define form name
        parent::__construct('modulo-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal form-label-left');

        $this->setHydrator(new DoctrineObject($objectManager, 'User\Entity\User'));
        $this->setObject(new User());

        $this->objectManager = $objectManager;


        $this->add([
            'type'  => Hidden::class,
            'name' => 'id',
        ]);

        // Add "email" field
        $this->add([
            'type'  => 'text',
            'name' => 'titulo',
            'options' => [
                'label' => 'Titulo',
            ],
        ]);

        // Add "email" field
        $this->add([
            'type'  => 'text',
            'name' => 'professor',
            'options' => [
                'label' => 'Professor',
            ],
        ]);



        $this->add([
            'type' => Textarea::class,
            'name'=> 'texto',
            'attributes'=> array(
                'class' =>  'form-control col-md-12 col-xs-12',
                'cols'  =>  '80',
                'rows'  =>  '30',
                'id'    => 'editor1',
            ),
            'options' => array(

            ),
        ]);




        // Add "status" field
        $this->add([
            'type'  => 'select',
            'name' => 'status',
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    1 => 'Ativo',
                    2 => 'Inativo',
                ]
            ],
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
            'name'     => 'titulo',
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