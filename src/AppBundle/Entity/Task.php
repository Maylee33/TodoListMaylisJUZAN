<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Liste;
use Symfony\Component\Validator\Constraints as Assert;


/**
* @ORM\Entity
* @ORM\Table(name="task")
*/

class Task
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=255)
     * @Assert\NotBlank
     */
    private $content;


    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=6, options={"default"="undone"})
     */
    private $status = 'undone';

    /**
     * @ORM\ManyToOne(targetEntity="Liste", inversedBy="tasks")
     * @ORM\JoinColumn(name="liste_id", referencedColumnName="id")
     */

    private $liste;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Task
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Task
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
  /**
     * Get the value of Liste

     *
     * @return mixed
     */
    public function getListe()
    {
        return $this->liste;
    }

    /**
     * Set the value of Liste

     *
     * @param mixed Liste

     *
     * @return self
     */
    public function setListe($liste)
    {
        $this->liste = $liste;

        return $this;
    }

}
