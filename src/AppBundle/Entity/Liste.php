<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
// Ajout de la classe ArrayCollection de Doctrine
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Task;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Liste
 *
 * @ORM\Entity
 * @ORM\Table(name="liste")
 *
 */
class Liste
{
    /**
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\Column(type="string", length=150)
     */
    private $title;

    /**
     *
     * @ORM\OneToMany(targetEntity="Task", mappedBy="liste", cascade={"remove"})
     */
    private $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

        /**
     * Get the value of Id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Get the value of Title
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of Title
     *
     * @param mixed title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }


    /**
     * Add task
     *
     * @param \AppBundle\Entity\Task $task
     *
     * @return Liste
     */
    public function addTask(Task $task)
    {
        $this->tasks[] = $task;

        return $this;
    }




    /**
     * Remove task
     *
     * @param \AppBundle\Entity\Task $task
     */
    public function removeTask(Task $task)
    {
        $this->tasks->removeElement($task);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    public function __toString() {
        return $this->getTitle();
    }

    /**
     * Set the value of Tasks
     *
     * @param mixed tasks
     *
     * @return self
     */
    public function setTasks($tasks)
    {
        $this->tasks = $tasks;

        return $this;
    }

}
