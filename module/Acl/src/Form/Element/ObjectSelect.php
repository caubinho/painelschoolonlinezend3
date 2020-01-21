<?php

namespace Acl\Form\Element;

use DoctrineModule\Form\Element\ObjectSelect as ORMObjectSelect;

class ObjectSelect extends ORMObjectSelect {

    public function getValue(){
        $value = parent::getValue();
        if(empty($value)){
            return null;
        }

        $om = $this->getProxy()->getObjectManager();
        if(is_object($value)){
            var_dump(get_class($value));exit;
        }

        return $value;
    }

    public function setValue($value){
        $this->value = $value;
        return $this;
    }

}

?>