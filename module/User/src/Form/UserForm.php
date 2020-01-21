<?php
namespace User\Form;


use DoctrineModule\Validator\NoObjectExists;
use Zend\Form\Element\File;
use Zend\Form\Element\Select;
use Zend\Form\Form;

use Zend\InputFilter\InputFilter;
use Zend\Validator\EmailAddress;
use Zend\Validator\File\Extension;

use Zend\Validator\Hostname;
use Zend\Validator\Identical;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

class UserForm extends Form
{


//    /**
//     * Scenario ('create' or 'update').
//     * @var string
//     */
//    private $scenario;

    /**
     * Entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager = null;


    /**
     * @var
     */
    private $funcao;
    /**
     * @var \User\Entity\Polo
     */
    private $polo;


    /**
     * Constructor.
     */
    public function __construct($entityManager,   $funcao, $polo)
    {
        // Define form name
        parent::__construct('user-form');

        $this->entityManager = $entityManager;

        $this->funcao = $funcao;
        $this->polo = $polo;

        // Set POST method for this form
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setAttribute('class', 'form form-horizontal form-label-left');


        $this->addElements();
        $this->addInputFilter();



    }

    protected function addElements()
    {

        // Add "email" field
        $this->add([
            'type'  => 'text',
            'name' => 'email',
            'options' => [
                'label' => 'E-mail',
            ],
        ]);

        // Add "full_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'full_name',
            'options' => [
                'label' => 'Nome',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'profissao',
            'options' => [
                'label' => 'Profissão',
            ],
        ]);

        // Add "codigo" field
        $this->add([
            'type'  => 'text',
            'name' => 'codigo',
            'options' => [
                'label' => 'Codigo SPP',
            ],
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
            'name' => 'rg',
            'options' => [
                'label' => 'RG',
            ],
        ]);

