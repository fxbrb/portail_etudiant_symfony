<?php

namespace App\Controller;

use App\Entity\Matiere;
use App\Form\MatiereType;
use App\Repository\MatiereRepository;
use App\Repository\NoteRepository;
use App\Service\Utile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MatiereController extends AbstractController
{
    /**
     * @Route("/matiere", name="matiere")
     */
    public function index(Request $request, Utile $utile): Response
    {
        $em = $this->getDoctrine()->getManager();

        $matiere = new Matiere();
        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $slug = $utile->generateUniqueSlug($matiere->getNom(), 'Matiere');
            $matiere->setSlug($slug);
            $em->persist($matiere);
            $em->flush();

            $this->addFlash('success', 'Matiere ajouté');
        }
    
        $matieres = $em->getRepository(Matiere::class)->findAll();

        return $this->render('matiere/index.html.twig', [
            'matieres' => $matieres,
            'ajout_matiere' => $form->createView()
        ]);
    }

    /**
     * @Route("/matiere/{slug}", name="show_matiere")
     */
    public function show(Matiere $matiere = null){
        if($matiere == null){
            $this->addFlash('error', 'Matiere introuvable');
            return $this->redirectToRoute('matiere');
        }

        return $this->render('matiere/show.html.twig', [
            'matiere' => $matiere
        ]);
    }

    /**
     * @Route("/moyenne_matiere/", name="moyenne-matiere")
     */

    public function moyennematiere(Request $request, NoteRepository $noteRepository, MatiereRepository $matiereRepository){

        if ($request->isMethod('POST')) {

            $matiereid = $request->request->get('matiereid'); // on récupère l'id de la matière transmis par le formulaire

            $matiere = $matiereRepository->findBy(['id' => $matiereid] ); // on recherche la matière concernée pour la réponse avec le twig

            $notes = $noteRepository->findBy(['id' => $matiereid] ); // on recherche l'ensemble des notes dont la matière a un id similaire a celui transmis

            $compte = count($notes); // création d'une variable contenant le nombre total de notes

            $somme = array_sum($notes); // on calcule la somme de toutes les notes
            // attention il faut absolument que les valeurs sortant de la bdd soient des INT

            $moyenne = $somme / $compte; // on calcule bêtement la moyenne

            return $this->render('affichage_moyenne_matiere.html.twig', ['matiere' => $matiere->getNom(), 'moyenne' => $moyenne]); // redirection vers la page d'affichage
        }

        else {
            return $this->render('erreur_moyenne.html.twig'); // redirection vers la page d'erreur
        }


    }
}
