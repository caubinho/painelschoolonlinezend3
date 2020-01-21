<?php
namespace User\Form;

use User\Entity\Boleto;
use User\Entity\User;
use User\Form\Element\ObjectSelect;


use Zend\Filter\File\RenameUpload;
use Zend\Form\Element\Select;
use Zend\InputFilter\InputFilterProviderInterface;

use Zend\Form\Element\Date;
use Zend\Form\Element\File;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Textarea;
use Zend\Form\Form;

use Zend\InputFilter\InputFilter;

use Zend\Validator\File\Extension;

use Zend\Validator\Hostname;



/**
 * This form is used to collect user's email, full name, password and status. The form
 * can work in two scenarios - 'create' and 'update'. In 'create' scenario, user
 * enters password, in 'update' scenario he/she doesn't enter password.
 */
class BoletoForm extends Form
{
    protected $objectManager = null;
    /**
     * @var int|null|string
     */
    private $alunos;
    private $turmas;

    /**
     * Constructor.
     */
    public function __construct($alunos, $turmas)
    {
        // Define form name
        parent::__construct('boleto-form');

        $this->alunos = $alunos;
        $this->turmas = $turmas;

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
            'type' => Date::class,
            'name' => 'vencimento',
            'attributes' => [
                'min'  => '2012-01-01',
                'max'  => '2030-01-01',
                'step' => '1', // days; default step interval is 1 day
                'class'=>'form-control col-md-7 col-xs-12',
            ],
            'options' => [
                'format' => 'Y-m-d'
            ]
        ]);



        $alunos = new Select();
        $alunos
            ->setName("aluno")
            ->setOptions(array('value_options' => $this->alunos,)
            );
        $this->add($alunos);


        $turmas = new Select();
        $turmas
            ->setAttribute('id', 'turmas')
            ->setName("turma")
            ->setOptions(array('value_options' => $this->turmas,)
            );
        $this->add($turmas);

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



        // Add "status" field
        $this->add([
            'type'  => 'select',
            'name' => 'status',
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    1 => 'Pendente',
                    2 => 'Pago',
                ]
            ],
        ]);

        // Add "status" field
        $this->add([
            'type'  => 'checkbox',
            'name' => 'envio',
            'options' => [
                'label' => 'Envio imediato?',


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

        // Add validation rules for the "file" field
        $inputFilter->add([
            'type'     => 'Zend\InputFilter\FileInput',
            'name'     => 'file',
            'validators' => [

                [
                    'name' => Extension::class,
                    'options' => [
                        'extensions' => 'pdf', 'jpg', 'png', 'zip',

                        'case' => true,
                        'message' => [
                            Extension::NOT_FOUND => 'Extensão inválida',
                        ]
                    ],

                ],


            ]
        ]);

        return $inputFilter;
    }
}