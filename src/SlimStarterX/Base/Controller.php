<?php

namespace SlimStarterX\Base;

class Controller extends SlimStarterController
{
    protected $enableCsrfValidation = true;
    protected $csrfTokenName = "csrf_token";

    public function __construct()
    {
        parent::__construct();

        /** trigger csrf event */
        $this->csrfEvent();
    }

    /**
     * handle csrf
     */
    protected function csrfEvent()
    {
        if($this->enableCsrfValidation){
            if(\Input::isPost()){
                try
                {
                    \CSRF::check( $this->csrfTokenName, $_POST, true, 60*10, false );
                }
                catch ( Exception $e )
                {
                    // CSRF attack detected
                    // TODO: proper error handling
                    echo $e->getMessage();
                    \App::stop();
                }
            }

            // order important!
            $this->data[$this->csrfTokenName] = \CSRF::generate($this->csrfTokenName);
            $this->data['csrfTokenHiddenInput'] = \CSRF::getAsHiddenInput($this->csrfTokenName);
        }
    }
}
