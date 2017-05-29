<?php
declare(strict_types = 1);

$config = include 'config.php';

$beanFactory = new \bitExpert\Disco\AnnotationBeanFactory(\App\Config\AppConfiguration::class, ['config' => $config]);
\bitExpert\Disco\BeanFactoryRegistry::register($beanFactory);

return $beanFactory;