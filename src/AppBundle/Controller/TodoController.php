<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;


use AppBundle\Form\TaskType;

use AppBundle\Entity\Task;
use AppBundle\Entity\Liste;

/**
*
*
*@Route("todolist")
*/
class TodoController extends Controller
{
    /**
    *
    * List all the listes
    * @Route("/", name="liste_index")
    *
    */

        public function indexAction(Request $request)
        {
            $liste = new Liste();
        $em = $this->getDoctrine()->getManager();
        $listes = $em->getRepository('AppBundle:Liste')->findAll();

        $form = $this->createForm('AppBundle\Form\ListeType', $liste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($liste);
            $em->flush();

            return $this->redirectToRoute('liste_show', array('id' => $liste->getId()));
        }

        return $this->render('todos/index.html.twig', array(
            'listes' => $listes,
            'form' => $form->createView(),

        ));
        }
    /**
     * Creates a new liste entity.
     *
     * @Route("/add", name="liste_add")
     * @Method({"GET", "POST"})
     */
    public function addAction(Request $request)
    {
        $liste = new Liste();
        $form = $this->createForm('AppBundle\Form\ListeType', $liste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($liste);
            $em->flush();

            return $this->redirectToRoute('liste_show', array('id' => $liste->getId()));
        }

        return $this->render('todos/show.html.twig', array(
            'liste' => $liste,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a liste entity.
     * and show tasks
     * @Route("/{id}", name="liste_show")
     */
    public function showListeAction($id, Request $request)
    {
        $repository= $this->getDoctrine()->getRepository('AppBundle:Liste');

        $liste =$repository->find($id);
        $tasks = $this->getDoctrine()->getRepository('AppBundle:Task')
            ->findAll();

        $task = new Task();

        // Valeurs par défaut
        //$defaults = ['title' => 'Nouvelle tâche'];

        // Création du formulaire d'ajout
        $form = $this->createForm('AppBundle\Form\TaskType', $task);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $task->setListe($liste);
            $liste->addTask($task);
            $task = $form->getData();

            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'Tâche ajoutée.');
            return $this->redirectToRoute('liste_show', array('id' => $liste->getId()));
        }
        return $this->render('todos/details.html.twig', array(
            'liste' => $liste,
            'tasks'=>$tasks,
            'form'=>$form->createView(),
        ));
    }

/**
     * Changement de statut
     *
     * @Route("/{id}/{status}",
     *  name="task_set_status",
     *  requirements={"id": "\d+", "status": "done|undone"}
     * )
     * @Method("GET")
     */
    public function taskSetStatusAction(Request $request, Task $task = null, $status, EntityManagerInterface $em)
    {

        // Si la tâche n'existe pas
        if($task === null) {
            throw $this->createNotFoundException('Tâche non trouvée');
        }
        // On modifie le statut de la tâche
        $task->setStatus($status);
        $em->flush();

        $this->addFlash('success', 'Statut mis à jour.');

        return $this->render('todos/show.html.twig',
        [
            'task' => $task,]
        );

    }

/**
     * @Route("/task/show/{id}", requirements={"id": "\d+"}, name="task_show")
     * @Method("GET")
     */
    public function taskShowAction(Task $task = null)
    {
        // Si la tâche n'existe pas
        if($task === null) {
            throw $this->createNotFoundException('Tâche non trouvée');
        }

        return $this->render('todos/show.html.twig',[
            'task' => $task,

        ]
    );
    }

    /**
     * Suppression d'une tâche
     *
     * @Route("/task/delete", name="task_delete")
     * @Method("POST")
     */
    public function taskDeleteAction(Request $request, EntityManagerInterface $em)
    {
        $task = $this->getDoctrine()->getRepository(Task::class)
            ->find($request->request->get('id'));

        // Si la tâche n'existe pas
        if($task === null) {
            throw $this->createNotFoundException('Tâche non trouvée');
        }

        // Suppression en bdd
        $em->remove($task);
        $em->flush();

        // Exemple avec plusieurs messages
        $this->addFlash('danger', 'La tâche a bien été supprimée.');


                return $this->render('todos/delete.html.twig', [
                'task' => $task,

            ]);
    }

  /**
     * @Route("/task/edit/{id}", requirements={"id": "\d+"}, name="task_edit")
     *
     * @Method({"GET", "POST"})
     */
         public function taskEditAction($id,Request $request)
    {

        $task = new Task();

        $task = $this->getDoctrine()
        ->getManager()
        ->getRepository('AppBundle:Task')
        ->find($id);

        $form = $this->get('form.factory')->createBuilder(FormType::class, $task)
        ->add('content', null, [
            'label' => false,
        ])
        ->add('status', ChoiceType::class, array(
            'choices'  => array(
                'Done' => 'done',
                'Undone' => 'undone',)))
        ->getForm();

            // Si la requête est en POST
    if ($request->isMethod('POST')) {
      // On fait le lien Requête <-> Formulaire
      // À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur
      $form->handleRequest($request);

      // On vérifie que les valeurs entrées sont correctes
      // (Nous verrons la validation des objets en détail dans le prochain chapitre)
      if ($form->isValid()) {
        // On enregistre notre objet $advert dans la base de données, par exemple
        $em = $this->getDoctrine()->getManager();
        $em->persist($task);
        $em->flush();

        $request->getSession()->getFlashBag()->add('notice', 'Tâche mise à jour.');

        // On redirige vers la page de visualisation de l'annonce nouvellement créée
        return $this->redirectToRoute('task_show', array('id' => $task->getId()));
      }
    }

    // À ce stade, le formulaire n'est pas valide car :
    // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
    // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
    return $this->render('todos/editTask.html.twig', array(
      'form' => $form->createView(),
    ));
    }

  /**
     * Displays a form to edit an existing liste entity.
     *
     * @Route("/{id}/edit", name="liste_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Liste $liste)
    {

        $deleteForm = $this->createDeleteForm($liste);
        $editForm = $this->createForm('AppBundle\Form\ListeType', $liste);

        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->persist($liste);
            $em->flush();

            return $this->redirectToRoute('liste_show', array('id' => $liste->getId()));
        }

        return $this->render('todos/edit.html.twig', array(
            'liste' => $liste,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Deletes a liste entity.
     *
     * @Route("/{id}/delete", name="liste_delete")
     * @Method({"GET", "POST", "DELETE"})
     */
    public function deleteAction(Request $request, Liste $liste)
    {
        $form = $this->createDeleteForm($liste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($liste);
            $em->flush();
            // Flash message
            $this->addFlash('success', 'Liste supprimée.');
        }

        return $this->redirectToRoute('liste_index');
    }

    /**
     * Creates a form to delete a liste entity.
     *
     * @param Liste $liste The liste entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Liste $liste)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('liste_delete', array('id' => $liste->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    }
