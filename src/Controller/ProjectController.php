<?php

namespace App\Controller;

use App\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{

    /**
     * @Route("/add-project", name="_add_project", methods={"POST"})
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $params = $request->request->all();
        $project = Project::createByName($params['project']['name']);
        $em->persist($project);
        $em->flush();
        return $this->redirectToRoute('_home');
    }
}