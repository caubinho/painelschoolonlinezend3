<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\Validator\Identical;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * This form is used when changing user's password (to collect user's old password 
 * and new password) or when resetting user's password (when user forgot his password).
 */
class PasswordChangeForm extends Form
{   
    // There can be two scenarios - 'change' or 'reset'.
    private $scenario;
    
    /**
     * Constructor.
     * @param string $scenario Either 'change' or 'reset'.     
     */
    public function __construct($scenario)
    {
        // Define form name
        parent::__construct('password-change-form');
     
        $this->scenario = $scenario;
        
        // Set POST method for this form
        $this->setAttribute('method', 'post');
        //$this->setAttribute('class', 'form-horizontal form-label-left');
        
        $this->addElements();
        $this->addInputFilter();          
    }
    
    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements() 
    {

        // Add "new_password" field
        $this->add([            
            'type'  => 'password',
            'name' => 'new_password',
            'options' => [
                'label' => 'Nova Senha',
            ],
        ]);
        
        // Add "confirm_new_password" field
        $this->add([            
            'type'  => 'password',
            'name' => 'confirm_new_password',
            'options' => [
                'label' => 'Confirme a nova senha',
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
        
        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [                
                'value' => 'Alterar Senha',
                'id' => 'submit'
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
        

        
        // Add input for "new_password" field
        $inputFilter->add([
                'name'     => 'new_password',
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
                            'max' => 64,
                            'messages' => [
                                StringLength::TOO_SHORT => 'Necessário 6 ou mais caracteres!',
                            ]
                        ],
                    ],
                ],
            ]);
        
        // Add input for "confirm_new_password" field
        $inputFilter->add([
                'name'     => 'confirm_new_password',
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
                            'max' => 64,
                            'messages' => [
                                StringLength::TOO_SHORT => 'Necessário 6 ou mais caracteres!',
                            ]
                        ],
                    ],

                    [
                        'name'    => Identical::class,
                        'options' => [
                            'token' => 'new_password',
                            'messages' => [
                                Identical::NOT_SAME => 'Inclua a mesma senha nos 2 campos!'
                            ]
                        ],
                    ],
                ],
            ]);
    }
}

