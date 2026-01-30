<?php

namespace App\Controller;

use App\Repository\TeamRepository;
use App\Service\DashboardProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(
        Request $request,
        TeamRepository $teamRepository,
        DashboardProvider $dashboardProvider,
    ): Response {
        $teams = $teamRepository->findBy([], ['name' => 'ASC']);

        $teamId = $request->query->get('team');
        $selectedTeam = null;

        if ($teamId && $teamId !== 'all') {
            $selectedTeam = $teamRepository->find((int) $teamId);
        }

        $stats = $dashboardProvider->getStats($selectedTeam);

        return $this->render('home/index.html.twig', [
            'teams' => $teams,
            'selectedTeam' => $selectedTeam,
            'stats' => $stats,
        ]);
    }
}
