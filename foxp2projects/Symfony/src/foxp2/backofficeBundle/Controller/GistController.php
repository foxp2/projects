<?php
namespace foxp2\backofficeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Github;

/**
 * Gist controller.
 *
 */
class GistController extends Controller{
    

    public function indexAction() {

        return $this->render('foxp2backofficeBundle:Gist:index.html.twig', array(
                    'Gist' => $this->getListofGist(),
        ));
    }
    
    private function getListofGist() {

        $data = array();

        $service = $this->container->get('github_api');        
        
        $client = $service->getClient();
        
        $userApi = $client->api('user');        
        
        $paginator  = new Github\ResultPager($client);
        
        $parameters = $this->container->getParameter('user_github_api');
        
        $gists = $paginator->fetchAll($userApi, 'gists', array($parameters));  

        foreach ($gists as $value) {

            $data[] = $value;
        }

        return $data;
    }
    
    public function getGistFilesAction() {

        $service = $this->container->get('github_api');

        $gists = $service->getClient();

        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {

            $id = $request->request->get('id');

            $gist = $gists->api('gist')->show($id);

            $cache = $this->container->get('kernel')->getCacheDir() . '/backoffice/gistcode/' . $gist['id'];

            if (!is_dir($cache)) {
                @mkdir($cache, 0777, true);
            }

            foreach ($gist['files'] as $data) {

                if (file_exists($cache . '/' . $data['filename'] . '.cache')) {

                    $dir_created_at = date(DATE_ISO8601, filemtime($cache));

                    if ($gist['updated_at'] > $dir_created_at) {
                        @rmdir($cache);
                        @mkdir($cache, 0777, true);
                        file_put_contents($cache . '/' . $data['filename'] . '.cache', $this->getCode($data['content']));
                    }

                    $file = file_get_contents($cache . '/' . $data['filename'] . '.cache');
                } else {

                    file_put_contents($cache . '/' . $data['filename'] . '.cache', $this->getCode($data['content']));
                    $file = file_get_contents($cache . '/' . $data['filename'] . '.cache');
                }

                $files_data[] = array('language' => $data['language'], 'filename' => $data['filename'], 'raw_url' => $data['raw_url'], 'content' => $file);
            }

            $response = new Response(json_encode($files_data));

            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
    }
    
    protected static function getCode($code) {

        $code = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');

        $encode = '<div class="pre-scrollable-fox"><pre>' . $code . '</pre></div>';

        return $encode;
    }
}
?>