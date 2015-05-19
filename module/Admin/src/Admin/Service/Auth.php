<?php

namespace Admin\Service;

use Core\Service\Service;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

/**
 * Serviço para autenticação de um usuário simples no sistema
 *
 * @category Admin
 * @package Service
 * @author Cezar
 */
class Auth extends Service
{

    /**
     * Faz a autenticação
     *
     * @param array $params
     * @return array
     */
    public function authenticate($params)
    {
        if (!isset($params['email']) || !isset($params['senha'])) {

            throw new \Exception("Parâmetros inválidos");
        }

        $senha = md5($params['senha']);

        $authService = $this->getServiceManager()->get('Zend\Authentication\AuthenticationService');
        $authAdapter = $authService->getAdapter();
        $authAdapter->setIdentityValue($params['email'])
            ->setCredentialValue($senha);
        $result = $authService->authenticate();

        if (!$result->isValid()) {
            throw new \Exception("Login ou senha inválidos");
        }

        $session = $this->getServiceManager()->get('Session');
	$identity = $result->getIdentity();
        $session->offsetSet('user', $identity);
        $session->offsetSet('role', $identity->getRole());

        return true;
    }

    /**
     * Faz o logout do sistema
     *
     * @return void
     */
    public function logout()
    {
        $Auth = new AuthenticationService();
        $session = $this->getServiceManager()->get('Session');
        $session->offsetUnset('user');
        $session->offsetUnset('role');
        $Auth->clearIdentity();

        return true;
    }

    /**
     * Faz a autorização do usuário para acessar o recurso
     * @param string $moduleName Nome do módulo sendo acessado
     * @param string $controllerName Nome do controller
     * @param string $actionName Nome da ação
     * @return boolean
     */
    public function authorize($moduleName, $controllerName, $actionName)
    {
        $auth = new AuthenticationService();
        $role = 'VISITANTE';
        if ($auth->hasIdentity()) {

            $session = $this->getServiceManager()->get('Session');
            if (!$session->offsetGet('role'))
                $role = 'VISITANTE';
            else
                $role = $session->offsetGet('role');
        }
        $resource = $controllerName . '.' . $actionName;
        $acl = $this->getServiceManager()->get('Core\Acl\Builder')->build();
        if ($acl->isAllowed($role, $resource)) {

            return true;
        }

        return false;
    }
}

