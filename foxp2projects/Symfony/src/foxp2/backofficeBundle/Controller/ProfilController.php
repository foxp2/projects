<?php
namespace foxp2\backofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use foxp2\backofficeBundle\Lib\Utils;
use Github;

class ProfilController extends Controller{
    
    public function indexAction() {      
        
        return $this->render('foxp2backofficeBundle:Profil:index.html.twig');
        
    }
    
    public function getlimitAction() {
        
        $response = null;
        
        $request = $this->getRequest();
        
        if ($request->isXmlHttpRequest()) {
            
            $response = new Response(json_encode(Utils::getLimit()));
            
            $response->headers->set('Content-Type', 'application/json');       
        }
        
        return $response;
    }
    
    public function getajaxprofilAction() {
        
        $response = null;
        
        $data = array();
        
        $service = $this->container->get('github_api');

        $client = $service->getClient();    
        
        $userApi = $client->api('user');
        
        $paginator  = new Github\ResultPager($client);
        
        $request = $this->getRequest();
        
        $parameters = $request->request->get('id');
        
        $profil = $paginator->fetch($userApi, 'show', array($parameters));      
        
        if ($request->isXmlHttpRequest()) {
            
                $data = array(
                    'login' => $profil['login'],       
                    'name' => array_key_exists('name', $profil) ? '<h3>' . $profil['name'] . '</h3>' : '',   
                    'location' =>  array_key_exists('location', $profil) ? '<li><i class="icon-globe"></i> Location : ' . $profil['location'] . '</li>' : '<li><i class="icon-globe"></i> Location : N/C </li>',
                    'email' =>  array_key_exists('email', $profil) ?  '<li><i class="icon-envelope"></i> Email : ' . $profil['email'] . '</li>' : '<li><i class="icon-envelope"></i> Email : N/C</li>', 
                    'blog' => array_key_exists('blog', $profil) ? ( isset($profil['blog']) && (!empty($profil['blog'])) ? '<li><i class="github-untitled-92"></i> Blog : <a href="' . $profil['blog'] . '" target="_blank">' . $profil['blog'] . '</a></li>' : '') : '', 
                    'company' => array_key_exists('company', $profil) ? '<li><i class="icon-archive"></i> Entreprise : ' . $profil['company'] .'</li>' : '<li><i class="icon-archive"></i> Entreprise : N/C</li>', 
                    'bio' => array_key_exists('bio', $profil) ? ( isset($profil['bio']) && (!empty($profil['bio'])) ? '<hr><div class="bio"><h3 class="text-left"><i class="icon-book-2"></i> Bio :</h3><br /><blockquote class="text-left">' . nl2br($profil['bio']) . '</blockquote></div>' : '') : '', 
                    'avatar_url' => $profil['avatar_url'],                  
                    'public_repos' => $profil['public_repos'],
                    'public_gists' => $profil['public_gists'],
                    'followers' => $profil['followers'],
                    'following' => $profil['following'],
                    'html_url' => $profil['html_url'],
                    'created_at' => date('d m Y',strtotime($profil['created_at'],0))                    
                );
           
            $response = new Response(json_encode($data));
            
            $response->headers->set('Content-Type', 'application/json');           
        }
        
        return $response;       
    }
    
    public function getajaxactivitiesAction() {

        $response = null;

        $data = array();

        $service = $this->container->get('github_api');

        $client = $service->getClient();

        $activitiesApi = $client->api('user');

        $paginator = new Github\ResultPager($client);

        $parameters = $this->container->getParameter('user_github_api'); 

        $activities = $paginator->fetch($activitiesApi, 'publicEvents', array($parameters));

        $request = $this->getRequest();
        
            foreach ($activities as $value) {
                $data[] = array(
                    'actid' => $value['id'],
                    'type' => $value['type'],
                    'login' => $value['actor']['login'],
                    'avatar' => $value['actor']['avatar_url'],
                    'filescount' => array_key_exists('size', $value['payload']) ? $value['payload']['size'] : '',
                    'created_at' => date('d m Y', strtotime($value['created_at'], 0)),
                    'repo' => $value['repo']['name']                    
                );
            }
            
        if ($request->isXmlHttpRequest()) {

            $response = new Response(json_encode($data));

            $response->headers->set('Content-Type', 'application/json');

        }

        return $response;
    }

