<?php

namespace App\Controller;

use App\Entity\Film;
use App\Form\FilmType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminMovieController extends AbstractController
{
    #[Route('/admin/movie/add', name: 'admin_movie_add')]
    public function add(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $film = new Film();
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $posterFile = $form->get('poster')->getData();

            if ($posterFile) {
                $originalFilename = pathinfo($posterFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$posterFile->guessExtension();

                try {
                    $posterFile->move(
                        $this->getParameter('posters_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw $e;
                }

                $film->setPoster($newFilename);
            }

            $film->setUser($this->getUser());

            $em->persist($film);
            $em->flush();

            $this->addFlash('success', 'Movie added successfully!');

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin_movie/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/movie/delete/{id}', name: 'admin_movie_delete')]
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $film = $em->getRepository(Film::class)->find($id);

        if (!$film) {
            throw $this->createNotFoundException('Movie not found');
        }

        $em->remove($film);
        $em->flush();

        $this->addFlash('success', 'Movie deleted successfully.');

        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/admin/movie/edit/{id}', name: 'admin_movie_edit')]
    public function edit(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $film = $em->getRepository(Film::class)->find($id);

        if (!$film) {
            throw $this->createNotFoundException('Movie not found');
        }

        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $posterFile = $form->get('poster')->getData();

            if ($posterFile) {
                $originalFilename = pathinfo($posterFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$posterFile->guessExtension();

                try {
                    $posterFile->move(
                        $this->getParameter('posters_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw $e;
                }

                $film->setPoster($newFilename);
            }

            $em->flush();

            $this->addFlash('success', 'Movie updated successfully!');

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin_movie/edit.html.twig', [
            'form' => $form->createView(),
            'film' => $film
        ]);
    }
}
