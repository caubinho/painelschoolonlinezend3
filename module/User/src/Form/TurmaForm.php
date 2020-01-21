<?php

namespace User\Form;

use User\Entity\Turma;
use Zend\Form\Element\Select;
use Zend\Form\Element\Date;
use Zend\Form\Element\File;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Textarea;
use Zend\Form\Form;

use Zend\InputFilter\InputFilter;

use Zend\Validator\File\Extension;

use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;



class TurmaForm extends Form
{

    /**
     * @var \User\Entity\User
     */
    private $coordenador;
    /**
     * @var \User\Entity\Polo
     */
    private $polo;

    /**
     * Constructor.
     */
    public function __construct($coordenador, $polo)
    {
        // Define form name
        parent::__construct('turma-form');

        $this->coordenador = $coordenador;
        $this->polo = $polo;

        // Set POST method for this form
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setAttribute('class', 'form-horizontal form-label-left');

        $this->addElements();
        $this->addInputFilter();


    }

    protected function addElements()
    {

        $this->add([
            'type'  => Hidden::class,
            'name' => 'id',
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'titulo',
            'options' => [
                'label' => 'Titulo',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'telefone',
            'options' => [
                'label' => 'Telefone',
            ],
        ]);

        $this->add([
            'type'  => 'text',
            'name' => 'endereco',
            'options' => [
                'label' => 'Endereço',
            ],
        ]);

        $this->add([
            'type' => Date::class,
            'name' => 'inicio',
            'attributes' => [
                'id'    => 'dataInicio',
                'min'  => '2012-01-01',
                'max'  => '2030-01-01',
                'step' => '1', // days; default step interval is 1 day
                'class'=>'form-control col-md-7 col-xs-12',
            ],
            'options' => [
                'format' => 'Y-m-d'
            ]
        ]);

        $this->add([
            'type' => Date::class,
            'name' => 'termino',
            'attributes' => [
                'id'    => 'dataTermino',
                'min'  => '2012-01-01',
                'max'  => '2030-01-01',
                'step' => '1', // days; default step interval is 1 day
                'class'=>'form-control col-md-7 col-xs-12',
            ],
            'options' => [
                'format' => 'Y-m-d'
            ]
        ]);

        $coordenador = new Select();
        $coordenador
            ->setAttribute('id', 'coordenador')
            ->setName("coordenador")
            ->setOptions(array('value_options' => $this->coordenador,)
            );
        $this->add($coordenador);

        $polo = new Select();
        $polo
            ->setAttribute('id', 'polo')
            ->setName("polo")
            ->setOptions(array('value_options' => $this->polo,)
            );
        $this->add($polo);

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
            'type'  => 'select',
            'name' => 'status',
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    Turma::STATUS_ACTIVE => 'Ativa',
                    Turma::STATUS_RETIRED => 'Inativa',
                    Turma::STATUS_FORMAD => 'Formada',
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
                        'min' => 3,
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
                                'jpg', 'png',
                        ],
                        'case' => true,
                        'messages' => [
                            Extension::FALSE_EXTENSION => 'Extensão inválida',
                        ]


                    ],


            ],
            'filters'  => [
                [
                    'name' => 'FileRenameUpload',
                    'options' => [
                        'target'=> './public/media/spp_',
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