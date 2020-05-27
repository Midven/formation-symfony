<?php

namespace App\Controller;

use App\Service\StatsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(EntityManagerInterface $manager, StatsService $statsService)
    {
        $stats = $statsService->getStats();

        $bestAds = $statsService->getAdsStats('DESC');

        $worstAds = $statsService->getAdsStats('ASC');


        return $this->render('admin/dashboard/index.html.twig', [
            // 'stats' => [
            //     'users' => $users,
            //     'ads' => $ads,
            //     'bookings' => $bookings,
            //     'comments' => $comments
            // ]
            'stats' => $stats,
            'bestAds' => $bestAds,
            'worstAds' => $worstAds,
        ]);
    }
}
