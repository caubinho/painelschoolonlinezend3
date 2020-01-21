<?php

namespace User\View\Helper;

use Zend\View\Helper\AbstractHelper;

class LimitaCaracter extends AbstractHelper
{
     
    public function __invoke($texto, $limite) {

        $originalString = preg_replace('#<img[^>]*>#i', ' ', $texto);
        $str = preg_replace('/<iframe.*?\/iframe>/i', '', $originalString);


        $contador = strlen($str);
        
        if ($contador >= $limite) {
            $texto = substr($texto, 0, strrpos(substr($texto, 0, $limite), ' ')) . '...';
            return $texto;
        } else {
            return $texto;
        }
    }

}