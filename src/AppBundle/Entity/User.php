<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="EvenementBundle\Entity\evenement" , mappedBy="evenement")
     */

    private $evenement;



    public function __construct()
    {
        parent::__construct();
        // your own logic
    }



    /**
     * Add evenement
     *
     * @param \EvenementBundle\Entity\evenement $evenement
     *
     * @return User
     */
    public function addEvenement(\EvenementBundle\Entity\evenement $evenement)
    {
        $this->evenement[] = $evenement;

        return $this;
    }

    /**
     * Remove evenement
     *
     * @param \EvenementBundle\Entity\evenement $evenement
     */
    public function removeEvenement(\EvenementBundle\Entity\evenement $evenement)
    {
        $this->evenement->removeElement($evenement);
    }

    /**
     * Get evenement
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvenement()
    {
        return $this->evenement;
    }
}
