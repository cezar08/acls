<?php

namespace Admin\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use \Admin\Form\Sexo as SexoForm;
use \Admin\Model\Sexo as Sexo;

/**
 * Controlador que gerencia os usuários
 *
 * @category Admin
 * @package Controller
 * @author  Cezar Junior de Souza <cezar08@unochapeco.edu.br>
 */
class SexosController extends AbstractActionController
{

    /**
     * Exibe os sexos
     * @return void
     */
    public function indexAction()
    {
        $em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $sexos = $em->getRepository('\Admin\Model\Sexo')->findAll(array(), array('desc_sexo'));
        $session = $this->getServiceLocator()->get('Session');
        echo $session->offsetGet('teste'); 
		$session->offsetUnset('teste');		
		exit;

        return new ViewModel(
            array(
                'sexos' => $sexos
            )
        );
    }

    /**
     * Cria ou edita um sexo
     * @return void
     */
    public function saveAction()
    {
        $form = new SexoForm();
        $request = $this->getRequest(); //Pega os dados da requisição
        $em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager'); //EntityManager

        if ($request->isPost()) {
            $values = $request->getPost();

            if ( (int) $values['id'] > 0)
                $sexo = $em->find('\Admin\Model\Sexo', $values['id']);
            else
                $sexo = new Sexo();

            $sexo->setDescSexo($values['desc_sexo']);
            $em->persist($sexo);

            try {
                $em->flush();
            } catch (\Exception $e) {
                echo $e; exit;
            }

            return $this->redirect()->toUrl('/pj/admin/sexos');
        }

        $id = $this->params()->fromRoute('id', 0);

        if ((int) $id > 0) {
            $sexo = $em->find('\Admin\Model\Sexo', $id);
            $form->bind($sexo);
        }


        return new ViewModel(
            array('form' => $form)
        );
    }

    /**
     * Exclui um sexo
     * @return void
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        if ($id > 0) {
            $sexo = $em->find('\Admin\Model\Sexo', $id);
            $em->remove($sexo);

            try {
                $em->flush();
            } catch (\Exception $e) {
                echo $e; exit;
            }
        }

        return $this->redirect()->toUrl('/pj/admin/sexos');
    }

}
