<?php

namespace App\Controller;

use App\Form\ContactFormType;
use App\Repository\ProjectRepository;
use App\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class DefaultController extends AbstractController
{

    /**
     * Homepage Action.
     *
     * @Route("/", name="index")
     *
     * @param ProjectRepository $repository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(ProjectRepository $repository)
    {
        $recentProjects = $repository->getSortedProjects(5);

        return $this->render('default/index.html.twig', [
            'recentProjects' => $recentProjects
        ]);
    }

    /**
     * Projects Action.
     *
     * @Route("/projects", name="projects")
     *
     * @param ProjectRepository $repository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function projectsListing(ProjectRepository $repository)
    {
        $projects = $repository->getSortedProjects();

        return $this->render('default/projects.html.twig', [
            'projects' => $projects
        ]);
    }

    /**
     * Projects Action.
     *
     * @Route("/project/{id}", name="single_project", requirements={"id"="\d+"})
     *
     * @param int $id
     * @param ProjectRepository $repository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function singleProject(int $id, ProjectRepository $repository)
    {
        $project = $repository->find($id);

        if (null === $project) {
            throw new NotFoundHttpException('Project ' . $id . ' not found!');
        }

        return $this->render('default/project.html.twig', [
            'project' => $project
        ]);
    }

    /**
     * Contact Action.
     *
     * @Route("/contact", name="contact")
     *
     * @param Request $request
     * @param MailerService $mailer
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contact(Request $request, MailerService $mailer)
    {
        $form = $this->createForm(ContactFormType::class);

        $form->handleRequest($request);

        // Send email
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $sendStatus = false;
            try {
                $sendStatus = $mailer->sendMessage(
                    [getenv('CONTACT_EMAIL')],
                    $formData['subject'] . ' | abgeo.dev',
                    'email/contact.html.twig',
                    $formData,
                    'no-replay@abgeo.dev'
                );
            } catch (LoaderError $e) {
            } catch (RuntimeError $e) {
            } catch (SyntaxError $e) {
            }

            $this->addFlash( 'contact-email-status', $sendStatus ? 'success' : 'error');
        }

        return $this->render('default/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
