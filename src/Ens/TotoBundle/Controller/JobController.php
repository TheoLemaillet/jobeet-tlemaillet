<?php

namespace Ens\TotoBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Ens\TotoBundle\Entity\Job;
use Ens\TotoBundle\Entity\Category;
use Ens\TotoBundle\Form\JobType;

/**
 * Job controller.
 *
 */
class JobController extends Controller
{
    /**
     * Lists all Job entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        //$jobs = $em->getRepository('EnsTotoBundle:Job')->findAll();

        /*
        $query = $em->createQuery(
          'SELECT j FROM EnsTotoBundle:Job j WHERE j.expires_at > :date'
        )->setParameter('date', date('Y-m-d H:i:s', time()));
        $jobs = $query->getResult();
        */

        //$jobs = $em->getRepository('EnsTotoBundle:Job')->getActiveJobs();

        /*
        return $this->render('EnsTotoBundle:Job:index.html.twig', array(
            'jobs' => $jobs,
        ));
        */


        $categories = $em->getRepository('EnsTotoBundle:Category')->getWithJobs();
        foreach($categories as $category)
        {
            $category->setActiveJobs(
                $em->getRepository('EnsTotoBundle:Job')->getActiveJobs(
                    $category->getId(),
                    $this->container->getParameter('max_jobs_on_homepage')
                )
            );
            $category->setMoreJobs(
                $em->getRepository('EnsTotoBundle:Job')->countActiveJobs($category->getId())
                -
                $this->container->getParameter('max_jobs_on_homepage')
            );
        }

        return $this->render('EnsTotoBundle:Job:index.html.twig', array(
            'categories' => $categories
        ));

    }

    /**
     * Creates a new Job entity.
     *
     */
    public function newAction(Request $request)
    {
        $job = new Job();
        $job->setType('full-time');
        $form = $this->createForm('Ens\TotoBundle\Form\JobType', $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /*
            $job->file->move(
                $this->get('kernel')->getRootDir() . "/../web/uploads/jobs",
                $job->file->getClientOriginalName()
            );
            $job->setLogo($job->file->getClientOriginalName());
            */

            $em->persist($job);
            $em->flush();

            return $this->redirectToRoute('ens_job_show', array(
                'company' => $job->getCompanySlug(),
                'location' => $job->getLocationSlug(),
                'id' => $job->getId(),
                'position' => $job->getPositionSlug()
            ));
        }

        return $this->render('EnsTotoBundle:Job:new.html.twig', array(
            'job' => $job,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Job entity.
     *
     */
    public function showAction(Job $job)
    {

        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository('EnsTotoBundle:Job')->getActiveJob($job->getId());

        if (!$job) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }

        $deleteForm = $this->createDeleteForm($job);

        return $this->render('EnsTotoBundle:Job:show.html.twig', array(
            'job' => $job,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Job entity.
     *
     */
    public function editAction(Request $request, Job $job)
    {
        $deleteForm = $this->createDeleteForm($job);
        $editForm = $this->createForm('Ens\TotoBundle\Form\JobType', $job);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($job);
            $em->flush();

            return $this->redirectToRoute('ens_job_edit', array('token' => $job->getToken()));
        }

        return $this->render('EnsTotoBundle:Job:edit.html.twig', array(
            'job' => $job,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
//FIXME Verifier si j'ai bien compris ctrl+f "modifiez JobController pour utiliser le jeton"
    }

    /**
     * Deletes a Job entity.
     *
     */
    public function deleteAction(Request $request, Job $job)
    {
        $form = $this->createDeleteForm($job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($job);
            $em->flush();
        }

        return $this->redirectToRoute('ens_job_index');
    }

    /**
     * Creates a form to delete a Job entity.
     *
     * @param Job $job The Job entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Job $job)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ens_job_delete', array('token' => $job->getToken())))
            ->add('token', HiddenType::class)
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
