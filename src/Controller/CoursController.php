<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Form\CoursType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/cours")
 */
class CoursController extends AbstractController
{
    /**
     * @Route("/", name="cours")
     */
    public function index(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $cours = new Cours();
        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($cours);
            $em->flush();

            $this->addFlash('success', 'Cours ajouté');
        }

        $AllCours = $em->getRepository(Cours::class)->findAll();

        return $this->render('cours/index.html.twig', [
            'AllCours' => $AllCours,
            'ajout' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="show_cours")
     */
    public function show(Cours $cours = null, Request $request){
        if($cours == null){
            $this->addFlash('erreur', 'Cours introuvable');
            return $this->redirectToRoute('cours');
        }

        $form = $this->createForm(CoursType::class, $cours);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($cours);
            $em->flush();

            $this->addFlash('success', 'Cours modifié');
        }

        return $this->render('cours/show.html.twig', [
            'cours' => $cours,
            'maj' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete_cours")
     */
    public function delete(Cours $cours = null){
        if($cours == null){
            $this->addFlash('erreur', 'Cours introuvable');
            return $this->redirectToRoute('cours');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($cours);
        $em->flush();

        $this->addFlash('success', 'Cours supprimé');
        return $this->redirectToRoute('cours');
    }
}
