<?php

namespace App\Controller;

use App\Entity\Chatroom;
use App\Entity\Message;
use App\Repository\ChatroomRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RoomController extends AbstractController
{
    public function index(Chatroom $chatroom, Request $request, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();

        if ($request->isXmlHttpRequest()) {
            $message = new Message();
            $message->setAuthor($currentUser);
            $message->setChatroom($chatroom);
            $message->setText($request->request->get('message'));

            $entityManager->persist($message);
            $entityManager->flush();

            return $this->render('room/_message.html.twig', ['message' => $message]);
        }

        $users = $chatroom->getUsers()->filter(function ($user) use ($currentUser) {
            return $user != $currentUser;
        });

        return $this->render('room/index.html.twig', [
            'chatroom' => $chatroom,
            'users' => $users
        ]);
    }

    public function list(ChatroomRepository $chatroomRepository, UserRepository $userRepository): Response
    {
        $templateParams = [];

        if ($this->isGranted("ROLE_USER")) {
            $templateParams['chatrooms'] = $chatroomRepository->findChatroomsByUser($this->getUser());
            $templateParams['users'] = $userRepository->findAll();
        }

        return $this->render('room/list.html.twig', $templateParams);
    }

    /**
     * @IsGranted("ROLE_USER")
     */
    public function create(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator
    ): Response {
        $user = $userRepository->find($request->request->get('email'));
        if ($user) {
            $room = new Chatroom();
            $name = $request->request->get('name');
            $name = trim($name) === "" ? null : $name;
            $room->setName($name);
            $room->addUser($user);
            $room->addUser($this->getUser());

            $entityManager->persist($room);
            $entityManager->flush();

            return $this->json([
                'status' => 'success',
                'message' => 'Chatroom was successfully created!',
                'url' => $urlGenerator->generate('roomView', ['id' => $room->getId()])
            ]);
        } else {
            return $this->json(['status' => 'failure', 'message' => "Internal Error! Chatroom wasn't created"]);
        }
    }
}
