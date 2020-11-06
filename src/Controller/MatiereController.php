<?php

namespace App\Controller;

use App\Entity\Matiere;
use App\Form\MatiereType;
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
}
