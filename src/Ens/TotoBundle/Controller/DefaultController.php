<?php

namespace Ens\TotoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('EnsTotoBundle:Default:index.html.twig');
    }
}
