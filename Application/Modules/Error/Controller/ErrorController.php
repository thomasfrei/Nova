<?php

class ErrorController extends \Nova\Controller\Action
{
    public function errorAction()
    {
        $this->view->title = 'Error';
        $error = $this->_request->getParam('error-handler');

        //var_dump($error);
        $this->view->error = $error;
        $this->view->errorType = $error->type;
        $this->view->errorFile = $error->file;
        $this->view->errorLine = $error->line;
        $this->view->errorMsg = $error->message;
        $this->view->params = $error->request->getParams();

        $counter = 0;
        $backtrace = array();
        
        $this->view->errorTrace = $error->trace;
        
        $this->view->test = $this->getActionParams();
        echo $this->view->render('error.php');

    }

}