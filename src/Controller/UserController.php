<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\User;
use App\Form\UserEditType;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/{firstname}-{lastname}")
     */
    public function index(User $user, Booking $booking)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(User::class, $user);

        $profile = $repository->findOneBy(
            [
                'id' => $user->getId()
            ]
        );
        dump($profile);

        $repositoryBookings = $em->getRepository(Booking::class, $booking);

        $appointments = $repositoryBookings->findBy(
            [
                'user' => $booking->getId(),
                'status' => 'EN_ATTENTE'
            ],
            ['date' => 'ASC']);


        return $this->render('user/index.html.twig',
            [
                'profile' => $profile,
                'appointments' => $appointments
            ]
        );
    }

    /**
     * @Route("/{firstname}-{lastname}/modification")
     */
    public function edit(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();


        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {


                $em->persist($user);
                $em->flush();

                // message de confirmation
                $this->addFlash('success', "La modification est enregistrée");
            } else {
                $this->addFlash('error', 'Le formulaire contient des erreurs');
            }
        }

        return $this->render('user/edit.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
