<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/default', name: 'app_default')]
    public function index(): Response
    {
        $charts = array();

        $chart1 = array();
        $chart1['id'] = 1;
        $chart2['labels'] = ['2019-09-09', '2020-05-09', '2020-06-12', '2021-09-02', '2022-09-18'];
        $chart1['data'] = [69, 71, 71.5, 71, 72];
        $charts[] = $chart1;

        $chart2 = array();
        $chart2['id'] = 2;
        $chart2['labels'] = ['2019-09-09', '2020-06-12', '2021-09-02', '2022-09-18'];
        $chart2['data'] = [164, 164.5, 165, 165];
        $charts[] = $chart2;

        return $this->render('base.html.twig', [
            'controller_name' => 'DefaultController',
            'charts' => $charts,
        ]);
    }
}
