<?php
namespace User\Form;

use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Validator\EmailAddress;
use Zend\Validator\Hostname;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * This form is used to collect user's email, full name, password and status. The form
 * can work in two scenarios - 'create' and 'update'. In 'create' scenario, user
 * enters password, in 'update' scenario he/she doesn't enter password.
 */
class PoloForm extends Form
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Define form name
        parent::__construct('polo');


        // Set POST method for this form
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal form-label-left');

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
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter()
    {
        // Create main input filter
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);


        // Add input for "full_name" field
        $inputFilter->add([
            'name'     => 'cidade',
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

    }
}