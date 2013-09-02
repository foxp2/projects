<?php

namespace foxp2\projectsBundle\Controller;

use foxp2\projectsBundle\Entity\Categories;
use foxp2\projectsBundle\Form\CategoriesType;
use foxp2\projectsBundle\Form\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Categories controller.
 *
 */
class CategoriesController extends Controller {

    /**
     * Liste des catégories réordonnées
     *
     */
    public function indexAction($page) {

        $em = $this->getDoctrine()->getManager();

        $counter = $em->getRepository('foxp2projectsBundle:Categories')->getCategoriesCount();

        $result_per_page = $this->container->getParameter('result_per_page');

        $number_of_page = ceil($counter / $result_per_page);

        $offset = ( $page - 1 ) * $result_per_page;

        if ($page < 1 or $page > $number_of_page && $counter != 0) {

            throw $this->createNotFoundException('page inéxistante !');
        }

        $entities = $em->getRepository('foxp2projectsBundle:Categories')->getAllCategoriesList($result_per_page, $offset);

        $form_search = $this->createForm(new SearchType());

        return $this->render('foxp2projectsBundle:Categories:index.html.twig', array(
                    'keyword' => '',
                    'page' => $page,
                    'nb_pages' => $number_of_page,
                    'counter' => $offset,
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

        $em = $this->getDoctrine()->getManager();

        $request = $this->container->get('request');

        $form_search = $this->createForm(new SearchType());

        $form_search->submit($request);

        if ($request->getMethod() == 'POST') {
            $keyword = $request->request->get("search");

            $entity = $em->getRepository('foxp2projectsBundle:Categories')->findCategoryByName(trim($keyword));

            return $this->render('foxp2projectsBundle:Categories:index.html.twig', array(
                        'page' => 1,
                        'nb_pages' => null,
                        'keyword' => $keyword,
                        'form_search' => $form_search->createView(),
                        'entities' => $entity,
            ));
        }
    }

    /**
     * Creates a new Categories entity.
     *
     */
    public function createAction(Request $request) {
        
        $entity = new Categories();

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new CategoriesType(), $entity);

        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('message', 'La nouvelle catégorie ' . $entity->getCategoriesName() . ' a été créé avec succès.');
            return $this->redirect($this->generateUrl('categories_show', array('id' => $entity->getId())));
        }
        return $this->render('foxp2projectsBundle:Categories:new.html.twig', array(
                    'entity' => $entity,
                    'new_form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Categories entity.
     *
     */
    public function newAction() {
        
        $entity = new Categories();

        $form = $this->createForm(new CategoriesType(), $entity);

        return $this->render('foxp2projectsBundle:Categories:new.html.twig', array(
                    'entity' => $entity,
                    'new_form' => $form->createView(),
        ));
    }

    /**
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxshowAction() {

        $request = $this->getRequest();

        $em = $this->getDoctrine()->getManager();

        if ($request->isXmlHttpRequest()) {

            sleep(2); /* temps d'attente pour démo */

            $id = $request->request->get('id');

            $entity = $em->getRepository('foxp2projectsBundle:Categories')->ajaxCategoryByid($id);

            if ($entity) {

                foreach ($entity as $key) {

                    $data = array(
                        'id de la catégorie' => $key->getId(),
                        'parent id de la catégorie' => $key->getParentId()->__tostring(),
                        'nom de la catégorie' => $key->getCategoriesName(),
                        'titre' => $key->getcategoriesTitle(),
                        'sous titre' => $key->getcategoriesSubTitle(),
                        'date de création' => $key->getDateCreated()->format('d-m-Y'),
                        'date de modification' => $key->getDateModified()->format('d-m-Y')
                    );
                }

                $response = new Response(json_encode($data));

                $response->headers->set('Content-Type', 'application/json');

                return $response;
            }
        }
    }

    /**
     * 
     * @param type $id
     * @return Categories entity
     */
    public function showAction($id) {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('foxp2projectsBundle:Categories')->find($id);

        if (!$entity) {
            $this->get('session')->getFlashBag()->add('message', 'Cette catégories n\'existe pas.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('foxp2projectsBundle:Categories:show.html.twig', array(
                    'entity' => $entity,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Categories entity.
     *
     */
    public function editAction($id) {
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('foxp2projectsBundle:Categories')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Cette catégories n\'existe pas.');
        }
        $editForm = $this->createForm(new CategoriesType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('foxp2projectsBundle:Categories:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Categories entity.
     *
     */
    public function updateAction(Request $request, $id) {
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('foxp2projectsBundle:Categories')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Cette catégories n\'existe pas..');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CategoriesType(), $entity);
        $editForm->submit($request);

        if ($id == $entity->getParentId()) {
            $this->get('session')->getFlashBag()->add('message', 'Une catégorie ne peut être parente d\'elle même.');
            return $this->render('foxp2projectsBundle:Categories:edit.html.twig', array(
                        'id' => $id,
                        'entity' => $entity,
                        'edit_form' => $editForm->createView(),
                        'delete_form' => $deleteForm->createView(),
            ));
        }

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('message', 'La catégorie ' . $entity->getCategoriesName() . ' a été mise à jour avec succès.');

            return $this->redirect($this->generateUrl('categories_index'));
        }

        return $this->render('foxp2projectsBundle:Categories:edit.html.twig', array(
                    'id' => $id,
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Categories entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('foxp2projectsBundle:Categories')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Cette catégories n\'existe pas.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('message', 'La catégorie ' . $entity->getCategoriesName() . ' a été supprimée avec succès.');
        }

        return $this->redirect($this->generateUrl('categories_index'));
    }

    /**
     * Creates a form to delete a Categories entity by id.
     *
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

}