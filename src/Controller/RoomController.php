<?php

namespace App\Controller;

use App\Entity\Attachment;
use App\Entity\Chatroom;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\AttachmentRepository;
use App\Repository\ChatroomRepository;
use App\Repository\UserRepository;
use App\Service\UploaderHelper;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RoomController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER", subject="chatroom")
     */
    public function index(Chatroom $chatroom, Request $request, UploaderHelper $uploaderHelper, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($request->isXmlHttpRequest()) {
            return $this->handleAjaxMessage($currentUser, $chatroom, $request, $entityManager, $validator, $uploaderHelper);
        }

        $users = $chatroom->getUsers()->filter(function ($user) use ($currentUser) {
            return $user != $currentUser;
        });

        return $this->render('room/index.html.twig', [
            'chatroom' => $chatroom,
            'users' => $users
        ]);
    }

    /**
     * @IsGranted("CHAT_AUTH_ATTACHMENT", subject="chatroom")
     */
    public function getAttachment(Chatroom $chatroom, int $attach_id, UploaderHelper $uploaderHelper, AttachmentRepository $repository): StreamedResponse
    {
        $attachment = $repository->find($attach_id);
        $response = new StreamedResponse(function () use ($attachment, $uploaderHelper) {
            $outputStream = fopen('php://output', 'wb');
            $fileStream = $uploaderHelper->readStream($attachment->getFilepath(), false);

            stream_copy_to_stream($fileStream, $outputStream);
        });
        $response->headers->set('Content-Type', $attachment->getMimeType());
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_INLINE,
            $attachment->getOriginalFilename()
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
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
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->in('id', $request->request->get('email')));
        $users = $userRepository->matching($criteria);
        if (!$users->isEmpty()) {
            $room = new Chatroom();
            if ($request->request->get('private', 'off') == 'on') {
                $room->setType('private');
            }
            $name = $request->request->get('name');
            $name = trim($name) === "" ? null : $name;
            $room->setName($name);
            $room->addUser($this->getUser());
            foreach ($users as $user) {
                $room->addUser($user);
            }

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

    /**
     * @param User $currentUser
     * @param Chatroom $chatroom
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @param UploaderHelper $uploaderHelper
     * @return JsonResponse|Response
     */
    private function handleAjaxMessage(User $currentUser, Chatroom $chatroom, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, UploaderHelper $uploaderHelper): Response|JsonResponse
    {
        $this->denyAccessUnlessGranted("CHAT_AUTH", $chatroom);

        $message = new Message();
        $message->setAuthor($currentUser);
        $message->setChatroom($chatroom);
        $message->setText($request->request->get('message'));

        $entityManager->persist($message);
        $entityManager->flush();
        if ($request->files->get('attachment')) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $request->files->get('attachment');

            $violations = $validator->validate(
                $uploadedFile,
                new Image(['maxSize' => '5m'])
            );
            if ($violations->count() > 0) {
                /** @var ConstraintViolation $violation */
                $violation = $violations[0];
                return $this->json(['error' => $violation->getMessage()]);
            }

            $attachment = new Attachment();
            $message->addAttachment($attachment);
            $attachment->setFilename($uploaderHelper->uploadAttachment($uploadedFile));
            $attachment->setOriginalFilename($uploadedFile->getClientOriginalName() ?? $attachment->getFilename());
            $attachment->setMimeType($uploadedFile->getMimeType() ?? 'application/octet-stream');

            $entityManager->persist($attachment);
        }
        $chatroom->setUpdatedAt(new \DateTime());
        $entityManager->flush();
        return $this->render('room/_message.html.twig', ['message' => $message]);
    }

    /**
     * @IsGranted("ROLE_USER")
     */
    public function search(Request $request, ChatroomRepository $chatroomRepository): Response
    {
        $templateParams['chatrooms'] = $chatroomRepository->findChatroomsByQuery($request->query->get('q', ''));

        return $this->render('room/search.html.twig', $templateParams);
    }
}
