<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * This is the Post controller class of the Blog application.
 * This controller is used for managing posts (adding/editing/viewing/deleting).
 */
class PermissaoController extends AbstractActionController
{

    public function indexAction()
    {

        // Render the view template.
        return new ViewModel();

    }


}
