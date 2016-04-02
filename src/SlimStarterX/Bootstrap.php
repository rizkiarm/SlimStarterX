<?php

namespace SlimStarterX;

use \Symfony\Component\Translation\Translator;
use \Illuminate\Validation\Factory as ValidationFactory;
use \Illuminate\Validation\DatabasePresenceVerifier;

class Bootstrap extends \SlimStarter\Bootstrap
{
	/**
     * Boot up validator
     */
    public function bootValidator(){
        $this->app->container->singleton('validator', function(){
            $validator = new ValidationFactory(new Translator('en'));
            $validator->setPresenceVerifier(new DatabasePresenceVerifier(\DB::getDatabaseManager()));
            return $validator;
        });
    }

    /**
     * Run the boot sequence
     * @return [type] [description]
     */
    public function boot(){
        parent::boot();
        $this->bootValidator();
    }
}