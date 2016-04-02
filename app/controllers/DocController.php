<?php

class DocController extends \SlimStarterX\Base\Controller
{

    public function index($page=array())
    {
        if(!$page){
            App::render('docs/index.twig', $this->data);
        }else{
            $page = 'docs/'.implode('/', $page).'.twig';
            App::render($page, $this->data);
        }
    }
}