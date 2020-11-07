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

            $this->addFlash('success', 'Matiere ajoutée');
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
    public function show(Matiere $matiere, NoteRepository $noteRepository){
        if($matiere == null){
            $this->addFlash('error', 'Matiere introuvable');
            return $this->redirectToRoute('matiere');
        }

        $notes = $noteRepository->findBy(['matiere' => $matiere] ); // on recherche l'ensemble des notes dont la matière a un id similaire a celui transmis

        $compte = count($notes); // création d'une variable contenant le nombre total de notes

$somme = 0; // on additione tout

foreach ($notes as $note){
    $somme = $somme + $note->getNote();
}
        // attention il faut absolument que les valeurs sortant de la bdd soient des INT

        $moyenne = $somme / $compte; // on calcule bêtement la moyenne

        return $this->render('matiere/show.html.twig', [
            'matiere' => $matiere,
            'moyenne' => $moyenne
        ]);
    }

}
