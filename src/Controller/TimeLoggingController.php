<?php

namespace App\Controller;

use App\Entity\TimeLogging;
use App\Services\ProjectService;
use App\Services\TimeLoggingService;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TimeLoggingController extends AbstractController
{

    /**
     * @Route("/start-logging", name="_start_logging", methods={"POST"})
     * @param Request $request
     * @return RedirectResponse
     */
    public function startLoggingAction(Request $request)
    {
        $params = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $logEntry = TimeLogging::createByStartdate(
            date_create('now', new DateTimeZone('Europe/Berlin')),
            $params['time_logging']['userid'],
            $params['time_logging']['projectid']
        );
        $em->persist($logEntry);
        $em->flush();
        return $this->redirectToRoute('_home');
    }

    /**
     * @Route("/stop-logging", name="_stop_logging")
     * @param Request $request
     * @param TimeLoggingService $loggingService
     * @return RedirectResponse
     */
    public function stopLoggingAction(Request $request, TimeLoggingService $loggingService)
    {
        $loggingService->stopLogging($request->query->get('logid'));
        return $this->redirectToRoute('_home');
    }

    /**
     * @Route("/edit-log-entry", name="_edit_log_entry")
     * @param Request $request
     * @param TimeLoggingService $loggingService
     * @param ProjectService $projectService
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editLogEntryAction(Request $request, TimeLoggingService $loggingService, ProjectService $projectService)
    {
        $success = true;
        if ($request->getMethod() === 'POST') {
            $em = $this->getDoctrine()->getManager();
            $logEntry = $loggingService->getEntryAsObjectById($request->request->get('id'));
            if (!$logEntry instanceof TimeLogging) {
                return $this->redirectToRoute('_home', ['success' => false]);
            }
            if ($request->request->get('changeproject') !== null) {
                $logEntry->setProjectId($request->request->get('changeproject'));
            }
            $logEntry->setStartdate(date_create_from_format('Y-m-d H:i:s',$request->request->get('startdate') . ' ' . $request->request->get('starttime')));
            if ($request->request->get('deleteenddate') !== null) {
                $logEntry->setEnddate(null);
                $logEntry->setStatecode(1);
            } else {
                if ($request->request->get('enddate') !== null) {
                    $logEntry->setEnddate(date_create_from_format('Y-m-d H:i:s', $request->request->get('enddate') . ' ' . $request->request->get('endtime')));
                }
            }
            $em->persist($logEntry);
            $em->flush();
            return $this->redirectToRoute('_home', ['success' => $success]);
        }
        $logEntry = $loggingService->getEntryAsArrayById($request->query->get('logid'));
        return $this->render('edit.logentry.html.twig', ['logentry' => $logEntry, 'projectList' => $projectService->getAllActiveProjectsAsArray()]);
    }

    /**
     * @Route("/delete-log-entry", name="_delete_log_entry")
     */
    public function deleteLogEntryAction(Request $request, TimeLoggingService $loggingService)
    {
        $loggingService->deleteEntry($request->query->get('logid'));
        return $this->redirectToRoute('_home');
    }
}