    public function getajaxactivityAction() {

        $data = array();

        $service = $this->container->get('github_api');

        $client = $service->getClient();

        $foxactivitiesApi = $client->api('user');

        $paginator = new Github\ResultPager($client);

        $parameters = $this->container->getParameter('user_github_api');

        $activities = $paginator->fetch($foxactivitiesApi, 'publicEvents', array($parameters));       

        $request = $this->getRequest();

        foreach ($activities as $value) {

            if ($value['id'] === $request->get('id')) {

                foreach ($value['payload']['commits'] as $value) {

                    $data[] = array(
                        'sha' => $value['sha'],
                        'author' => $value['author']['name'],
                        'email' => $value['author']['email'],
                        'message' => nl2br($value['message'])
                    );
                }
            }
        }
        if ($request->isXmlHttpRequest()) {

            $response = new Response(json_encode($data));

            $response->headers->set('Content-Type', 'application/json');
            
        } else {
            // test
            $response = $data;
        }
        return $response;
    }
    
    public function getajaxrepositoriesAction()
    {
        $response = null;
        
        $data = array();
        
        $service = $this->container->get('github_api');
        
        $client = $service->getClient();    
        
        $repositoriesApi = $client->api('user');
        
        $paginator  = new Github\ResultPager($client);
        
        $parameters = $this->container->getParameter('user_github_api');
        
        $repositories = $paginator->fetchAll($repositoriesApi, 'repositories', array($parameters));      
        
        $request = $this->getRequest();
        
        if ($request->isXmlHttpRequest()) { 
            
            foreach ($repositories as $value) {
                
                $data[] = array(                  
                    'id' => $value['id'],
                    'name' => $value['name'],
                    'full_name' => $value['full_name'],
                    'master_branch' => $value['master_branch'],
                    'default_branch' => $value['default_branch'],
                    'owner' => $value['owner']['login'],
                    'avatar' => $value['owner']['avatar_url'],
                    'homepage' => ($value['homepage'] !== null) ? $value['homepage'] : '',
                    'language' => ($value['language'] !== null) ? $value['language'] : '',
                    'pushed_at' => date('d m Y',strtotime($value['pushed_at'],0)),
                    'created_at' => date('d m Y',strtotime($value['created_at'],0)),
                    'updated_at' => date('d m Y',strtotime($value['updated_at'],0)),
                    'watchers_count' => $value['watchers_count'],
                    'forks_count' => $value['forks_count'],
                    'open_issues_count' => $value['open_issues_count']
                );
            }
            
            $response = new Response(json_encode($data));
            
            $response->headers->set('Content-Type', 'application/json');      
        }      
        
        return $response; 
        
    }
    
    public function getajaxwatchedAction()
    {
        $response = null;
        
        $data = array();
        
        $service = $this->container->get('github_api');
        
        $client = $service->getClient();        

        $userApi = $client->api('user');
        
        $paginator  = new Github\ResultPager($client);
        
        $parameters = $this->container->getParameter('user_github_api');
        
        $watched = $paginator->fetchAll($userApi, 'watched', array($parameters));      
        
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            
            foreach( $watched as $value) {                
                $data[] = array(
                    'name' => $value['name'],
                    'fullname' => $value['full_name'],
                    'avatar' => $value['owner']['avatar_url'],
                    'owner' => $value['owner']['login'],
                    'html_url' => $value['html_url'],
                    'description' => $value['description'],
                    'forks' => $value['forks'],
                    'watchers' => $value['watchers'],
                    'language' => $value['language'],
                    'pushed_at' => date('d m Y',strtotime($value['pushed_at'],0)),
                    'created_at' => date('d m Y',strtotime($value['created_at'],0)),
                    'updated_at' => date('d m Y',strtotime($value['updated_at'],0))
                );
            }
            
            $response = new Response(json_encode($data));
            
            $response->headers->set('Content-Type', 'application/json'); 
        }
        
