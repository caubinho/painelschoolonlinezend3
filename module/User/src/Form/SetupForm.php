<?php
namespace User\Form;

use Zend\Form\Element\File;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Password;
use Zend\Form\Element\Textarea;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Validator\File\Extension;
use Zend\Validator\Hostname;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * This form is used to collect user's email, full name, password and status. The form
 * can work in two scenarios - 'create' and 'update'. In 'create' scenario, user
 * enters password, in 'update' scenario he/she doesn't enter password.
 */
class SetupForm extends Form
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        // Define form name
        parent::__construct('setup-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal form-label-left');
        $this->setAttribute('enctype','multipart/form-data');

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements()
    {
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
            'name' => 'fone',
            'options' => [
                'label' => 'Fone',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'email',
            'options' => [
                'label' => 'Email',
            ],
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

        $this->add([
            'type'  => File::class,
            'name' => 'file',
            'attributes' => [
                'id' => 'file'
            ],
            'options' => [
                'label' => 'Logo',
            ],
        ]);

        $this->add([
            'type'  => File::class,
            'name' => 'background',
            'attributes' => [
                'id' => 'file'
            ],
            'options' => [
                'label' => 'Background',
            ],
        ]);

        $this->add([
            'type'  => File::class,
            'name' => 'contrato',
            'attributes' => [
                'id' => 'file'
            ],
            'options' => [
                'label' => 'Contrato',
            ],
        ]);

        $this->add([
            'type'  => Password::class,
            'name' => 'passhost',
            'options' => [
                'label' => 'Senha',
            ],
        ]);


        // Add "status" field
        $this->add([
            'type'  => 'select',
            'name' => 'security',
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    '0' => 'Sem segurança',
                    'tls' => 'Tls',
                    'ssl' => 'Ssl',
                ]
            ],
        ]);


        // Add "status" field
        $this->add([
            'type'  => 'select',
            'name' => 'debug',
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    0 => '0 - Sem debug',
                    1 => '1 - Somente Email',
                    2 => '2 - Host e Email',
                ]
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'host',
            'options' => [
                'label' => 'Host',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'port',
            'options' => [
                'label' => 'Porta',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'emailhost',
            'options' => [
                'label' => 'Usuario',
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
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter()
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

        // Add validation rules for the "file" field
        $inputFilter->add([
            'type'     => 'Zend\InputFilter\FileInput',
            'name'     => 'file',
            'validators' => [
                [
                    'name' => Extension::class,
                    'options' => [
                        'extensions' =>
                            'gif',
                            'jpeg',
                            'jpg',
                            'png',
                        
                        //'enableHeaderCheck' => true

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


        // Add validation rules for the "file" field
        $inputFilter->add([
            'type'     => 'Zend\InputFilter\FileInput',
            'name'     => 'background',
            'validators' => [
                [
                    'name' => Extension::class,
                    'options' => [
                        'extensions' =>
                            'gif',
                            'jpeg',
                            'jpg',
                            'png',

                        //'enableHeaderCheck' => true
                        'case' => true,

                        'messages' => [
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

        // Add validation rules for the "file" field
        $inputFilter->add([
            'type'     => 'Zend\InputFilter\FileInput',
            'name'     => 'contrato',
            'validators' => [
                [
                    'name' => Extension::class,
                    'options' => [
                        'extensions' => 'gif',
                        'jpeg',
                        'jpg',
                        'png',
                        'pdf',
                        'docx',
                        'doc',

                        //'enableHeaderCheck' => true
                        'case' => true,

                        'messages' => [
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


    }
}