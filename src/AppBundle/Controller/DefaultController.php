<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\User;
use EvenementBundle\Entity\evenement;



class DefaultController extends Controller
{
    /**
     * @Route("/{_locale}" , defaults={"_locale": "en"}, requirements={ "_locale": "en|fr"} , name="homepage" )
     */
    public function indexAction(Request $request)
    {


        // replace this example code with whatever you need
        return $this->render('base.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/{_locale}/error" , defaults={"_locale": "en"}, requirements={ "_locale": "en|fr"} , name="error" )
     */
    public function errorAction(Request $request)
    {
        return $this->render('error.html.twig') ;
    }


    /**
     * Generate the article feed
     * @return Response XML Feed
     * @Route("/rss"  , name="rss" )
     */
    public function rssAction()
    {

        $evenements = $this->getDoctrine()->getRepository('EvenementBundle:evenement')->findAll();

        return $this->render('rss.html.twig', array(
        'evenements' => $evenements

        ));

    }



}
