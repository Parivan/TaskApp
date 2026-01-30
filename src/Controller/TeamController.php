<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamType;
use App\Manager\TeamManager;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/team')]
final class TeamController extends AbstractController
{
    #[Route(name: 'app_team_index', methods: ['GET'])]
    public function index(TeamRepository $teamRepository): Response
    {
        return $this->render('team/index.html.twig', [
            'teams' => $teamRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_team_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TeamManager $teamManager): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $teamManager->save($team);

            return $this->redirectToRoute('app_team_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('team/new.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_team_show', methods: ['GET'])]
    public function show(Team $team, ProjectRepository $projectRepository, TaskRepository $taskRepository): Response
    {
        return $this->render('team/show.html.twig', [
            'team' => $team,
            'active_projects_count' => $projectRepository->countActiveProjects($team),
            'task_stats' => $taskRepository->countTasksByStatus($team),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_team_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Team $team, TeamManager $teamManager): Response
    {
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $teamManager->save($team);

            return $this->redirectToRoute('app_team_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('team/edit.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_team_delete', methods: ['POST'])]
    public function delete(Request $request, Team $team, TeamManager $teamManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $team->getId(), $request->getPayload()->getString('_token'))) {
            $teamManager->remove($team);
        }

        return $this->redirectToRoute('app_team_index', [], Response::HTTP_SEE_OTHER);
    }
}