        return $response;
        
    }
    
    public function getajaxfollowingAction()
    {
        $response = null;
        
        $data = array();
        
        $service = $this->container->get('github_api');
        
        $client = $service->getClient();
        
        $userApi = $client->api('user');
        
        $paginator  = new Github\ResultPager($client);
        
        $parameters = $this->container->getParameter('user_github_api');
        
        $following = $paginator->fetchAll($userApi, 'following', array($parameters));
        
        $request = $this->getRequest();
        
        if ($request->isXmlHttpRequest()) {
            
            foreach( $following as $value) {
                
                $data[] = array(
                    'login' => $value['login'],
                    'avatar' => $value['avatar_url'],
                    'html_url' => $value['html_url']                    
                );
            }
            
            $response = new Response(json_encode($data));
            
            $response->headers->set('Content-Type', 'application/json'); 
        }
        
        return $response;        
    }
    
    public function getajaxfollowersAction()
    {
        $response = null;
        
        $data = array();
        
        $service = $this->container->get('github_api');
        
        $client = $service->getClient();
        
        $userApi = $client->api('user');
        
        $paginator  = new Github\ResultPager($client);
        
        $parameters = $this->container->getParameter('user_github_api');
        
        $followers = $paginator->fetchAll($userApi, 'followers', array($parameters));
        
        $request = $this->getRequest();
        
        if ($request->isXmlHttpRequest()) {
            
            foreach( $followers as $value) {
                
                $data[] = array(
                    'login' => $value['login'],
                    'avatar' => $value['avatar_url'],
                    'html_url' => $value['html_url']                    
                );
            }
            
            $response = new Response(json_encode($data));
            
            $response->headers->set('Content-Type', 'application/json'); 
        }
        
        return $response;        
    }
    
    public function getgistsajaxAction() 
    {
        $response = null;
        
        $data = array();

        $service = $this->container->get('github_api');

        $client = $service->getClient();
        
        $gistsApi = $client->api('user');
        
        $paginator = new Github\ResultPager($client);
        
        $parameters = $this->container->getParameter('user_github_api');

        $gists = $paginator->fetchAll($gistsApi, 'gists', array($parameters));
        
        $request = $this->getRequest();
        
        if ($request->isXmlHttpRequest()) {

            foreach ($gists as $value) {

                $data[] = array(
                    'id' => $value['id'],
                    'created_at' => date('d m Y',strtotime($value['created_at'],0)),
                    'avatar' => $value['user']['avatar_url']
                );
            }
            
            $response = new Response(json_encode($data));
            
            $response->headers->set('Content-Type', 'application/json');         
        }

        return $response;
    }
    
    public function getgistajaxAction() {
        
        $response = null;
        
        $data = array();

        $service = $this->container->get('github_api');

        $client = $service->getClient();
        
        $gistApi = $client->api('gists');
        
        $paginator = new Github\ResultPager($client);
        
        $request = $this->getRequest();
        
        $parameters = $request->request->get('id');
        
        $gist = $paginator->fetchAll($gistApi, 'show', array($parameters));         

        if ($request->isXmlHttpRequest()) {

            $cache = $this->container->get('kernel')->getCacheDir() . '/backoffice/gistcode/' . $gist['id'];

            if (!is_dir($cache)) {
                @mkdir($cache, 0777, true);
            }

            foreach ($gist['files'] as $value) {

                if (file_exists($cache . '/' . $value['filename'] . '.cache')) {

                    $dir_created_at = date(DATE_ISO8601, filemtime($cache));

                    if ($gist['updated_at'] > $dir_created_at) {
                        @rmdir($cache);
                        @mkdir($cache, 0777, true);
                        file_put_contents($cache . '/' . $value['filename'] . '.cache', $this->getCode($value['content']));
                    }

                    $file = file_get_contents($cache . '/' . $value['filename'] . '.cache');
                } else {

                    file_put_contents($cache . '/' . $value['filename'] . '.cache', $this->getCode($value['content']));
                    $file = file_get_contents($cache . '/' . $value['filename'] . '.cache');
                }

                $data[] = array('language' => $value['language'], 'filename' => $value['filename'], 'raw_url' => $value['raw_url'], 'content' => $file);
            }

            $response = new Response(json_encode($data));

            $response->headers->set('Content-Type', 'application/json');            
        }
        return $response; 
        
    }
    
    protected static function getCode($code) {

        $code = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');

        $encode = '<div class="pre-scrollable-fox"><pre>' . $code . '</pre></div>';

        return $encode;
    }
}
?>