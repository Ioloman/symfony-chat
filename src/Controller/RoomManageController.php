<?php

namespace App\Controller;

use App\Entity\Chatroom;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoomManageController extends AbstractController
{
    /**
     * @IsGranted("CHAT_AUTH_WATCH", subject="chatroom")
     */
    public function joinChat(Chatroom $chatroom, EntityManagerInterface $em): JsonResponse
    {
        if ($chatroom->getUsers()->contains($this->getUser())) {
            return $this->json([
                'status' => 'success',
                'message' => 'The user is already in the chat! 😜',
            ]);
        } else {
            $chatroom->addUser($this->getUser());
            $em->flush();
            return $this->json([
                'status' => 'success',
                'message' => 'The user was added to the chat! 😜',
            ]);
        }
    }
}