        // Add "full_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'naturalidade',
            'options' => [
                'label' => 'Naturalidade',
            ],
        ]);

        // Add "status" field
        $this->add([
            'type'  => 'select',
            'name' => 'sexo',
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    'Masculino' => 'Masculino',
                    'Feminino' => 'Feminino',
                ]
            ],
        ]);

        // Add "status" field
        $this->add([
            'type'  => 'checkbox',
            'name' => 'isteacher',
            'options' => [
                'label' => 'É Professor!',

            ],
        ]);


        // Add "full_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'telefone',
            'options' => [
                'label' => 'Telefone',
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

        $this->add([
            'type'  => File::class,
            'name' => 'file',
            'attributes' => [
                'id' => 'file'
            ],
            'options' => [
                'label' => 'Arquivo',
            ],
        ]);

        $this->add([
            'type'  => File::class,
            'name' => 'contrato',
            'attributes' => [
                'id' => 'contrato'
            ],
            'options' => [
                'label' => 'Contrato',
            ],
        ]);



        //if ($this->scenario == 'create') {

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
       // }

        // Add "status" field
        $this->add([
            'type'  => 'select',
            'name' => 'status',
            'attributes' => [
                'id' => 'status'
            ],
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    2 => 'Inativo',
                    1 => 'Ativo',

                ]
            ],
        ]);

        $role = new Select();
        $role
            ->setAttribute('id', 'role')
            ->setName("role")
            ->setOptions(array('value_options' => $this->funcao))
            ->setValue('2', 'Aluno');
        $this->add($role);

        $polo = new Select();
        $polo
            ->setAttribute('id', 'polo')
            ->setName("polo")
            ->setOptions(array('value_options' => $this->polo));
        $this->add($polo);


        // Add "codigo" field
        $this->add([
            'type'  => 'text',
            'name' => 'nascimento',
            
        ]);

        /*-- novos campos --*/

        $this->add([
            'type'  => 'text',
            'name' => 'emissor',
            'options' => [
                'label' => 'Emissor',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'recado',
            'options' => [
                'label' => 'Recado',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'formacao',
            'options' => [
                'label' => 'Formação',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'empresa',
            'options' => [
                'label' => 'Empresa',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'empresaendereco',
            'options' => [
                'label' => 'Endereço',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'empresanumero',
            'options' => [
                'label' => 'Numero',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'empresacomplemento',
            'options' => [
                'label' => 'Complemento',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'empresabairro',
            'options' => [
                'label' => 'Numero',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'empresacidade',
            'options' => [
                'label' => 'Cidade',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'empresacep',
            'options' => [
                'label' => 'Cep',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'empresatel',
            'options' => [
                'label' => 'tel',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'empresaramal',
            'options' => [
                'label' => 'Ramal',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'empresacel',
            'options' => [
                'label' => 'Ramal',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'website',
            'options' => [
                'label' => 'Website',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'empresaemail',
            'options' => [
                'label' => 'Email',
            ],
        ]);

        // Add "empresa estado" field
        $this->add([
            'type'  => 'select',
            'name' => 'empresaestado',
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




        /*--end novos campos--*/

        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Salvar'
            ],
        ]);


    }

    private function addInputFilter()
    {
        $entityManager = $this->entityManager;

        // Create main input filter
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

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

        // Add input for "full_name" field
        $inputFilter->add([
            'name'     => 'nascimento',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],

            'validators' => [
                [
                    'name' =>NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Preencha este campo!'
                        ]
                    ]
                ],

            ],

        ]);

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
                            NotEmpty::IS_EMPTY => 'Preencha este campo!'
                        ]
                    ]
                ],
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 2,
                        'max' => 80,
                        'messages' => [
                            StringLength::TOO_SHORT => 'Necessário mais de 3 caracteres!',
                            StringLength::TOO_LONG => 'Nome muito longo',
                        ]
                    ],
                ]

            ],
        ]);

        // Add input for "full_name" field
        $inputFilter->add([
            'name'     => 'cpf',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [

                [
                    'name' =>NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Preencha este campo!'
                        ]
                    ]
                ],
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 11,
                        //'max' => 14,
                        'messages' => [
                            StringLength::TOO_SHORT => 'Necessário 11 caracteres!',
                            StringLength::TOO_LONG => 'Campo muito longo',
                        ]
                    ],
                ]



            ],
        ]);

        // Add input for "full_name" field
        $inputFilter->add([
            'name'     => 'pai',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' =>NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Preencha este campo!'
                        ]
                    ]
                ],
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 2,
                        'max' => 80,
                        'messages' => [
                            StringLength::TOO_SHORT => 'Necessário mais de 3 caracteres!',
                            StringLength::TOO_LONG => 'Nome muito longo',
                        ]
                    ],
                ],




            ],
        ]);

        // input for "full_name" field
        $inputFilter->add([
            'name'     => 'mae',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' =>NotEmpty::class,
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
                            StringLength::TOO_SHORT => 'Necessário mais de 3 caracteres!',
                            StringLength::TOO_LONG => 'Nome muito longo',
                        ]
                    ],
                ],
                
            ],
        ]);

        // Add inp for "password" field
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
                                NotEmpty::IS_EMPTY => 'Preencha este campo!'
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
                                NotEmpty::IS_EMPTY => 'Preencha este campo!'
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


        // Add validation rules for the "file" field
        $inputFilter->add([
            'type'     => 'Zend\InputFilter\FileInput',
            'name'     => 'file',
            'validators' => [
                [
                    'name' => Extension::class,
                    'options' => [
                        'extensions' => 'jpeg', 'jpg', 'png',

                        'case' => true,

                        'message' => [
                            Extension::NOT_FOUND => 'Extensão inválida',
                            ]

                        ]


                ],

            ],
            'filters'  => [
                [
                    'name' => 'FileRenameUpload',
                    'options' => [
                        'target'=> getcwd().'/public/media/spp_',
                        'useUploadName'=>false,
                        'useUploadExtension'=>true,
                        'overwrite'=>false,
                        'randomize'=>true
                    ]
                ]
            ],
        ]);

        //contrato

        // Add validation rules for the "file" field
        $inputFilter->add([
            'type'     => 'Zend\InputFilter\FileInput',
            'name'     => 'contrato',
            'validators' => [
                [
                    'name' => Extension::class,
                    'options' => [
                        'extensions' => 'pdf', 'doc', 'docx',

                        'case' => true,

                        'message' => [
                                Extension::FALSE_EXTENSION => 'Extensão inválida',
                            ]

                    ],





                ],

            ],
            'filters'  => [
                [
                    'name' => 'FileRenameUpload',
                    'options' => [
                        'target'=> getcwd().'/public/media/spp_',
                        'useUploadName'=>false,
                        'useUploadExtension'=>true,
                        'overwrite'=>false,
                        'randomize'=>true
                    ]
                ]
            ],
        ]);

        return $inputFilter;
    }
}