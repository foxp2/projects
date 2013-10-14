<?php
namespace foxp2\backofficeBundle\Controller;

use foxp2\backofficeBundle\Entity\Articles;
use foxp2\backofficeBundle\Form\ArticlesType;
use foxp2\backofficeBundle\Form\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Articles controller.
 *
 */
class ArticlesController extends Controller
{

    /**
     * Lists all Articles entities.
     *
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        $counter = $em->getRepository('foxp2backofficeBundle:Articles')->getArticlesCount();

        $result_per_page = $this->container->getParameter('article_per_page');

        $number_of_page = ceil($counter / $result_per_page);

        $offset = ( $page - 1 ) * $result_per_page;

        if ($page < 1 or $page > $number_of_page && $counter != 0) {

            $this->get('session')->getFlashBag()->add('error', 'This page does not exist !');
            return $this->redirect($this->generateUrl('articles_index'));
        }

        $entities = $em->getRepository('foxp2backofficeBundle:Articles')->getAllArticlesList($result_per_page, $offset);

        $form_search = $this->createForm(new SearchType());

        return $this->render('foxp2backofficeBundle:Articles:index.html.twig', array(
            'keyword' => '',
            'page' => $page,
            'nb_pages' => $number_of_page,
            'counter' => $counter,
            'form_search' => $form_search->createView(),
            'entities' => $entities,
        ));
    }
    /**
     *
     * @param Request $request
     * @return type
     */
    public function searchAction(Request $request) {

        $result_per_page = $this->container->getParameter('article_per_page');

        $em = $this->getDoctrine()->getManager();

        $request = $this->container->get('request');

        $form_search = $this->createForm(new SearchType());

        $form_search->submit($request);

        if ($request->getMethod() == 'POST') {
            $keyword = $request->request->get("search");

            $entity = $em->getRepository('foxp2backofficeBundle:Articles')->findArticleyByName(trim($keyword));          

            if ($entity) {

                $result = sizeof($entity);

                if ($result > 1) {

                    $number_of_page = ceil($result / $result_per_page);

                    $this->get('session')->getFlashBag()->add('message', 'Research with %s has returned %s results');
                    $this->get('session')->getFlashBag()->add('keyword', $keyword);
                    $this->get('session')->getFlashBag()->add('result', $result);
                    
                    return $this->render('foxp2backofficeBundle:Articles:index.html.twig', array(
                                'page' => 1,
                                'counter' => $result,
                                'nb_pages' => $number_of_page,
                                'keyword' => $keyword,
                                'form_search' => $form_search->createView(),
                                'entities' => $entity,
                    ));
                } else {

                    $aloneentity = $em->getRepository('foxp2backofficeBundle:Articles')->find($entity[0]->getId());                  

                    $deleteForm = $this->createDeleteForm($entity[0]->getId());

                    $this->get('session')->getFlashBag()->add('message', 'Research with %s only returned this result');
                    $this->get('session')->getFlashBag()->add('keyword', $keyword);

                    return $this->render('foxp2backofficeBundle:Articles:show.html.twig', array(
                                'entity' => $aloneentity,
                                'category_name' => $aloneentity->getCategory()->getCategoriesName(),
                                'delete_form' => $deleteForm->createView(),
                    ));
                }
            } else {

                 $this->get('session')->getFlashBag()->add('error', 'Research with %s did not return any results');
                 $this->get('session')->getFlashBag()->add('keyword', $keyword);

                return $this->redirect($this->generateUrl('articles_index'));
            }
        }
    }
    /**
     * Creates a new Articles entity.
     *
     */
    public function createAction(Request $request) {

        if (false === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

            $this->get('session')->getFlashBag()->add('error', 'This action requires administrator rights !');
            return $this->redirect($this->generateUrl('articles_index'));
        } else {
            $entity = new Articles();
            $form = $this->createForm(new ArticlesType(), $entity, array('gists' => $this->getListofGist()));
            $form->submit($request);

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                $this->get('session')->getFlashBag()->add('message', 'Article %s was successfully created');
                $this->get('session')->getFlashBag()->add('keyword', $entity->getArticleName());
                        
                return $this->redirect($this->generateUrl('articles_show', array('id' => $entity->getId())));
            }

            return $this->render('foxp2backofficeBundle:Articles:new.html.twig', array(
                        'entity' => $entity,
                        'form' => $form->createView(),
            ));
        }
    }

    /**
     * Displays a form to create a new Articles entity.
     *
     */
    public function newAction()
    {
        $entity = new Articles();

        $form   = $this->createForm(new ArticlesType(), $entity, array('gists' => $this->getListofGist()));

        return $this->render('foxp2backofficeBundle:Articles:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Articles entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('foxp2backofficeBundle:Articles')->find($id);

        if (!$entity) {
            $this->get('session')->getFlashBag()->add('message', 'This article does not exist !');
            return $this->redirect($this->generateUrl('articles_index'));
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('foxp2backofficeBundle:Articles:show.html.twig', array(
            'entity'      => $entity,
            'category_name' => $entity->getCategory()->getCategoriesName(),
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Articles entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('foxp2backofficeBundle:Articles')->find($id);

        if (!$entity) {
            $this->get('session')->getFlashBag()->add('message', 'This article does not exist !');
            return $this->redirect($this->generateUrl('articles_index'));
        }

        $editForm = $this->createForm(new ArticlesType(), $entity, array('gists' => $this->getListofGist($entity->getArticleGistReference()),'gist_selected' => $entity->getArticleGistReference()));
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('foxp2backofficeBundle:Articles:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Articles entity.
     *
     */
    public function updateAction(Request $request, $id) {
        if (false === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

            $this->get('session')->getFlashBag()->add('error', 'This action requires administrator rights !');
            return $this->redirect($this->generateUrl('articles_index'));
        } else {
            $em = $this->getDoctrine()->getManager();

            $entity = $em->getRepository('foxp2backofficeBundle:Articles')->find($id);

            if (!$entity) {
                    $this->get('session')->getFlashBag()->add('message', 'This article does not exist !');
                    return $this->redirect($this->generateUrl('articles_index'));
            }

            $deleteForm = $this->createDeleteForm($id);
            $editForm = $this->createForm(new ArticlesType(), $entity, array('gists' => $this->getListofGist($entity->getArticleGistReference())));
            $editForm->submit($request);

            if ($editForm->isValid()) {

                $em->persist($entity);
                $em->flush();

                $this->get('session')->getFlashBag()->add('message', 'The %s article has been updated successfully');
                $this->get('session')->getFlashBag()->add('keyword', $entity->getArticleName());
                
                return $this->redirect($this->generateUrl('articles_index'));
            }

            return $this->render('foxp2backofficeBundle:Articles:edit.html.twig', array(
                        'entity' => $entity,
                        'edit_form' => $editForm->createView(),
                        'delete_form' => $deleteForm->createView(),
            ));
        }
    }
    /**
     * Deletes a Articles entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        if (false === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

            $this->get('session')->getFlashBag()->add('error', 'This action requires administrator rights !');
            return $this->redirect($this->generateUrl('articles_index'));
        } else {
            $form = $this->createDeleteForm($id);
            $form->submit($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $entity = $em->getRepository('foxp2backofficeBundle:Articles')->find($id);

                if (!$entity) {
                    $this->get('session')->getFlashBag()->add('message', 'This article does not exist !');
                    return $this->redirect($this->generateUrl('articles_index'));
                }

                $em->remove($entity);
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('message', 'The %s article was removed successfully');
                $this->get('session')->getFlashBag()->add('keyword', $entity->getArticleName());
            }

            return $this->redirect($this->generateUrl('articles_index'));
        }
    }

    /**
     * Creates a form to delete a Articles entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    public function ajaxgistAction() {

        $response = null;

        $data = array();

        $request = $this->getRequest();

        $id = $request->request->get('id');

        $service = $this->container->get('github_api');

        $gists = $service->getClient();

        $gist = $gists->api('gists')->show($id);

        if ($request->isXmlHttpRequest()) {

            if (is_array($gist) && sizeof($gist) > 0) {
                $data[] = array(
                    'avatar' => $gist['user']['avatar_url'],
                    'id' => $gist['id'],
                    'created_at' => date('d m Y', strtotime($gist['created_at'], 0)),
                    'author' => $gist['user']['login'],
                    'description' => $gist['description'],
                    'files' => sizeof($gist['files'])
                );
            }
            $response = new Response(json_encode($data));

            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }

    /**
     *
     * @return array
     */
    private function getListofGist($g = null)
    {

        $gistsindb = array();

        $list = array();

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('foxp2backofficeBundle:Articles')->findAll();

        foreach($entity as $e)
        {
            if ($e->getArticleGistReference() != null)
            {

                $gistsindb[] = $e->getArticleGistReference();

            }
        }

        $service = $this->container->get('github_api');

        $gists = $service->getClient();

        $listgist = $gists->api('users')->gists('foxp2');

        foreach ($listgist as $value) {

            if(!in_array($value['id'],$gistsindb)) {

                $list[$value['id']] = "$value[id]";
            }

        }

        if( $g != null ) {
            $list[$g] = "$g";
        }

        natcasesort($list);

        return $list;
    }
}