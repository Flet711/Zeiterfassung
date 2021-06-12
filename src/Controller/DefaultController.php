<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProjectType;
use App\Form\TimeLoggingType;
use App\Services\CsvService;
use App\Services\ProjectService;
use App\Services\TimeLoggingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController {

    /**
     * @Route("/", name="_home")
     * @param Request $request
     * @param TimeLoggingService $loggingService
     * @param ProjectService $projectService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function indexAction(Request $request, TimeLoggingService $loggingService, ProjectService $projectService)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        /** @var User $user */
        $user = $this->getUser();

        $errorMessage = null;
        if ($request->request->get('success') === false) {
            $errorMessage  = 'Logeintrag konnte nicht gefunden werden. Bitte versuchen Sie es erneut oder wenden sich an einen Systemadministrator';
        }
        $projects = $projectService->getAllActiveProjectsAsArray();

        /* Symfonyforms vorbereiten */
        $form = $this->createForm(TimeLoggingType::class, ['userid' => $user->getId(), 'projects' => $projects]);
        $formProject = $this->createForm(ProjectType::class);

        /* Hole alle Logs zur Übersicht aus Service */
        $logs = $loggingService->getAllActiveLogsForUser($user->getId());

        /* Daten für die Reports */
        $dailyReport = $loggingService->getDailyReportDataByUserId($user->getId(), true);
        $monthlyReport = $loggingService->getDailyReportDataByUserId($user->getId(), false);

        return $this->render('landingpage.html.twig',
            [
                'form' => $form->createView(),
                'projectForm' => $formProject->createView(),
                'timelogs' => $logs,
                'errormsg' => $errorMessage,
                'projectList' => $projects,
                'dailyreport' => $dailyReport,
                'monthlyreport' => $monthlyReport
            ]);
    }

    /**
     * @Route("/generate-csv", name="_generate_csv")
     * @param Request $request
     * @param CsvService $csvService
     * @param TimeLoggingService $loggingService
     * @return Response
     */
    public function downloadCsvAction(Request $request, CsvService $csvService, TimeLoggingService $loggingService)
    {
        if ($request->query->get('daily') === '1') {
            $reportData = $loggingService->getDailyReportDataByUserId($this->getUser()->getId(), true);
            $csvName = 'daily_report.csv';
        } else {
            $reportData = $loggingService->getDailyReportDataByUserId($this->getUser()->getId(), false);
            $csvName = 'monthly_report.csv';
        }
        $header = ['Nuter (email)', 'Projekt', 'Benötigte Zeit'];
        $csvService->generateCsvByData($header, $reportData);
        $response = new Response();

        $response->headers->set('Content-Encoding', 'UTF-8');
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$csvName);
        return $response;
    }

}