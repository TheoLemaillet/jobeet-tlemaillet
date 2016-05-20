<?php
// src/Ens/TotoBundle/Controller/CategoryController.php

namespace Ens\TotoBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ens\TotoBundle\Entity\Category;

/**
 * Category controller.
 *
 */
class CategoryController extends Controller
{
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('EnsTotoBundle:Category')->findOneBySlug($slug);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $category->setActiveJobs($em->getRepository('EnsTotoBundle:Job')->getActiveJobs($category->getId()));

        return $this->render('EnsTotoBundle:Category:show.html.twig', array(
            'category' => $category,
        ));
    }
}