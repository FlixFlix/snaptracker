<?php

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;

/**
 * @Entity(repositoryClass="ProjectRepository") @Table(name="projects")
 **/
class Project {
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $projectId;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    protected $description;

    /**
     * @Column(type="simple_array", nullable=true)
     */
    protected $clientEmails;

    /**
     * @Column(type="string")
     */
    protected $toDoSetId;

    /**
    * @Column(type="datetime")
     * @var date
     */
    protected $createdAt;

    /**
     * @Column(type="datetime")
     * @var date
     */
    protected $updatedAt;

    public function getId()
    {
        return $this->id;
    }

    public function getProjectId()
    {
        return $this->projectId;
    }

    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Set clientEmails
     *
     * @param array $clientEmails
     *
     * @return Project
     */
    public function setClientEmails($clientEmails)
    {
        $this->clientEmails = $clientEmails;

        return $this;
    }

    /**
     * Get clientEmails
     *
     * @return array
     */
    public function getClientEmails()
    {
        return $this->clientEmails;
    }

    /**
     * Set toDoSetId
     *
     * @param string $toDoSetId
     *
     * @return Project
     */
    public function setToDoSetId($toDoSetId)
    {
        $this->toDoSetId = $toDoSetId;

        return $this;
    }

    /**
     * Get toDoSetId
     *
     * @return string
     */
    public function getToDoSetId()
    {
        return $this->toDoSetId;
    }
}
