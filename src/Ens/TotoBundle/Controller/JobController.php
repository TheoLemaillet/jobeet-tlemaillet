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
    public function indexAction(Request $request)
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
        $format = $request->getRequestFormat();

        return $this->render('EnsTotoBundle:Job:index.'.$format.'.twig', array(
            'categories' => $categories,
            'lastUpdated' => $em
                ->getRepository('EnsTotoBundle:Job')
                ->getLatestPost()
                ->getCreatedAt()
                ->format(DATE_ATOM),
            'feedId' => sha1($this->get('router')->generate('ens_job_index', array('_format'=> 'atom'), true)),
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

            return $this->redirectToRoute('ens_job_preview', array(
                'company' => $job->getCompanySlug(),
                'location' => $job->getLocationSlug(),
                'token' => $job->getToken(),
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
    public function showAction(Request $request, Job $job)
    {

        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository('EnsTotoBundle:Job')->getActiveJob($job->getId());

        if (!$job) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }


        $session = $request->getSession();

        // fetch jobs already stored in the job history
        $sjobs = $session->get('job_history', array());

        // store the job as an array so we can put it in the session and avoid entity serialize errors
        $sjob = array(
            'id' => $job->getId(),
            'position' =>$job->getPosition(),
            'company' => $job->getCompany(),
            'companyslug' => $job->getCompanySlug(),
            'locationslug' => $job->getLocationSlug(),
            'positionslug' => $job->getPositionSlug()
        );

        if (!in_array($sjob, $sjobs)) {
            // add the current job at the beginning of the array
            array_unshift($sjobs, $sjob);

            // store the new job history back into the session
            $session->set('job_history', array_slice($sjobs, 0, 3));
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

            return $this->redirectToRoute('ens_job_preview', array(
                'company' => $job->getCompanySlug(),
                'location' => $job->getLocationSlug(),
                'token' => $job->getToken(),
                'position' => $job->getPositionSlug()
            ));
        }

        return $this->render('EnsTotoBundle:Job:edit.html.twig', array(
            'job' => $job,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
        //FIXME Verifier si j'ai bien compris ctrl+f "modifiez JobController pour utiliser le jeton"
    }


    public function previewAction(Job $job)
    {
        $deleteForm = $this->createDeleteForm($job);
        $publishForm = $this->createPublishForm($job);
        $extendForm = $this->createExtendForm($job);

        return $this->render('EnsTotoBundle:Job:show.html.twig', array(
            'job' => $job,
            'delete_form' => $deleteForm->createView(),
            'publish_form' => $publishForm->createView(),
            'extend_form' => $extendForm->createView(),
        ));
    }
    //FIXME ??????????????????????????????????????? ctrl+f ici la différence avec l'action show

    /**
     * Deletes a Job entity.
     *
     */
    public function deleteAction(Request $request, Job $job)
    {
        $form = $this->createDeleteForm($job);
        $form->handleRequest($request);

        //if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($job);
            $em->flush();
        //}

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
            ->getForm();
    }


    public function publishAction(Request $request, Job $job)
    {
        $form = $this->createPublishForm($job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $job->publish();
            $em->persist($job);
            $em->flush();

            $this->get('session')
                ->getFlashBag()
                ->set(
                    'notice',
                    'Your job is now online for 30 days.'
                );
        }

        return $this->redirect($this->generateUrl('ens_job_preview', array(
            'company' => $job->getCompanySlug(),
            'location' => $job->getLocationSlug(),
            'token' => $job->getToken(),
            'position' => $job->getPositionSlug()
        )));
    }

    private function createPublishForm(Job $job)
    {
        return $this->createFormBuilder(array('token' => $job->getToken()))
            ->add('token', HiddenType::class)
            ->getForm();
    }

    public function extendAction(Request $request, Job $job)
    {
        $form = $this->createExtendForm($job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            if (!$job->extend()) {
                throw $this->createNotFoundException('Unable to find extend the Job.');
            }

            $em->persist($job);
            $em->flush();

            $this->get('session')
                ->getFlashBag()
                ->set(
                    'notice', 
                    sprintf(
                        'Your job validity has been extended until %s.', 
                        $job->getExpiresAt()->format('m/d/Y')
                    )
                );
        }

        return $this->redirect($this->generateUrl('ens_job_preview', array(
            'company' => $job->getCompanySlug(),
            'location' => $job->getLocationSlug(),
            'token' => $job->getToken(),
            'position' => $job->getPositionSlug()
        )));
    }

    private function createExtendForm(Job $job)
    {
        return $this->createFormBuilder(array('token' => $job->getToken()))
            ->add('token', HiddenType::class)
            ->getForm();
    }
}
