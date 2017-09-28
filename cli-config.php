<?php

require_once "bootstrap.php";

$handler = new Initialize();

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($handler->getEntityManager());
