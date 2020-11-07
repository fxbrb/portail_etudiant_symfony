<?php

namespace App\Controller;

use App\Entity\Matiere;
use App\Form\NoteType;
use App\Entity\Note;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class NoteController extends AbstractController
{
    /**
     * @Route("/note", name="note")
     */
    public function index(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $note = new note();
        $form = $this->createForm(noteType::class, $note);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $em->persist($note);
            $em->flush();

            $this->addFlash('success', 'note ajoutée');
            return $this->render('note/note_ajoutée.html.twig');
        }

        $notes = $em->getRepository(note::class)->findAll();
        return $this->render('note/index.html.twig', [
            'notes' => $notes,
            'ajout' => $form->createView()
        ]);
    }

}


