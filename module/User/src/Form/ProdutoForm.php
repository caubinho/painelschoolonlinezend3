<?php

namespace User\Form;

use User\Entity\CategoriaProduto;
use User\Entity\Produto;
use User\Form\Element\ObjectSelect;
use Zend\Form\Element\File;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select;
use Zend\Form\Form;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Filter\StringTrim;
use Zend\Validator\StringLength;
use Zend\Validator;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\Validator\File\Size;

/**
 * This form is used to collect post data.
 */
class ProdutoForm extends Form implements InputFilterProviderInterface

{
    protected $objectManager = null;

    /**
     * Constructor.
     */
    public function __construct(ObjectManager $objectManager)
    {
        // Define form name
        parent::__construct('produto-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal form-label-left');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->setHydrator(new DoctrineObject($objectManager, 'User\Entity\Produto'));
        $this->setObject(new Produto());


        $this->objectManager = $objectManager;

        $this->add([
            'name' => 'id',
            'type' => Hidden::class
        ]);

        // Add "title" field
        $this->add([
            'type'  => 'text',
            'name' => 'titulo',
            'attributes' => [
                'id' => 'title'
            ],
            'options' => [
                'label' => 'Titulo',
            ],
        ]);

        // Add "status" field
        $this->add([
            'type'  => Select::class,
            'name' => 'tipo',
            'attributes' => [
                'id' => 'tipo'
            ],
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    'Audio' => 'Áudio',
                    'Html5' => 'Html5',
                    'YouTube' => 'YouTube',
                    'Vimeo' => 'Vimeo',
                ],
                'empty_option' => 'Selecione',
            ],
        ]);

        $this->add(array(
            'type' => ObjectSelect::class,
            'name' => 'categoriaproduto',
            'options' => array(
                'label'			 => 'Categoria',
                'object_manager' => $this->getObjectManager(),
                'target_class'   => CategoriaProduto::class,
                'property'       => 'titulo',
                'empty_option' => 'Selecione',
            )
        ));

        // Add "status" field
        $this->add([
            'type'  => Select::class,
            'name' => 'tipo',
            'attributes' => [
                'id' => 'tipo'
            ],
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    'Audio'     => 'Áudio',
                    'Video'     => 'Vídeo',
                    'YouTube'   => 'YouTube',
                    'Vimeo'     => 'Vimeo',
                ],
                'empty_option' => 'Selecione',
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


        $this->add(array(
            'name' => 'link',
            'type'=>'text',
            'attributes'=> array(
                'id'=>'link',
                // 'readonly'=> 'readonly',
                'class'=>'form-control col-md-7 col-xs-12'
            )
        ));


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
                    Produto::STATUS_PUBLISHED => 'Ativo',
                    Produto::STATUS_DRAFT => 'Inativo',
                ]
            ],
        ]);









        // Add the submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Salvar',
                'id' => 'submitbutton',
            ],
        ]);
    }



    /**
     * This method creates input filter (used for form filtering/validation).
     */
//    private function addInputFilter()
//    {
//
//        $inputFilter = new InputFilter();
//        $this->setInputFilter($inputFilter);
//
//        $inputFilter->add([
//            'name'     => 'title',
//            'required' => true,
//            'filters'  => [
//                ['name' => 'StringTrim'],
//                ['name' => 'StripTags'],
//                ['name' => 'StripNewlines'],
//            ],
//            'validators' => [
//                [
//                    'name'    => 'StringLength',
//                    'options' => [
//                        'min' => 1,
//                        'max' => 1024
//                    ],
//                ],
//
//                [
//                    'name' => NotEmpty::class,
//                    'options' => [
//                        'messages' => [
//                            NotEmpty::IS_EMPTY => 'O campo é requerido!'
//                        ]
//                    ]
//                ]
//            ],
//        ]);
//
//        $inputFilter->add([
//            'name'     => 'content',
//            'required' => true,
//            'filters'  => [
//                ['name' => 'StripTags'],
//            ],
//            'validators' => [
//                [
//                    'name'    => 'StringLength',
//                    'options' => [
//                        'min' => 1,
//                        'max' => 4096
//                    ],
//                ],
//            ],
//        ]);
//
//        $inputFilter->add([
//            'name'     => 'tags',
//            'required' => true,
//            'filters'  => [
//                ['name' => 'StringTrim'],
//                ['name' => 'StripTags'],
//                ['name' => 'StripNewlines'],
//            ],
//            'validators' => [
//                [
//                    'name'    => 'StringLength',
//                    'options' => [
//                        'min' => 1,
//                        'max' => 1024
//                    ],
//                ],
//            ],
//        ]);
//    }



    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        // TODO: Implement getInputFilterSpecification() method.



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
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 512
                    ],
                ],

                [
                    'name' => Validator\NotEmpty::class,
                    'options' => [
                        'messages' => [
                            Validator\NotEmpty::IS_EMPTY => 'O campo é requerido!'
                        ]
                    ]
                ]
            ],
        ]);

        // Add input for "full_name" field
        $inputFilter->add([
            'name'     => 'categoria',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 512
                    ],
                ],

                [
                    'name' => Validator\NotEmpty::class,
                    'options' => [
                        'messages' => [
                            Validator\NotEmpty::IS_EMPTY => 'O campo é necessário!'
                        ]
                    ]
                ]
            ],
        ]);

        // Add validation rules for the "file" field
        $inputFilter->add([
            'type'     => 'Zend\InputFilter\FileInput',
            'name'     => 'file',
            'required' => true,

            'validators' => [
                ['name'    => 'FileUploadFile'],
                [
                    'name'    => 'FileMimeType',
                    'options' => [
                        'mimeType'  => ['audio/mp3', 'audio/mpeg', 'video/mp4']
                    ]
                ],

            ],
            'filters'  => [
                [
                    'name' => 'FileRenameUpload',
                    'options' => [
                        'target'=> getcwd().'/public/media/pn_',
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

    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function getObjectManager()
    {
        return $this->objectManager;
    }


}

