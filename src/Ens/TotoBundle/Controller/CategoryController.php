<?php
// src/Ens/TotoBundle/Controller/CategoryController.php

namespace Ens\TotoBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Category controller.
 *
 */
class CategoryController extends Controller
{
    public function showAction(Request $request, $slug, $page)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('EnsTotoBundle:Category')->findOneBySlug($slug);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $total_jobs = $em->getRepository('EnsTotoBundle:Job')->countActiveJobs($category->getId());
        $jobs_per_page = $this->container->getParameter('max_jobs_on_category');
        $last_page = ceil($total_jobs / $jobs_per_page);
        $previous_page = $page > 1 ? $page - 1 : 1;
        $next_page = $page < $last_page ? $page + 1 : $last_page;

        $category->setActiveJobs(
            $em->getRepository('EnsTotoBundle:Job')->getActiveJobs(
                $category->getId(),
                $jobs_per_page, ($page - 1) * $jobs_per_page
            ));

        $format = $request->getRequestFormat();

        return $this->render('EnsTotoBundle:Category:show.'.$format.'.twig', array(
            'category' => $category,
            'last_page' => $last_page,
            'previous_page' => $previous_page,
            'current_page' => $page,
            'next_page' => $next_page,
            'total_jobs' => $total_jobs,
            'feedId' => sha1($this
                ->get('router')
                ->generate(
                    'EnsTotoBundle_category',
                    array('slug' =>  $category->getSlug(),
                        '_format' => 'atom'
                    ),
                    true
                )
            ),
        ));
    }
}