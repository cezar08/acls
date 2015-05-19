<?php

namespace Admin;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Zend\Authentication\AuthenticationService' => function($serviceManager) {

                    return $serviceManager->get('doctrine.authenticationservice.orm_default');
                }
            )
        );
    }

	/**
	 * Executada no bootstrap do módulo
	 * 
	 * @param MvcEvent $e
	 */
	public function onBootstrap($e)
	{
	    /** @var \Zend\ModuleManager\ModuleManager $moduleManager */
	    $moduleManager = $e->getApplication()->getServiceManager()->get('modulemanager');
	    /** @var \Zend\EventManager\SharedEventManager $sharedEvents */
	    $sharedEvents = $moduleManager->getEventManager()->getSharedManager();

	    //adiciona eventos ao módulo
	    $sharedEvents->attach('Zend\Mvc\Controller\AbstractActionController', \Zend\Mvc\MvcEvent::EVENT_DISPATCH, array($this, 'mvcPreDispatch'), 100);
	}

	/**
	 * Verifica se precisa fazer a autorização do acesso
	 * @param  MvcEvent $event Evento
	 * @return boolean
	 */
	public function mvcPreDispatch($event)
	{
	    $di = $event->getTarget()->getServiceLocator();
	    $routeMatch = $event->getRouteMatch();
	    $moduleName = $routeMatch->getParam('module');
	    $controllerName = $routeMatch->getParam('controller');
	    $actionName = $routeMatch->getParam('action');

	    $authService = $di->get('Admin\Service\Auth');

	    if (!$authService->authorize($moduleName, $controllerName, $actionName)) {
		 throw new \Exception('Você não tem permissão para acessar esse recurso!');
	    }
	    

	    return true;
	}
}
