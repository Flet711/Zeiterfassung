<?php

namespace App\Services;


use App\Repository\ProjectRepository;

class ProjectService
{
    private $projectRepo;

    public function __construct(
        ProjectRepository $projectRepo
    )
    {
        $this->projectRepo = $projectRepo;
    }

    public function getAllActiveProjectsAsArray()
    {
        $projectList = [];
        $projects = $this->projectRepo->findBy(
            [
                'statecode' => 1
            ]
        );
        foreach ($projects as $project) {
            $projectList[] =
                [
                    'id' => $project->getId(),
                    'name' => $project->getName()
                ];
        }
        return $projectList;
    }
}

