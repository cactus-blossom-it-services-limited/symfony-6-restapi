<?php

namespace App\Controller;

use App\Entity\Project;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
class ProjectController extends AbstractController
{
    #[Route('/projects', name: 'project_create', methods: ['POST'])]
    public function create(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $project = new Project();
        $project->setName($request->get('name'));
        $project->setDescription($request->get('description'));

        $entityManager->persist($project);
        $entityManager->flush();

        $data = [
            'id' => $project->getId(),
            'name' => $project->getName(),
            'description' => $project->getDescription(),
        ];
        return $this->json($data);
    }

    #[Route('/projects', name: 'project_index', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $projects = $doctrine
            ->getRepository(Project::class)
            ->findAll();

        $data = [];

        foreach ($projects as $project) {
            $data[] = [
                'id' => $project->getId(),
                'name' => $project->getName(),
                'description' => $project->getDescription(),
            ];
        }
        return $this->json($data);
    }

    #[Route('/projects/{id}', name: 'project_show', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $project = $doctrine->getRepository(Project::class)->find($id);

        if (!$project) {
            return $this->json('No project found for $id ' . $id, 404);
        }

        $data = [
          'id' => $project->getId(),
          'name' => $project->getName(),
          'description' => $project->getDescription(),
        ];

        return $this->json($data);
    }

    #[Route('/projects/{id}', name: 'project_update', methods: ['PUT', 'PATCH'] )]
    public function update(ManagerRegistry $doctrine, Request $request, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $project = $doctrine->getRepository(Project::class)->find($id);

        if (!$project) {
            return $this->json('No project found for $id ' . $id, 404);
        }

        $project->setName($request->get('name'));
        $project->setDescription($request->get('description'));
        $entityManager->flush();

        $data = [
          'id' => $project->getId(),
          'name' => $project->getName(),
          'description' => $project->getDescription(),
        ];

        return $this->json($data);
    }

    #[Route('/projects/{id}', name: 'project_delete', methods: ['DELETE'] )]
    public function delete(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $project = $doctrine->getRepository(Project::class)->find($id);

        if(!$project) {
            return $this->json('No project found for $id ' . $id, 404);
        }

        $entityManager->remove($project);
        $entityManager->flush();

        return $this->json('Deleted a project successfully with id ' . $id);
    }
}