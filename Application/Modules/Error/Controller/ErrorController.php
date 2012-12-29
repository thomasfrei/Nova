<?php

class ErrorController extends \Nova\Controller\Action
{
    public function errorAction()
    {
        $this->view->title = 'Error';
        $error = $this->_request->getParam('error_handler');

        $this->view->error = $error;
        $this->view->errorFile = $error->exception->getFile();
        $this->view->errorLine = $error->exception->getLine();
        $this->view->errorMsg = $error->exception->getMessage();
        $this->view->params = $error->request->getParams();

        $counter = 0;
        $backtrace = array();

        foreach($error->exception->getTrace() as $exception){
            $backtrace[$counter]['file'] = $exception['file'];
            $backtrace[$counter]['function'] = $exception['function'];
            $backtrace[$counter]['line'] = $exception['line'];
            $counter++;
        }
        
        $this->view->errorTrace = array_reverse($backtrace);
        
        echo $this->view->render('error.php');
    }

}