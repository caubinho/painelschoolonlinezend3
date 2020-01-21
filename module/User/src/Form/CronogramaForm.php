<?php

namespace User\Form;

use Zend\Form\Element\Date;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Validator;

/**
 * This form is used to collect post data.
 */
class CronogramaForm extends Form
{
    /**
     * @var int|null|string
     */
    private $professor;
    /**
     * @var int|null|string
     */
    private $modulos;
    /**
     * @var array
     */
    private $aulas;

    /**
     * Constructor.
     */
    public function __construct($modulos, $aulas, $professor)
    {
        // Define form name
        parent::__construct('cronograma-form');

        $this->modulos = $modulos;
        $this->aulas = $aulas;
        $this->professor = $professor;


        // Set POST method for this form
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal form-label-left form');



        $this->addElements();
        $this->addInputFilter();



    }

    protected function addElements()
    {


        $modulo = new Select();
        $modulo
            ->setAttribute('id', 'modulos')
            ->setName("modulo")
            ->setOptions(array('value_options' => $this->modulos,)
            );
        $this->add($modulo);

        $aulas = new Select();
        $aulas
            ->setAttribute('id', 'aula')
            ->setName("aula")
            ->setOptions(array('value_options' => $this->aulas,)
            );
        $this->add($aulas);


        $professor = new Select();
        $professor
            ->setAttribute('id', 'professor')
            ->setName("professor")
            ->setOptions(array('value_options' => $this->professor,)
            );
        $this->add($professor);

        $this->add([
            'type' => Date::class,
            'name' => 'inicio',
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

    private function addInputFilter()
    {

        // Create main input filter
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        // Add input for "full_name" field
        $inputFilter->add([
            'name'     => 'professor',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [

                [
                    'name' => Validator\NotEmpty::class,
                    'options' => [
                        'messages' => [
                            Validator\NotEmpty::IS_EMPTY => 'Selecione um Professor!'
                        ]
                    ]
                ]
            ],
        ]);


        return $inputFilter;
    }

}

