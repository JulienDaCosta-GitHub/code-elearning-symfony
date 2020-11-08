<?php

namespace App\Controller;

use App\Entity\Chapitre;
use App\Form\ChapitreType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChapitreController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, TranslatorInterface $trans): Response
    {
        $em = $this->getDoctrine()->getManager(); // Récupération de doctrine

        $chapitre = new Chapitre();
        $form = $this->createForm(ChapitreType::class, $chapitre);
        $form->handleRequest($request); // analyse la requete HTTP
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($chapitre); // prépare la sauvegarde
            $em->flush(); // execute la sauvegarde

            $this->addFlash(
                'success',
                $trans->trans('chapitre.ajoutee')
            );
        }

        $chapitres = $em->getRepository(Chapitre::class)->findAll();

        return $this->render('chapitre/index.html.twig', [
            'chapitres' => $chapitres,
            'ajout' => $form->createView()
        ]);

    }

    /**
     * @Route("/chapitre/{id}", name="show")
     */
    public function show(Chapitre $chapitre = null, Request $request){ // converti automatiquement l'id en un chapitre
        if($chapitre == null){ // On n'a pas trouvé de chapitre correspondant à l'id
            $this->addFlash(
                'erreur',
                'Le chapitre est introuvable'
            );
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(ChapitreType::class, $chapitre);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($chapitre);
            $em->flush();

            $this->addFlash(
                'success',
                'Chapitre mise à jour'
            );
        }

        return $this->render('chapitre/show.html.twig', [
            'chapitre' => $chapitre,
            'maj' => $form->createView()
        ]);
    }

    /**
     * @Route("/chapitre/delete/{id}", name="delete")
     */
    public function delete(Chapitre $chapitre = null){
        if($chapitre == null){
            $this->addFlash(
                'erreur',
                'Chapitre introuvable'
            );
            return $this->redirectToRoute('home');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($chapitre);
        $em->flush();

        $this->addFlash(
            'success',
            'Chapitre supprimée'
        );

        return $this->redirectToRoute('home');

    }
}
