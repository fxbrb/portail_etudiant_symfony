<?php

namespace App\Controller;

use App\Entity\Module;
use App\Form\ModuleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Utile;
use Symfony\Component\Routing\Annotation\Route;

class ModuleController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, Utile $utile): Response
    {
        $em = $this->getDoctrine()->getManager();

        $module = new Module();
        $form = $this->createForm(ModuleType::class, $module);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $slug = $utile->generateUniqueSlug($module->getNom(), 'Module');
            $module->setSlug($slug);
            $em->persist($module);
            $em->flush();

            $this->addFlash('success', 'Module ajoutée');
        }

        $modules = $em->getRepository(Module::class)->findAll();
        return $this->render('module/index.html.twig', [
            'modules' => $modules,
            'ajout' => $form->createView()
        ]);
    }

    /**
     * @Route("/module/{slug}", name="show_module")
     */
    public function show(Module $module = null){
        if($module == null){
            $this->addFlash('error', 'Module introuvable');
            return $this->redirectToRoute('home');
        }

        return $this->render('module/show.html.twig', [
            'module' => $module
        ]);
    }
}
