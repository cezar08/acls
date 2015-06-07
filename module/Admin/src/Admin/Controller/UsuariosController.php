<?php

namespace Admin\Controller;

use Zend\Validator\Exception\InvalidMagicMimeFileException;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use \Admin\Form\Usuario as UsuarioForm;
use \Admin\Entity\Usuario as Usuario;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

/**
 * Controlador que gerencia os usuários
 *
 * @category Admin
 * @package Controller
 * @author  Cezar Junior de Souza <cezar08@unochapeco.edu.br>
 */
class UsuariosController extends AbstractActionController
{

    /**
     * Exibe os usuários
     * @return void
     */
    public function indexAction()
    {
        $search = isset($_GET['q']) ? $_GET['q'] : null;

        $data = $this->getServiceUser()->fetchAll($search);
        $paginator = new Paginator(
            new DoctrinePaginator(new ORMPaginator($data))
        );

        $paginator
        ->setCurrentPageNumber($this->params()->fromRoute('page'))
        ->setItemCountPerPage(2);

        return new ViewModel(
            array(
                'usuarios' => $paginator
            )
        );
    }

    /**
     * Cria ou edita um usuário
     * @return void
     */
    public function saveAction()
    {
        $em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $form = new UsuarioForm($em);
        $request = $this->getRequest();

        if ($request->isPost()) {
            $usuario = new Usuario();
            $values = $request->getPost();
            $file = $request->getFiles('photo');
            $photo = $this->getServiceUser()->uploadPhoto($file);
            $form->setInputFilter($usuario->getInputFilter());
            $form->setData($values);

            if ($form->isValid()) {				
                $values = $form->getData();
                $values['photo'] = $photo;

                try {
                    $this->getServiceUser()->save($values);
                    $this->flashMessenger()->addSuccessMessage('Usuário armazenado com sucesso');
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                }

                return $this->redirect()->toUrl('/admin/usuarios');
            }
        }

        $id = $this->params()->fromRoute('id', 0);

        if ((int) $id > 0) {
            $form->bind($this->getServiceUser()->get($id));
        }

        return new ViewModel(
            array('form' => $form)
        );
    }

	public function interessesAction()
	{
		$id = $this->params()->fromRoute('id',0);
		
		if ($id > 0) {
			$usuario = $this->getServiceUser()->get($id);

			return new ViewModel(
				array(
					'usuario' => $usuario				
				)
			);
		}

		return $this->redirect()->toUrl('/admin/usuarios');
	}
    

    /**
     * Exclui um usuário
     * @return void
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id', 0);

        if ($id > 0) {
            try {
                $this->getServiceUser()->delete($id);
                $this->flashMessenger()->addSuccessMessage('Usuário excluído com sucesso');
            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage($e->getMessage());
            }
        }

        return $this->redirect()->toUrl('/admin/usuarios');
    }

    public function getPhotoAction()
    {
        header('Content-Type: image');
        $id = (int) $this->params()->fromRoute('id', 0);
        $photo = $this->getServiceUser()->getPhoto($id);
        $view = new ViewModel(array('photo' => $photo));
        $view->setTerminal(true);

        return $view;
    }

    /**
     * @return object
     */
    private function getServiceUser()
    {
        return $this->getServiceLocator()->get('Admin\Service\Usuario');
    }

}
