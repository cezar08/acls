<?php

namespace Admin\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use \Admin\Entity\Uf as Uf;
use \Admin\Form\Uf as UfForm;

/**
 * Controlador que gerencia os ufs
 *
 * @category Admin
 * @package Controller
 * @author  Cezar Junior de Souza <cezar08@unochapeco.edu.br>
 */
class UfsController extends AbstractActionController
{
    /**
     * Exibe os ufs
     * @return void
     */
    public function indexAction()
    {
		$em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$dados = $em->getRepository('\Admin\Entity\Uf')->findAll();
		
		return new ViewModel(
			array('ufs' => $dados)
		);
    }

	public function saveAction()
	{
		$form = new UfForm();
		$request = $this->getRequest();
		$em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

		if ($request->isPost()) {
			$values = $request->getPost();
			$uf = new Uf();
			$form->setInputFilter($uf->getInputFilter());
			$form->setData($values);

			if($form->isValid()) {
				$values = $form->getData();

				if ((int)$values['id'] > 0)
					$uf = $em->find('\Admin\Entity\Uf', $values['id']);					


				$uf->setDescUf($values['desc_uf']);				
				$em->persist($uf);

				try{
					$em->flush();
				}catch(\Exception $e) {
					echo $e; exit;
				}

				return $this->redirect()->toUrl('/admin/ufs/index');
			}
		}

		$id = $this->params()->fromRoute('id', 0);

		if ($id > 0) {
			$uf = $em->find('\Admin\Entity\Uf', $id);
			$form->bind($uf);
		}

		return new ViewModel(
			array('form' => $form)
		);
	}

















    /**
     * Cria ou edita um uf
     * @return void
     */
/*    public function saveAction()
    {
        $form = new UfForm();
        $request = $this->getRequest(); //Pega os dados da requisição
        $em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager'); //EntityManager

        if ($request->isPost()) {
            $values = $request->getPost();
			$uf = new Uf();
			$form->setInputFilter($uf->getInputFilter());
			$form->setData($values);
			
			if ($form->isValid()) {
				$values = $form->getData();			

            if ( (int) $values['id'] > 0)
                $uf = $em->find('\Admin\Model\Uf', $values['id']);
               

            $uf->setDescUf($values['desc_uf']);
            $em->persist($uf);

            try {
                $em->flush();
				$this->flashMessenger()->addSuccessMessage('Uf armazenado com sucesso');
            } catch (\Exception $e) {
				$this->flashMessenger()->addErrorMessage('Erro ao armazenar uf');
            }

            return $this->redirect()->toUrl('/pj/admin/ufs');

		}
        }

        $id = $this->params()->fromRoute('id', 0);

        if ((int) $id > 0) {
            $uf = $em->find('\Admin\Model\uf', $id);			
            $form->bind($uf);
        }


        return new ViewModel(
            array('form' => $form)
        );
    }
*/
    /**
     * Exclui um uf
     * @return void
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        if ($id > 0) {
            $uf = $em->find('\Admin\Entity\Uf', $id);
            $em->remove($uf);

            try {
                $em->flush();
            } catch (\Exception $e) {
                echo $e; exit;
            }
        }

        return $this->redirect()->toUrl('/admin/ufs');
    }
}
