<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FilmRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(FilmRepository $filmRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); 

        $films = $filmRepository->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'films' => $films
        ]);
    }
#[Route('/admin/delete/{id}', name: 'admin_film_delete')]
public function deleteFilm(int $id, FilmRepository $filmRepository, EntityManagerInterface $em): Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $film = $filmRepository->find($id);
    if ($film) {
        $em->remove($film);
        $em->flush();
    }

    return $this->redirectToRoute('admin_dashboard');
}


}
