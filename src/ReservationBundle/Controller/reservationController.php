<?php

namespace ReservationBundle\Controller;


use ReservationBundle\Entity\reservation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use EvenementBundle\Entity\evenement;
use EvenementBundle\Form\evenementRechType;

use AppBundle\Entity\User;




/**
 * Reservation controller.
 *
 */
class reservationController extends Controller
{
    /**
     * Lists all reservation entities.
     *
     */
    public function indexAction()
    {
        $user = $this->getUser();

        $reservations = new Reservation();
        $evenements = new Reservation();

        $communautes = array();
        $em = $this->getDoctrine()->getManager();
        $reservRep = $em->getRepository('ReservationBundle:reservation');

        $evenRep = $em->getRepository('EvenementBundle:evenement');

        // le grand admin peu voir toute les reservations , et toute les communautes
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN'))
        {
            $reservations = $reservRep->findAll();

        }

        // si l'utilisateur n'est pas un admin , il ne peut voir que ses reservation
        else
        {
            $reservations = $reservRep->FindUser($user->getId());
            $evenements = $evenRep->FindUser($user->getId());
        }

        return $this->render('reservation/index.html.twig', array(
            'reservations' => $reservations,
            'evenements' => $evenements,


        ));
    }


    /**
     * Creates a new reservation entity.
     *
     */
    public function newAction(Request $request , $id)
    {

        $reservation = new Reservation();
        $evenement = new Evenement();

        $form = $this->createForm('ReservationBundle\Form\reservationType', $reservation);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $repository = $em->getRepository('EvenementBundle:evenement');
            $evenement = $repository->findOneBy(array('id' => $id));


            // si le nombre entré par l'utilisateur est suprérieur
            // au nombre de place disponible
            if ($evenement->getNombreMax() < $data->getNombrePlace())
            {
                // la reservation n'est pas pris en compte
                // on renvoie a l'utilsateur la meme page
                return $this->render('reservation/new.html.twig', array(
                    'reservation' => $reservation,
                    'form' => $form->createView(),
                ));
            }

            else
            {
                // dans le cas contraire
                // on enregistre la reservation
                $evenement->setNombreMax($evenement->getNombreMax() - $data->getNombrePlace() );
                $reservation->setEvenement($evenement);
                $reservation->setNombrePlace($evenement->getNombreMax());
                $reservation->setUser($this->getUser());

                $em->persist($reservation);
                $em->flush();
                return $this->redirectToRoute('evenement_homepage');
            }
       }


        return $this->render('reservation/new.html.twig', array(
            'reservation' => $reservation,
            'form' => $form->createView(),
        ));

    }

    /**
     * Finds and displays a reservation entity.
     *
     */
    public function showAction(reservation $reservation)
    {
        $deleteForm = $this->createDeleteForm($reservation);

        return $this->render('reservation/show.html.twig', array(
            'reservation' => $reservation,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing reservation entity.
     *
     */
    public function editAction(Request $request, reservation $reservation)
    {
        $evenement = new Evenement();

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('EvenementBundle:evenement');

        $e = $reservation->getEvenement();

        $evenement = $repository->find($e->getId());

        $nbplace = $reservation->getNombrePlace();


        $deleteForm = $this->createDeleteForm($reservation);
        $editForm = $this->createForm('ReservationBundle\Form\reservationType', $reservation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $place = $editForm->getData();

            // nvxPlace = nombre place du formulaire - le nombre de place reservé au debut
            $nvxPlace = $place->getNombrePlace() - $nbplace;


            $evenement->setNombreMax($evenement->getNombreMax() + $nvxPlace);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reservation_homepage');
          //  return $this->redirectToRoute('reservation_edit', array('id' => $reservation->getId()));
        }

        return $this->render('reservation/edit.html.twig', array(
            'reservation' => $reservation,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a reservation entity.
     *
     */

    // on a redefini la méthode delete
    public function deleteAction($id)
    {

        $reservation = new Reservation();
        $evenement = new Evenement();

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('ReservationBundle:reservation');

        // on récupere la reservation que l'utilisateur veut suprimer
        $reservation = $repository->findOneBy(array('id' => $id));

        // on récupére l'évenement lié a la reservation
        $evenement = $reservation->getEvenement();

        // on ajoute le nombre de place de l'évenement a supprimé
        // au nombre de place actuel de l'évenement concerné
        // et on persiste nos donnée
        $evenement->setNombreMax($evenement->getNombreMax() + $reservation->getNombrePlace() );
        $em->persist($evenement);
        $em->flush();

        $em->remove($reservation);
        $em->flush();


        return $this->redirectToRoute('reservation_homepage');


    }
    /**
     * Creates a form to delete a reservation entity.
     *
     * @param reservation $reservation The reservation entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(reservation $reservation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('reservation_delete', array('id' => $reservation->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
