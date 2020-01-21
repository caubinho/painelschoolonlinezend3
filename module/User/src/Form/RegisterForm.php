<?php
namespace User\Form;

use DoctrineModule\Validator\NoObjectExists;
use User\Entity\User;
use Zend\Form\Element\Email;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Validator\EmailAddress;
use Zend\Validator\Hostname;
use Zend\Validator\Identical;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * This form is used to collect user's login, password and 'Remember Me' flag.
 */
class RegisterForm extends Form
{
    /**
     * Entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager = null;

    /**
     * Constructor.
     */
    public function __construct($entityManager)
    {
        // Define form name
        parent::__construct('register-form');

        $this->entityManager = $entityManager;

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addInputFilter();

    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements()
    {
        // Add "full_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'full_name',
            'options' => [
                'label' => 'Nome',
            ],
        ]);

        // Add "codigo" field
        $this->add([
            'type'  => 'text',
            'name' => 'nascimento',

        ]);

        // Add "codigo" field
        $this->add([
            'type'  => 'text',
            'name' => 'cpf',
            'options' => [
                'label' => 'Cpf',
            ],
        ]);

        // Add "full_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'rg',
            'options' => [
                'label' => 'RG',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'formacao',
            'options' => [
                'label' => 'Formação',
            ],
        ]);

        // Add "full_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'pai',
            'options' => [
                'label' => 'Pai',
            ],
        ]);

        // Add "full_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'mae',
            'options' => [
                'label' => 'Mãe',
            ],
        ]);


        // Add "full_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'profissao',
            'options' => [
                'label' => 'Profissão',
            ],
        ]);


        // Add "email" field
        $this->add([
            'type'  => Email::class,
            'name' => 'email',
            'options' => [
                'label' => 'Your E-mail',
            ],
        ]);

        // Add "password" field
        $this->add([
            'type'  => 'password',
            'name' => 'password',
            'options' => [
                'label' => 'Senha',
            ],
        ]);

        // Add "confirm_password" field
        $this->add([
            'type'  => 'password',
            'name' => 'confirm_password',
            'options' => [
                'label' => 'Confirmar Senha',
            ],
        ]);

        // Add "full_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'celular',
            'options' => [
                'label' => 'Celular',
            ],
        ]);




        // Add the CSRF field
        $this->add([
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ],
        ]);

        // Add "full_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'celular',
            'options' => [
                'label' => 'Celular',
            ],
        ]);


        $this->add([
            'type'  => 'text',
            'name' => 'emissor',
            'options' => [
                'label' => 'Emissor',
            ],
        ]);

        // Add "full_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'rua',
            'options' => [
                'label' => 'Rua',
            ],
        ]);

        // Add "full_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'numero',
            'options' => [
                'label' => 'Numero',
            ],
        ]);

        // Add "full_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'bairro',
            'options' => [
                'label' => 'Bairro',
            ],
        ]);

        // Add "full_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'complemento',
            'options' => [
                'label' => 'Complemento',
            ],
        ]);

        // Add "full_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'cep',
            'options' => [
                'label' => 'Cep',
            ],
        ]);

        // Add "full_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'cidade',
            'options' => [
                'label' => 'Cidade',
            ],
        ]);

        // Add "status" field
        $this->add([
            'type'  => 'select',
            'name' => 'estado',
            'options' => [

                'label' => 'Estado',
                'value_options' => [
                    '' => 'Selecione',
                    'Acre' => 'Acre' ,
                    'Alagoas' => 'Alagoas' ,
                    'Amapá' => 'Amapá' ,
                    'Amazonas' => 'Amazonas' ,
                    'Bahia' => 'Bahia' ,
                    'Ceará' => 'Ceará' ,
                    'Distrito Federal' => 'Distrito Federal' ,
                    'Espírito Santo' => 'Espírito Santo' ,
                    'Goiás' => 'Goiás' ,
                    'Maranhão' => 'Maranhão' ,
                    'Mato Grosso' => 'Mato Grosso' ,
                    'Mato Grosso do Sul' => 'Mato Grosso do Sul' ,
                    'Minas Gerais' => 'Minas Gerais' ,
                    'Pará' => 'Pará' ,
                    'Paraíba' => 'Paraíba' ,
                    'Paraná' => 'Paraná' ,
                    'Pernambuco' => 'Pernambuco' ,
                    'Piauí' => 'Piauí' ,
                    'Rio de Janeiro' => 'Rio de Janeiro' ,
                    'Rio Grande do Sul' => 'Rio Grande do Sul' ,
                    'Rio Grande do Norte' => 'Rio Grande do Norte' ,
                    'Rondônia' => 'Rondônia' ,
                    'Roraima' => 'Roraima' ,
                    'Santa Catarina' => 'Santa Catarina' ,
                    'São Paulo' => 'São Paulo' ,
                    'Sergipe' => 'Sergipe' ,
                    'Tocantins' => 'Tocantins' ,
                ],
            ],
            'attributes' => array(
                'value' => 'Paraná' //set selected to '1'
            )
        ]);

        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Logar',
                'id' => 'submit',
            ],
        ]);
    }

    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter()
    {

        $entityManager = $this->entityManager;

        // Create main input filter
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        // Add input for "full_name" field
        $inputFilter->add([
            'name'     => 'full_name',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [

                [
                    'name' =>NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Preencha seu Nome!'
                        ]
                    ]
                ],
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 4,
                        'max' => 80,
                        'messages' => [
                            StringLength::TOO_SHORT => 'Necessário mais de 3 caracteres!',
                            StringLength::TOO_LONG => 'Nome muito longo',
                        ]
                    ],
                ]

            ],
        ]);

        // Add input for "email" field
        $inputFilter->add([
            'name'     => 'email',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' =>NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Preencha seu Email!'
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
                [
                    'name' => 'EmailAddress',
                    'options' => [
                        'allow' => Hostname::ALLOW_DNS,
                        'useMxCheck'    => false,
                        'messages' => [
                            EmailAddress::INVALID_FORMAT => 'Preencha com um email válido example@email.com',
                        ]

                    ],
                ],

                [
                    'name' => NoObjectExists::class,
                    'options' => [
                        'object_repository' => $entityManager->getRepository('User\Entity\User'),
                        'fields' => 'email',
                        'messages' => [
                            'objectFound' => 'Este email já existe em nossas bases!',
                        ],

                    ],
                ],
            ],
        ]);



        // Add input for "password" field
        $inputFilter->add([
            'name'     => 'password',
            'required' => true,
            'filters'  => [
            ],
            'validators' => [
                [
                    'name' =>NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Preencha uma Senha!'
                        ]
                    ]
                ],

                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 6,
                        'max' => 10,
                        'messages' => [
                            StringLength::TOO_SHORT => 'Necessário de 6 a 10 caracteres!'
                        ]
                    ],
                ],



            ],
        ]);

        // Add input for "confirm_password" field
        $inputFilter->add([
            'name'     => 'confirm_password',
            'required' => true,
            'filters'  => [
            ],
            'validators' => [
                [
                    'name' =>NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Confirme sua Senha!'
                        ]
                    ]
                ],
                [
                    'name'    => 'Identical',
                    'options' => [
                        'token' => 'password',
                        'messages' => [
                            Identical::NOT_SAME => 'Senha deve ser igual!',
                        ]
                    ],
                ],


            ],
        ]);



    }
}

