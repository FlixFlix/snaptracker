<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Tools\SchemaTool;

require_once "vendor/autoload.php"; // lets include all autoloaded dependencies
require 'config/config.php'; // lets include configuration files
require 'includes/autoload.php'; // lets include all the libraries we need

class Initialize {
    protected $entityManager;
    protected $tokenStorage;
    protected $connection;
    protected $communicator;

    public function __construct()
    {
        $configurationParams = getConfig();

        $isDevMode = true;
        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src"), $isDevMode);

        // database configuration parameters
        $conn = array(
            'driver' => 'pdo_mysql',
            'dbname' => $configurationParams['dbname'],
            'user' => $configurationParams['dbuser'],
            'password' => $configurationParams['dbpass'],
            'host' => $configurationParams['dbhost'],
            'port' => 3306
        );

        // obtaining the entity manager
        $this->entityManager = EntityManager::create($conn, $config);

        $this->createRequiredTables();

        // obtaining token storage
        $this->tokenStorage = new TokenStorage();

        // obtaining connection class
        $this->connection = new Connect();
    }

    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    protected function createRequiredTables() {
        $schemaManager = $this->entityManager->getConnection()->getSchemaManager();

        if (count($schemaManager->listTables()) < 1) {
            $schemaTool = new SchemaTool($this->entityManager);

            $classes = $this->entityManager->getMetadataFactory()->getAllMetadata();

            $schemaTool->createSchema($classes);
        }
    }
}
