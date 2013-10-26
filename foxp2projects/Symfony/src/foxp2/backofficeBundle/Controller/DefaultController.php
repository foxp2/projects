<?php

namespace foxp2\backofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller {

    public function indexAction() {

        $em = $this->getDoctrine()->getManager();

        $categories_count = $em->getRepository('foxp2backofficeBundle:Categories')->getCategoriesCount();

        $articles_count = $em->getRepository('foxp2backofficeBundle:Articles')->getArticlesCount();
        
        $service = $this->container->get('github_api');
        
        $client = $service->getClient();        
       
        $client->authenticate($this->container->getParameter('githubtoken'), null, $client::AUTH_URL_TOKEN);      
        
        $listgist = sizeof($client->api('users')->gists('foxp2'));

        return $this->render('foxp2backofficeBundle:Default:index.html.twig', array('categories_count' => $categories_count,
                    'gist_count' => $listgist,
                    'articles_count' => $articles_count));
    }
    
    public function aboutAction() {
        return $this->render('foxp2backofficeBundle:Common:about.html.twig', array('bundles' => $this->container->getParameter('kernel.bundles')));
    }

    public function loginAction(Request $request) {
        $request = $this->getRequest();
        $session = $request->getSession();
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('foxp2backofficeBundle:Secured:login.html.twig', array(
                    'last_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
                    'error' => $error,
        ));
    }

    public function login_checkAction() {
        
    }

    public function logoutAction() {
        $request = $this->getRequest();
        $session = $request->getSession();
        $session->remove(SecurityContext::LAST_USERNAME);
    }

}