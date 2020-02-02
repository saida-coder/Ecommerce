<?php

namespace App\Controller;
use App\Entity\User;

use App\Entity\Vendeur;
use App\Form\VendeurType;
use App\Repository\VendeurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class VendeurController extends AbstractController
{
   /**
     * @Route("/newarticle", name="vendeur_new")
     */
    public function new(Request $request)
    {
        $vendeur = new Vendeur();
        $form = $this->createForm(VendeurType::class, $vendeur);
        $form->handleRequest($request); 


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($vendeur);
            $entityManager->flush();
           

            return $this->redirectToRoute('vendeur');
        }

        return $this->render('vendeur/new.html.twig', [
            'vendeur' => $vendeur,
            
            'form' => $form->createView(),

        ]);
    }
/**
     * @Route("/vendeur_show/{id}", name="vendeur_show")
     */
    public function show(vendeur $vendeur)
    {
        return $this->render('vendeur/show.html.twig', [
            'vendeur' => $vendeur,
        ]);
    }
    
    

    /**
     * @Route("/{id}/edit", name="vendeur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Vendeur $vendeur): Response
    {
        $form = $this->createForm(VendeurType::class, $vendeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('vendeur_index');
        }

        return $this->render('vendeur/edit.html.twig', [
            'vendeur' => $vendeur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("vendeurdelate/{id}", name="vendeur_delete")
     */
    public function delete(Request $request,$id): Response
    {
            $entityManager = $this->getDoctrine()->getManager();
            $vendeur=$entityManager->getRepository(Vendeur::class)->find($id);

            $entityManager->remove($vendeur);
            $entityManager->flush();

        return $this->redirect($this->generateURL('vendeur'));
        
    }
}
