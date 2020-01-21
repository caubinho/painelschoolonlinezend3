<?php
namespace User\Form;

use Zend\Form\Element\File;
use Zend\Form\Element\Hidden;
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
class VideoForm extends Form
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        // Define form name
        parent::__construct('video-form');

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
            'name' => 'link',
            'options' => [
                'label' => 'Link',
            ],
        ]);


        // Add "status" field
        $this->add([
            'type'  => 'select',
            'name' => 'tipo',
            'options' => [

                'label' => 'Tipo',
                'value_options' => [
                    'YouTube' => 'YouTube',
                    'Vimeo' => 'Vimeo',
                    'Html5' => 'Html5',
                ],
            ],
            'attributes' => array(
                'value' => 'Html5' //set selected to '1'
            )
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
                        'extension' =>  'gif',
                        'jpeg',
                        'jpg',
                        'png',
                        'mp4',
                        'mpeg',
                        //'enableHeaderCheck' => true

                        'case' => true,

                        'message' => [
                            Extension::NOT_FOUND => 'Extensão inválida',
                        ]

                    ],

                ],

            ],
            'filters'  => [
                [
                    'name' => 'FileRenameUpload',
                    'options' => [
                        'target'=> getcwd().'/public/video/spp_',
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