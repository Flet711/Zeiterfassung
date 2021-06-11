<?php

namespace App\Services;


use App\Entity\Project;
use App\Entity\TimeLogging;
use App\Repository\ProjectRepository;
use App\Repository\TimeLoggingRepository;

class TimeLoggingService
{
    private $timeLoggingRepo;
    private $projectRepo;

    public function __construct(
        TimeLoggingRepository $timeLoggingRepo,
        ProjectRepository $projectRepo
    )
    {
        $this->timeLoggingRepo = $timeLoggingRepo;
        $this->projectRepo = $projectRepo;
    }

    public function getAllActiveLogsForUser($userId)
    {
        $logs = $this->timeLoggingRepo->findBy(
            [
                'userid' => $userId
            ], ['startdate' => 'ASC']
        );
        $logList = [];
        foreach ($logs as $log) {
            $projekt = $this->projectRepo->findOneBy(
                [
                    'id' => $log->getProjectId()
                ]
            );
            if (!$projekt instanceof Project) {
                continue;
            }
            $logList[] = [
                'logid' => $log->getId(),
                'statecode' => $log->getStatecode(),
                'project' => $projekt->getName(),
                'startdate' => $log->getStartdate()->format('d.m.Y H:i:s'),
                'enddate' => $log->getEnddate() !== null ? $log->getEnddate()->format('d.m.Y H:i:s') : null
            ];
        }
        return $logList;
    }

    public function stopLogging($logId) {
        return $this->timeLoggingRepo->stopLogging($logId);
    }

    public function getEntryAsArrayById($id)
    {
        $logEntry = $this->timeLoggingRepo->findOneBy(
            [
                'id' => $id
            ]
        );
        if (!$logEntry instanceof TimeLogging) {
            return false;
        }
        $project = $this->projectRepo->findOneBy(
            [
                'id' => $logEntry->getProjectId()
            ]
        );
        return [
            'id' => $logEntry->getId(),
            'startdate' => $logEntry->getStartdate(),
            'enddate' => $logEntry->getEnddate(),
            'projectid' => $logEntry->getProjectId(),
            'projectname' => $project !== null ? $project->getName() : null,
            'statecode' => $logEntry->getStatecode(),
            'userid' => $logEntry->getUserid()
        ];
    }

    public function getEntryAsObjectById($id)
    {
        return $this->timeLoggingRepo->findOneBy(
        [
            'id' => $id
        ]
    );
    }

    public function getDailyReportDataByUserId($userId, $daily)
    {
        return $this->timeLoggingRepo->getDataForUserReport($userId, $daily);
    }

    public function deleteEntry($id) {
        return $this->timeLoggingRepo->deleteEntry($id);
    }
}