<?php

/**
 * @Entity(repositoryClass="ToDoListRepository") @Table(name="to_do_lists")
 **/
class ToDoList {
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $projectId;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set projectId
     *
     * @param string $projectId
     *
     * @return ToDoList
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;

        return $this;
    }

    /**
     * Get projectId
     *
     * @return string
     */
    public function getProjectId()
    {
        return $this->projectId;
    }
}
