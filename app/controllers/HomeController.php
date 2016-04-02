<?php

Class HomeController extends \SlimStarterX\Base\Controller
{

    public function welcome()
    {
        $this->data['title'] = 'Welcome to Slim Starter Application';
        App::render('welcome.twig', $this->data);
    }
}