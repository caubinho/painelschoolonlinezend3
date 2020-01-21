<?php
namespace User\Form;

use Doctrine\DBAL\Types\DecimalType;
use Zend\Db\Sql\Ddl\Column\Decimal;
use Zend\Form\Element\File;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select;
use Zend\Form\Element\Textarea;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Validator\File\Extension;
use Zend\Validator\Hostname;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;


class TrabalhoForm extends Form
{
    /**
     * @var int|null|string
     */
    private $professor;

    /**
     * Constructor.
     */
    public function __construct($professor)
    {
        // Define form name
        parent::__construct('trabalho-form');

        //sempre instanciar abaixo do construct
        $this->professor = $professor;

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
            'name' => 'aula',
            'options' => [
                'label' => 'Aula',
            ],
        ]);

        // Add "email" field
        $this->add([
            'type'  => 'text',
            'name' => 'aluno',
            'options' => [
                'label' => 'Aluno',
            ],
        ]);

        // Add "email" field
        $this->add([
            'type'  => 'text',
            'name' => 'turma',
            'options' => [
                'label' => 'Turma',
            ],
        ]);

        // Add "email" field
        $this->add([
            'type'  => 'text',
            'name' => 'nota',
            'options' => [
                'label' => 'Nota',
            ],
        ]);

        // Add "email" field
        // Add "status" field
        $this->add([
            'type'  => 'select',
            'name' => 'corrigido',
            'options' => [
                'label' => 'Corrigido',
                'value_options' => [
                    '2' => 'Não',
                    '1' => 'Sim',

                ]
            ],
        ]);

        $professor = new Select();
        $professor
            ->setAttribute('id', 'professor')
            ->setName("professor")
            ->setOptions(array('value_options' =>
                    $this->professor,
                    )
            );
        $this->add($professor);


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



        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Salvar'
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


        // Add validation rules for the "file" field
        $inputFilter->add([
            'type'     => 'Zend\InputFilter\FileInput',
            'name'     => 'file',
            'required' => true,
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Selecione um arquivo!'
                        ]
                    ]
                ],
                [
                    'name' => Extension::class,
                    'options' => [
                        'extensions' =>'pdf',
                                        'doc',
                                        'docx',
                                        'jpeg',
                                        'jpg',
                                        'pptx',
                                        'ppt',
                                        'ppsx',

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
                        'target'=> getcwd().'/public/trabalho/spp_',
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