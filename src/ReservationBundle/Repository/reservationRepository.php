<?php

namespace ReservationBundle\Repository;

/**
 * reservationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class reservationRepository extends \Doctrine\ORM\EntityRepository
{

    // méthode qui permet de récupéré les reservations de l'utilisateur
    // on fait une jointure entre la table Reservation et User
    // avec l'utilisateur qui a le bon identifiant passé en parametre
    public function FindUser($id)
    {
        $qb = $this->createQueryBuilder('r')
            ->Join('r.user' , 'u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->addSelect('u');

        return $qb->getQuery()->getResult();

    }

    // méthode qui permet de récupéré les evenements réservé
    // on fait une jointure entre la table Reservation et evenement
    // avec l'identifiant de l'évenement passé en parametre

    public function FindEvenement($id)
    {
        $qb = $this->createQueryBuilder('r')
                    ->Join('r.evenement' , 'e')
                    ->where('e.id = :id')
                    ->setParameter('id', $id)
                    ->addSelect('e');


        return $qb->getQuery()->getResult();
    }
}
