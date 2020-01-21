<?php

namespace User\Form;

use User\Form\Element\ObjectSelect;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;

use User\Entity\Category;
use User\Entity\Post;

use Zend\Form\Element\Hidden;
use Zend\Form\Form;

use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Filter\StringTrim;
use Zend\Validator\StringLength;
use Zend\Validator;


/**
 * This form is used to collect post data.
 */
class PostForm extends Form implements InputFilterProviderInterface

{
    protected $objectManager = null;

    /**
     * Constructor.
     */
    public function __construct(ObjectManager $objectManager)
    {
        // Define form name
        parent::__construct('post-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal form-label-left');

        $this->setHydrator(new DoctrineObject($objectManager, 'User\Entity\Post'));
        $this->setObject(new Post());


        $this->objectManager = $objectManager;

        $this->add([
            'name' => 'id',
            'type' => Hidden::class
        ]);

        // Add "title" field
        $this->add([
            'type'  => 'text',
            'name' => 'title',
            'attributes' => [
                'id' => 'title'
            ],
            'options' => [
                'label' => 'Title',
            ],
        ]);

        $this->add(array(
            'name' => 'img',
            'type'=>'text',
            'attributes'=> array(
                'id'=>'img',
               // 'readonly'=> 'readonly',
                'class'=>'form-control col-md-7 col-xs-12'
            )
        ));


        // Add "content" field
        $this->add([
            'type'  => 'textarea',
           'name' => 'content',
            'attributes' => [
                'id' => 'editor1'
            ],
            'options' => [
                'label' => 'Content',
            ],
        ]);

        // Add "tags" field
        $this->add([
            'type'  => 'text',
            'name' => 'tags',
            'attributes' => [
                'id' => 'tags'
            ],
            'options' => [
                'label' => 'Tags',
            ],
        ]);

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
                    Post::STATUS_PUBLISHED => 'Ativo',
                    Post::STATUS_DRAFT => 'Inativo',
                ]
            ],
        ]);

        

        $this->add(array(
            'type' => ObjectSelect::class,
            'name' => 'category',
            'options' => array(
                'label'			 => 'Categoria',
                'object_manager' => $this->getObjectManager(),
                'target_class'   => Category::class,
                'property'       => 'titulo',
            )
        ));






        // Add the submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Create',
                'id' => 'submitbutton',
            ],
        ]);
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        // TODO: Implement getInputFilterSpecification() method.



        $array_return = array(
            'title' => array(
                'required' => true,
                'filters' => array(
                    new StringTrim()
                ),
                'validators' => array(
                    new StringLength(3, 255)
                )
            ),


        );

        return $array_return;
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

