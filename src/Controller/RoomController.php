<?php

namespace App\Controller;

use App\Repository\ChatroomRepository;
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

    public function list(ChatroomRepository $repository): Response
    {
        return $this->render('room/list.html.twig', [
            'chatrooms' => $repository->findAll()
        ]);
    }
}
