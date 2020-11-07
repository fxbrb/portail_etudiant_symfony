<?php

namespace App\Controller;

use App\Entity\Note;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use http\Env\Request;

class NoteController extends AbstractController
{
    /**
     * @Route("/note", name="note")
     */
    public function index(): Response
    {
        return $this->render('note/index.html.twig', [
            'controller_name' => 'NoteController',
        ]);
    }

    /**
     * @Route("/ajout_note", name="ajout-note")
     */


    public function ajoutnote(Request $request){

        if ($request->isMethod('POST')) { // on vérifie que le formulaire a bien été rempli sinon on redirige vers une page d'erreur

            $nouvellenote = new Note(); // creation de l'objet note qui va etre inséré en BDD a partir des valeurs qui sortent du formulaire
            $nouvellenote->setCommentaire($request->request->get('commentaire'));
            $nouvellenote->setMatiere($request->request->get('matiere'));
            $nouvellenote->setNote($request->request->get('note'));

            // insertion BDD classique
            $em = $this->getDoctrine()->getManager();
            $em->persist($nouvellenote);
            $em->flush();

            return $this->render('fin_ajout_note.html.twig'); // redirection vers la page de confirmation
        }

        else {
            return $this->render('erreur_ajout_note.html.twig'); // redirection vers la page d'erreur
        }
    }
}


