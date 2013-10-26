<?php

namespace foxp2\backofficeBundle\Controller;

use foxp2\backofficeBundle\Lib\Utils;
use Github;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfilController extends Controller {

    public function indexAction(Request $request) {

        $form_search = $this->createForm(new SearchType());

        $request = $this->container->get('request');

        $search = $request->request->get("search");

        $keyword = strtolower($search);

        $service = $this->container->get('github_api');

        $client = $service->getClient();
        
        // clean up cache 
        
            $cache = $this->container->get('kernel')->getCacheDir() . '/githubapi';

            $finder = new Finder();

            $finder->files()->in($cache);

            $finder->size('> 10K')->date('until 1 days ago')->getIterator();

            foreach($finder as $file) {         

               unlink($file);   

            }

        $client->authenticate($this->container->getParameter('githubtoken'), null, $client::AUTH_URL_TOKEN);

        if ($request->getMethod() == 'POST' && !empty($keyword)) {

            $user = $client->api('user')->find($keyword);

            if (0 === sizeof($user['users'])) {
                $user_exist = "0";
                $result = null;
                $this->get('session')->getFlashBag()->add('error', 'Research with %s did not return any results');
                $this->get('session')->getFlashBag()->add('keyword', $keyword);
            } else {
                foreach ($user['users'] as $user) {                    
                    $result [] = strtolower($user['login']);                    
                }
                if (in_array($keyword, $result)) {
                    $user_exist = "1";                    
                } else {
                    $user_exist = "0";                    
                    $this->get('session')->getFlashBag()->add('error', 'Research with %s did not return any results');
                    $this->get('session')->getFlashBag()->add('keyword', $keyword);
                }
            }
            return $this->render('foxp2backofficeBundle:Profil:index.html.twig', array(
                        'profil_github_name' => $keyword,
                        'keyword' => $keyword,
                        'form_search' => $form_search->createView(),
                        'user_exist' => $user_exist,
                        'results' => $result,
            ));
        } else {

            return $this->render('foxp2backofficeBundle:Profil:index.html.twig', array(
                        'profil_github_name' => $this->container->getParameter('user_github_api'),
                        'form_search' => $form_search->createView(),
                        'keyword' => null,
                        'user_exist' => "1",
            ));
        }
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

        $client->authenticate($this->container->getParameter('githubtoken'), null, $client::AUTH_URL_TOKEN);

        $userApi = $client->api('user');

        $paginator = new Github\ResultPager($client);

        $request = $this->getRequest();

        $parameters = $request->request->get('id');

        $profil = $paginator->fetchAll($userApi, 'show', array($parameters));

        if ($request->isXmlHttpRequest()) {

            $data = array(
                'login' => $profil['login'],
                'name' => array_key_exists('name', $profil) ? '<h3>' . $profil['name'] . '</h3>' : '',
                'location' => array_key_exists('location', $profil) ? '<li><i class="icon-globe"></i> ' . $this->container->get('translator')->transChoice('Location',''). ' : ' . $profil['location'] . '</li>' : '<li><i class="icon-globe"></i> ' . $this->container->get('translator')->transChoice('Location','') . ' : N/C </li>',
                'email' => array_key_exists('email', $profil) ? '<li><i class="icon-envelope"></i> Email : ' . $profil['email'] . '</li>' : '<li><i class="icon-envelope"></i> Email : N/C</li>',
                'blog' => array_key_exists('blog', $profil) ? ( isset($profil['blog']) && (!empty($profil['blog'])) ? '<li><i class="github-untitled-92"></i> Blog : <a class="text-center" href="' . $profil['blog'] . '" target="_blank">' . $this->container->get('translator')->transChoice('External link','') . '</a></li>' : '') : '',
                'company' => array_key_exists('company', $profil) ? '<li><i class="icon-archive"></i> ' . $this->container->get('translator')->transChoice('Company','') . ' : ' . $profil['company'] . '</li>' : '<li><i class="icon-archive"></i> ' . $this->container->get('translator')->transChoice('Company','') . ' : N/C</li>',
                'bio' => array_key_exists('bio', $profil) ? ( isset($profil['bio']) && (!empty($profil['bio'])) ? '<hr><div class="bio"><h3 class="text-left"><i class="icon-book-2"></i> Bio :</h3><br /><blockquote class="text-left">' . nl2br($profil['bio']) . '</blockquote></div>' : '') : '',
                'avatar_url' => $profil['avatar_url'],
                'public_repos' => $profil['public_repos'],
                'public_gists' => $profil['public_gists'],
                'followers' => $profil['followers'],
                'following' => $profil['following'],
                'html_url' => $profil['html_url'],
                'created_at' => date('d m Y', strtotime($profil['created_at'], 0)),
                'size' => '14'
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

        $client->authenticate($this->container->getParameter('githubtoken'), null, $client::AUTH_URL_TOKEN);

        $activitiesApi = $client->api('user');

        $paginator = new Github\ResultPager($client);

        $request = $this->getRequest();

        $parameters = $request->request->get('id');

        $activities = $paginator->fetchAll($activitiesApi, 'publicEvents', array($parameters));

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

        $client->authenticate($this->container->getParameter('githubtoken'), null, $client::AUTH_URL_TOKEN);

        $foxactivitiesApi = $client->api('user');

        $paginator = new Github\ResultPager($client);

        $request = $this->getRequest();

        $parameters = $request->request->get('name');

        $activities = $paginator->fetchAll($foxactivitiesApi, 'publicEvents', array($parameters));

        if ($request->isXmlHttpRequest()) {

            $id = $request->request->get('id');

            foreach ($activities as $value) {

                if ($value['id'] === $id) {

                    switch ($value['type']) {
                        case 'PushEvent':
                            foreach ($value['payload']['commits'] as $payload) {

                                $data[] = array(
                                    'type' => 'PushEvent',
                                    'repo' => $value['repo']['name'],
                                    'sha' => $payload['sha'],
                                    'author' => $payload['author']['name'],
                                    'email' => $payload['author']['email'],
                                    'message' => nl2br($payload['message'])
                                );
                            }
                            break;
                        case 'WatchEvent':
                            $data[] = array(
                                'type' => 'WatchEvent',
                                'action' => $value['payload']['action']
                            );
                            break;
                        case 'IssuesEvent':
                            $data[] = array(
                                'type' => 'IssuesEvent',
                                'action' => $value['payload']['action'],
                                'status' => $value['payload']['issue']['state'],
                                'title' => array_key_exists('title', $value['payload']['issue']) ? $value['payload']['issue']['title'] : '',
                                'body' => array_key_exists('body', $value['payload']['issue']) ? nl2br($value['payload']['issue']['body']) : '',
                                'link' => $value['payload']['issue']['html_url']
                            );
                            break;
                        default:
                            $data[] = array(
                                'type' => 'Undefined'
                            );
                            break;
                    }
                }
            }

            $response = new Response(json_encode($data));

            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }

    public function getajaxrepositoriesAction() {

        $response = null;

        $data = array();

        $service = $this->container->get('github_api');

        $client = $service->getClient();

        $client->authenticate($this->container->getParameter('githubtoken'), null, $client::AUTH_URL_TOKEN);

        $repositoriesApi = $client->api('user');

        $paginator = new Github\ResultPager($client);

        $request = $this->getRequest();

        $parameters = $request->request->get('id');

        $repositories = $paginator->fetchAll($repositoriesApi, 'repositories', array($parameters));

        if ($request->isXmlHttpRequest()) {

            foreach ($repositories as $value) {

                $data[] = array(
                    'id' => $value['id'],
                    'name' => $value['name'],
                    'full_name' => $value['full_name'],
                    'master_branch' => array_key_exists('master_branch',$value) ? $value['master_branch'] : '',
                    'default_branch' => array_key_exists('default_branch',$value) ? $value['default_branch'] : '',
                    'owner' => $value['owner']['login'],
                    'avatar' => $value['owner']['avatar_url'],
                    'homepage' => ($value['homepage'] !== null) ? $value['homepage'] : '',
                    'language' => ($value['language'] !== null) ? $value['language'] : '',
                    'pushed_at' => date('d m Y', strtotime($value['pushed_at'], 0)),
                    'created_at' => date('d m Y', strtotime($value['created_at'], 0)),
                    'updated_at' => date('d m Y', strtotime($value['updated_at'], 0)),
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

    public function getajaxwatchedAction() {

        $response = null;

        $data = array();

        $service = $this->container->get('github_api');

        $client = $service->getClient();

        $client->authenticate($this->container->getParameter('githubtoken'), null, $client::AUTH_URL_TOKEN);

        $userApi = $client->api('user');

        $paginator = new Github\ResultPager($client);

        $request = $this->getRequest();

        $parameters = $request->request->get('id');

        $watched = $paginator->fetch($userApi, 'watched', array($parameters));

        if ($request->isXmlHttpRequest()) {

            foreach ($watched as $value) {
                $data[] = array(
                    'name' => $value['name'],
                    'fullname' => $value['full_name'],
                    'avatar' => $value['owner']['avatar_url'],
                    'owner' => $value['owner']['login'],
                    'html_url' => $value['html_url'],
                    'description' => strip_tags($value['description']),
                    'forks' => $value['forks'],
                    'watchers' => $value['watchers'],
                    'language' => $value['language'],
                    'pushed_at' => date('d m Y', strtotime($value['pushed_at'], 0)),
                    'created_at' => date('d m Y', strtotime($value['created_at'], 0)),
                    'updated_at' => date('d m Y', strtotime($value['updated_at'], 0))
                );
            }

            $response = new Response(json_encode($data));

            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }

    public function getajaxfollowingAction() {

        $response = null;

        $data = array();

        $service = $this->container->get('github_api');

        $client = $service->getClient();

        $client->authenticate($this->container->getParameter('githubtoken'), null, $client::AUTH_URL_TOKEN);

        $userApi = $client->api('user');

        $paginator = new Github\ResultPager($client);

        $request = $this->getRequest();

        $parameters = $request->request->get('id');

        $following = $paginator->fetch($userApi, 'following', array($parameters));

        if ($request->isXmlHttpRequest()) {

            foreach ($following as $value) {

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

    public function getajaxfollowersAction() {

        $response = null;

        $data = array();

        $service = $this->container->get('github_api');

        $client = $service->getClient();

        $client->authenticate($this->container->getParameter('githubtoken'), null, $client::AUTH_URL_TOKEN);

        $userApi = $client->api('user');

        $paginator = new Github\ResultPager($client);

        $request = $this->getRequest();

        $parameters = $request->request->get('id');

        $followers = $paginator->fetch($userApi, 'followers', array($parameters));

//        if($paginator->hasNext()) {
//
//            $paginator->fetchNext();
//
//            $followers = $paginator->fetchNext();
//
//        }

        if ($request->isXmlHttpRequest()) {

            foreach ($followers as $value) {

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

    public function getgistsajaxAction() {

        $response = null;

        $data = array();

        $service = $this->container->get('github_api');

        $client = $service->getClient();

        $client->authenticate($this->container->getParameter('githubtoken'), null, $client::AUTH_URL_TOKEN);

        $gistsApi = $client->api('user');

        $paginator = new Github\ResultPager($client);

        $request = $this->getRequest();

        $parameters = $request->request->get('id');

        $gists = $paginator->fetchAll($gistsApi, 'gists', array($parameters));

        if ($request->isXmlHttpRequest()) {

            foreach ($gists as $value) {

                $data[] = array(
                    'id' => $value['id'],
                    'created_at' => date('d m Y', strtotime($value['created_at'], 0)),
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

        $client->authenticate($this->container->getParameter('githubtoken'), null, $client::AUTH_URL_TOKEN);

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