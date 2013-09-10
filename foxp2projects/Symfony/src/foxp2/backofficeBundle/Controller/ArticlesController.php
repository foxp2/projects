<?php
namespace foxp2\backofficeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use foxp2\backofficeBundle\Entity\Articles;
use foxp2\backofficeBundle\Form\ArticlesType;
use foxp2\backofficeBundle\Form\SearchType;

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

            throw $this->createNotFoundException('page inéxistante !');
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
                    
                    $this->get('session')->getFlashBag()->add('message', 'La recherche avec ' . $keyword . ' a retourné ' . $result . ' résultat(s).');

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
                    
                    $this->get('session')->getFlashBag()->add('message', 'La recherche avec ' . $keyword . ' n\'a retourné que ce résultat.');

                    return $this->render('foxp2backofficeBundle:Articles:show.html.twig', array(                                
                                'entity' => $aloneentity,
                                'delete_form' => $deleteForm->createView(),
                    ));
                }
            } else {

                $this->get('session')->getFlashBag()->add('message', 'La recherche avec  ' . $keyword . ' n\'a retourné aucun résultat.');

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

            $this->get('session')->getFlashBag()->add('error', 'Cette action necessite des droits administrateur !');
            return $this->redirect($this->generateUrl('articles_index'));
        } else {
            $entity = new Articles();
            $form = $this->createForm(new ArticlesType(), $entity, array('gists' => $this->getListofGist()));
            $form->submit($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

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
            throw $this->createNotFoundException('Unable to find Articles entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('foxp2backofficeBundle:Articles:show.html.twig', array(
            'entity'      => $entity,
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
            throw $this->createNotFoundException('Unable to find Articles entity.');
        }

        $editForm = $this->createForm(new ArticlesType(), $entity, array('gists' => $this->getListofGist()));
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

            $this->get('session')->getFlashBag()->add('error', 'Cette action necessite des droits administrateur !');
            return $this->redirect($this->generateUrl('articles_index'));
        } else {
            $em = $this->getDoctrine()->getManager();

            $entity = $em->getRepository('foxp2backofficeBundle:Articles')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Articles entity.');
            }

            $deleteForm = $this->createDeleteForm($id);
            $editForm = $this->createForm(new ArticlesType(), $entity, array('gists' => $this->getListofGist()));
            $editForm->submit($request);

            if ($editForm->isValid()) {
                $em->persist($entity);
                $em->flush();
                
                $this->get('session')->getFlashBag()->add('message', 'L\article ' . $entity->getArticleName() . ' a été mise à jour avec succès.');

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

            $this->get('session')->getFlashBag()->add('error', 'Cette action necessite des droits administrateur !');
            return $this->redirect($this->generateUrl('articles_index'));
        } else {
            $form = $this->createDeleteForm($id);
            $form->submit($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $entity = $em->getRepository('foxp2backofficeBundle:Articles')->find($id);

                if (!$entity) {
                    throw $this->createNotFoundException('Unable to find Articles entity.');
                }

                $em->remove($entity);
                $em->flush();
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
    
    /**
     * 
     * @return array
     */
    private function getListofGist() {
        
        $service = $this->container->get('github_api');

        $gists = $service->getClient();

        $listgist = $gists->api('users')->gists('foxp2');

        foreach ($listgist as $value) {
            $list[$value['id']] = "$value[id]";
        }

        return $list;
    }
}
