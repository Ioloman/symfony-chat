<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoomController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('room/index.html.twig', [

        ]);
    }

    public function list(): Response
    {
        return $this->render('room/list.html.twig', [

        ]);
    }
}
