<?php

namespace EvenementBundle\Controller;

use EvenementBundle\Entity\evenement;
use EvenementBundle\EvenementBundle;

use ReservationBundle\Entity\reservation;
use EvenementBundle\Form\evenementType;
use EvenementBundle\Form\evenementPlaceType;
use EvenementBundle\Form\evenementComType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use ReservationBundle\Form\reservationType;
use ReservationBundle\Repository\reservationRepository;


class evenementController extends Controller
{
    /**
     * Lists all evenement entities.
     *
     */


    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $event = new Evenement();
        $reservation = new Reservation();



        // formulaire qui affiche le formulaire pour filtrer les evenements selon
        // le lieu
        // le type  de communaute

        $form = $this->get('form.factory')->create('EvenementBundle\Form\evenementRechType', $event);

        $form->handleRequest($request);
    //    $formBook   = $this->get('form.factory')->create('ReservationBundle\Form\reservationType', $reservation);

         if($request->getMethod() == 'POST')
        {

            //on recupère les differents champs du formulaire
            $data = $form->getData();
            $lieu = $data->getLieu();
            $communaute = $data->getCommunaute();

            // selon ce qui est entré par l'utilisateur
            // on fait des tries differents
            if ($lieu == null && $communaute == null)
            {
                $evenements = $em->getRepository('EvenementBundle:evenement')->findAll();
            }
            else if ($lieu != null && $communaute == null)
            {
                $evenements = $em->getRepository('EvenementBundle:evenement')->findBy(array('lieu' => $lieu));
            }
            else if ($lieu == null && $communaute != null)
            {
                $evenements = $em->getRepository('EvenementBundle:evenement')->findBy(array('communaute' => $communaute));
            }
            else
            {
                $evenements = $em->getRepository('EvenementBundle:evenement')->findBy(array('lieu' => $lieu , 'communaute' => $communaute));
            }

        }

         // si l'utilisateur ne rentre rien dans le formualaire , on revoie tour les evenements
         // dans la page evenements
        else
        {
            $evenements = $em->getRepository('EvenementBundle:evenement')->findAll();
        }


        return $this->render('evenement/index.html.twig', array(
            'evenements' => $evenements,
            'formRecherche' => $form->createView(),
        ));
    }

    /**
     * Creates a new evenement entity.
     *
     */
    public function newAction(Request $request)
    {

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
        {

            $evenement = new Evenement();
            $form = $this->createForm('EvenementBundle\Form\evenementType', $evenement);
            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid())
            {
                $data = $form->getData();

                $em = $this->getDoctrine()->getManager();

                if ($evenement->getNombreMax() > 0)
                {
                    $evenement->setUser($this->getUser());
                    $em->persist($evenement);
                    $em->flush();
                    return $this->redirectToRoute('evenement_homepage');
                }
            }

            return $this->render('evenement/new.html.twig', array(
                'evenement' => $evenement,
                'form' => $form->createView(),
            ));
        }

    }

    /**
     * Finds and displays a evenement entity.
     *
     */

    //methode generer pas doctrine
    public function showAction(evenement $evenement)
    {
        $deleteForm = $this->createDeleteForm($evenement);

        return $this->render('evenement/show.html.twig', array(
            'evenement' => $evenement,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing evenement entity.
     *
     */

    //methode generer pas doctrine
    public function editAction(Request $request, evenement $evenement)
    {
        $deleteForm = $this->createDeleteForm($evenement);
        $editForm = $this->createForm('EvenementBundle\Form\evenementType', $evenement);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            //return $this->redirectToRoute('evenement_edit', array('id' => $evenement->getId()));
            return $this->redirectToRoute('evenement_homepage');
        }

        return $this->render('evenement/edit.html.twig', array(
            'evenement' => $evenement,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a evenement entity.
     *
     */

    // on a réécrit la fonction de suppression d'un evenement
    public function deleteAction(Request $request , $id)
    {
        $evenement = new Evenement();
        $reservation = new Reservation();

        $em = $this->getDoctrine()->getManager();

        // on récupère les evenements enregistrer dans la table reservation
        // la recherche s'établie par l'identifiant de l'événement
        $repository = $em->getRepository("ReservationBundle:reservation");

        // la methode findEvenement a été défini dans le repository de reservation
        $reservation = $repository->FindEvenement($id);

        // on supprime tout les evenements récupéré
        foreach($reservation as $r)
        {
            $em->remove($r);
        }
        // on met a jour la table
        $em->flush();

        // a ce stade on a plus de tuple de l'évenement dans la table reservation

        $repository = $em->getRepository("EvenementBundle:evenement");

        // on récupère l'évenement dans la table evenements
        $evenement = $repository->findOneBy(array('id' => $id));

        // on supprime l'évenement de la table evenement
        $em->remove($evenement);
        $em->flush();

        return $this->redirectToRoute('evenement_homepage');

    }

    /**
     * Creates a form to delete a evenement entity.
     *
     * @param evenement $evenement The evenement entity
     *
     * @return \Symfony\Component\Form\Form The form
     */


    private function createDeleteForm(evenement $evenement)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('evenement_delete', array('id' => $evenement->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }


}
