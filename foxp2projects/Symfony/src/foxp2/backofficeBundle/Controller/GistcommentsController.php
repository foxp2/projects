<?php

namespace foxp2\backofficeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use foxp2\backofficeBundle\Entity\Gistcomments;
use foxp2\backofficeBundle\Form\GistcommentsType;

/**
 * Gistcomments controller.
 *
 */
class GistcommentsController extends Controller
{

    /**
     * Lists all Gistcomments entities.
     *
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();         
        
        $counter = sizeof($em->getRepository('foxp2backofficeBundle:Gistcomments')->findAll());
        
        $result_per_page = $this->container->getParameter('article_per_page');
        
        $number_of_page = ceil($counter / $result_per_page);
        
        $offset = ( $page - 1 ) * $result_per_page;
        
        if ($page < 1 or $page > $number_of_page && $counter != 0) {
            
            $this->get('session')->getFlashBag()->add('error', 'This page does not exist !');
            return $this->redirect($this->generateUrl('gistcomments_index'));
        }
        
        $entities = $em->getRepository('foxp2backofficeBundle:Gistcomments')->getAllGistcommentsList($result_per_page, $offset);

        return $this->render('foxp2backofficeBundle:Gistcomments:index.html.twig', array(
            'keyword' => '',
            'page' => $page,
            'nb_pages' => $number_of_page,
            'counter' => $counter,
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Gistcomments entity.
     *
     */
    public function createAction(Request $request) {

        if (false === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

            $this->get('session')->getFlashBag()->add('error', 'This action requires administrator rights !');
            return $this->redirect($this->generateUrl('gistcomments_index'));
        } else {

            $request = $this->getRequest();

            for ($i = 0; $i < ((count($request->get('form')) / 3) - 1); $i++) {

                $entity = new Gistcomments();
                $em = $this->getDoctrine()->getManager();
                $entity->setIdGist($request->get('form')['IdGist' . $i]);
                $entity->setFilenameGist($request->get('form')['FilenameGist' . $i]);
                $entity->setComments($request->get('form')['Comments' . $i]);
                $em->persist($entity);
                $em->flush();
            }
            return $this->redirect($this->generateUrl('gistcomments_show', array('id' => $entity->getId())));
        }
    }

    /**
    * Creates a form to create a Gistcomments entity.
    *
    * @param Gistcomments $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm($idgist)
    {

        $service = $this->container->get('github_api');

        $gists = $service->getClient();

        $gist = $gists->api('gist')->show($idgist);

        foreach ($gist['files'] as $data) {

            $gistdata[] = array('idgist' => $idgist, 'filenameGist' => $data['filename'], 'comments' => '');
        }
        $formBuilder = $this->createFormBuilder();
        for ($i=0; $i<count($gistdata); $i++)
        {
            $formBuilder->add("IdGist$i",'hidden', array('label' => 'Numéro du gist', 'data' => $gistdata[$i]['idgist'],'read_only'=>true));
            $formBuilder->add("FilenameGist$i", 'text', array('label' => 'Nom du fichier', 'data' => $gistdata[$i]['filenameGist'],'read_only' => true));
            $formBuilder->add("Comments$i", 'ckeditor',array('label' => 'commentaires'));
        }
        $form = $formBuilder->getForm();

        return $form;
    }

    /**
     * Displays a form to create a new Gistcomments entity.
     *
     */
    public function newAction()
    {
        $request = $this->getRequest();

        $form = $this->createCreateForm($request->get('id'));

//        $service = $this->container->get('github_api');
//
//        $gists = $service->getClient();
//
//        $gist = $gists->api('gist')->show($request->get('id'));
//
//        foreach ($gist['files'] as $data) {
//
//            $gistdata[] = array('idgist' => $request->get('id'), 'filenameGist' => $data['filename'], 'comments' => '');
//        }
//
//        $formBuilder = $this->createFormBuilder();
//
//        for ($i=0; $i<count($gistdata); $i++)
//        {
//            $formBuilder->add("IdGist$i",'hidden', array('label' => 'Numéro du gist', 'data' => $gistdata[$i]['idgist'],'read_only'=>true));
//            $formBuilder->add("FilenameGist$i", 'text', array('label' => 'Nom du fichier', 'data' => $gistdata[$i]['filenameGist'],'read_only' => true));
//            $formBuilder->add("Comments$i", 'ckeditor',array('label' => 'commentaires'));
//        }
//
//        $form = $formBuilder->getForm();

        return $this->render('foxp2backofficeBundle:Gistcomments:new.html.twig', array(
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Gistcomments entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('foxp2backofficeBundle:Gistcomments')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gistcomments entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('foxp2backofficeBundle:Gistcomments:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Gistcomments entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('foxp2backofficeBundle:Gistcomments')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gistcomments entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('foxp2backofficeBundle:Gistcomments:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Gistcomments entity.
    *
    * @param Gistcomments $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Gistcomments $entity)
    {
        $form = $this->createForm(new GistcommentsType(), $entity, array(
            'action' => $this->generateUrl('gistcomments_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'attr' => array('id' => 'gistcomments_update'),
        ));
        /* commentaire : bouton supprimé, remplacé par la toolbar */
        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Gistcomments entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

            $this->get('session')->getFlashBag()->add('error', 'This action requires administrator rights !');

            return $this->redirect($this->generateUrl('gistcomments_index'));

        } else {

            $em = $this->getDoctrine()->getManager();


            $entity = $em->getRepository('foxp2backofficeBundle:Gistcomments')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Gistcomments entity.');
            }

            $deleteForm = $this->createDeleteForm($id);
            $editForm = $this->createEditForm($entity);
            $editForm->handleRequest($request);

            if ($editForm->isValid()) {
                $em->flush();

                return $this->redirect($this->generateUrl('gistcomments_show', array('id' => $id)));
            }

            return $this->render('foxp2backofficeBundle:Gistcomments:edit.html.twig', array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            ));
        }
    }
    /**
     * Deletes a Gistcomments entity.
     *
     */
    public function deleteAction(Request $request, $id) {

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if (false === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

            $this->get('session')->getFlashBag()->add('error', 'This action requires administrator rights !');
            return $this->redirect($this->generateUrl('gistcomments_index'));

        } else {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $entity = $em->getRepository('foxp2backofficeBundle:Gistcomments')->find($id);

                if (!$entity) {
                    throw $this->createNotFoundException('Unable to find Gistcomments entity.');
                }

                $em->remove($entity);
                $em->flush();
            }

            return $this->redirect($this->generateUrl('gistcomments_index'));
        }
    }

    /**
     * Creates a form to delete a Gistcomments entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('gistcomments_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
