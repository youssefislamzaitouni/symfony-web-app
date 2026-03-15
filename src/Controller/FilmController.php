<?php

namespace App\Controller;

use App\Entity\Film;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

final class FilmController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(FilmRepository $filmRepository): Response
    {
        $films = $filmRepository->findAll();

        return $this->render('film/index.html.twig', [
            'films' => $films,
        ]);
    }

#[Route('/films', name: 'film_list')]
public function list(FilmRepository $filmRepository): Response
{
    $this->denyAccessUnlessGranted('ROLE_USER');

    $films = $filmRepository->findBy(['user' => $this->getUser()]);

    return $this->render('film/list.html.twig', [
        'films' => $films,
    ]);
}




    #[Route('/films/add', name: 'film_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $em, FilmRepository $filmRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $title = trim($request->request->get('title'));
            $genre = trim($request->request->get('genre'));
            $director = $request->request->get('director');
            $description = $request->request->get('description');
            $rating = $request->request->get('rating');

            if ($title === '' || $genre === '') {
                return new Response('Invalid data', 400);
            }

            $existingFilm = $filmRepository->findOneBy(['title' => $title]);
            if ($existingFilm) {
                return new Response('Already exists', 409);
            }

            $film = new Film();
            $film->setTitle($title);
            $film->setGenre($genre);
            $film->setDirector($director);
            $film->setDescription($description);
            $film->setRating($rating);

            $file = $request->files->get('poster');
            if ($file) {
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('kernel.project_dir') . '/public/uploads', $filename);
                $film->setPoster($filename);
            }

            $em->persist($film);
            $em->flush();

            return $this->redirectToRoute('film_list');
        }

        return $this->render('film/add.html.twig');
    }

#[Route('/films/remove/{id}', name: 'film_remove')]
public function removeFromList(int $id, FilmRepository $filmRepository, EntityManagerInterface $em): Response
{
    $this->denyAccessUnlessGranted('ROLE_USER');

    $film = $filmRepository->find($id);
    if (!$film) return $this->redirectToRoute('film_list');

    if ($film->getUser() === $this->getUser()) {
        $film->setUser(null);
        $em->flush();
    }

    return $this->redirectToRoute('film_list');
}




    #[Route('/movie/{id}', name: 'movie_show')]
    public function movieShow(int $id, FilmRepository $filmRepository): Response
    {
        $film = $filmRepository->find($id);

        if (!$film) {
            throw $this->createNotFoundException('Movie not found');
        }

        return $this->render('film/details.html.twig', [
            'film' => $film,
        ]);
    }
#[Route('/films/save/{id}', name: 'film_save')]
public function saveToList(int $id, FilmRepository $filmRepository, EntityManagerInterface $em): Response
{
    $this->denyAccessUnlessGranted('ROLE_USER');

    $film = $filmRepository->find($id);
    if (!$film) return $this->redirectToRoute('home');

    if ($film->getUser() !== $this->getUser()) {
        $film->setUser($this->getUser());
        $em->flush();
    }

    return $this->redirectToRoute('film_list');
}




}
