<?php

namespace foxp2\backofficeBundle\Service;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface {
    
    protected $router;    
    protected $mailer;
    
    public function __construct(Router $router, $mailer)
    {
        $this->router = $router;       
        $this->mailer = $mailer;
    }
    
    public function onLogoutSuccess(Request $request)
    {
        $session = $request->getSession();
        
        $message = \Swift_Message::newInstance()
                ->setSubject('nouvelle deconnexion')
                ->setFrom('administrator@foxp2projects.fr.nf')
                ->setTo('foxp2projects@gmail.com')
                ->setBody('nouvelle déconnexion : ' . $_SERVER['REMOTE_ADDR']);
        $this->mailer->send($message);
        
        $session->clear();           
        $response = new RedirectResponse($this->router->generate('foxp2backoffice_homepage'));           
        return $response;
    }
}
?>
