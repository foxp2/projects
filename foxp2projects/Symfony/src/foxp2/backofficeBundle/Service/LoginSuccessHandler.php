<?php

namespace foxp2\backofficeBundle\Service;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface {
    
    protected $router;
    protected $security;
    protected $mailer;
    
    public function __construct(Router $router, SecurityContext $security, $mailer)
    {
        $this->router = $router;
        $this->security = $security;
        $this->mailer = $mailer;
    }
    
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        
        if ($this->security->isGranted('ROLE_SUPER_ADMIN'))
        {
            $response = new RedirectResponse($this->router->generate('foxp2backoffice_homepage'));          
        }
        elseif ($this->security->isGranted('ROLE_ADMIN'))
        {
            $session = $request->getSession();
            
            $session->set('admin_ip', $_SERVER['REMOTE_ADDR']);
            
            $message = \Swift_Message::newInstance()
                    ->setSubject('nouvelle connexion d\'un admin')
                    ->setFrom('administrator@foxp2projects.fr.nf')
                    ->setTo('foxp2projects@gmail.com')
                    ->setBody('nouvelle connexion : ' . $_SERVER['REMOTE_ADDR']);
            $this->mailer->send($message);
            
            $session->getFlashBag()->add('message', 'Successful authentication !');
            
            $response = new RedirectResponse($this->router->generate('foxp2backoffice_homepage'));
        } 
        elseif ($this->security->isGranted('ROLE_SPECIAL_GUEST'))
        {
            $session = $request->getSession();
            
            $session->set('special_guest_ip', $_SERVER['REMOTE_ADDR']);
            
            $message = \Swift_Message::newInstance()
                    ->setSubject('nouvelle connexion d\'un invité spécial')
                    ->setFrom('administrator@foxp2projects.fr.nf')
                    ->setTo('foxp2projects@gmail.com')
                    ->setBody('nouvelle connexion : ' . $_SERVER['REMOTE_ADDR']);
            $this->mailer->send($message);
            
            $session->getFlashBag()->add('message', 'Successful authentication !');                        
            $response = new RedirectResponse($this->router->generate('foxp2backoffice_homepage'));
        }
            
        return $response;
    }
}

?>
