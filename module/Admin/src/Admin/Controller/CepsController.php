<?php

namespace Admin\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use \Admin\Entity\Cep as Cep;
use \Admin\Form\Cep as CepForm;

/**
 * Controlador que gerencia os ceps
 *
 * @category Admin
 * @package Controller
 * @author  Cezar Junior de Souza <cezar08@unochapeco.edu.br>
 */
class CepsController extends AbstractActionController
{
    /**
     * Exibe os ceps
     * @return void
     */
    public function indexAction()
    {
		$em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$dados = $em->getRepository('\Admin\Entity\Cep')->findAll();
		
		return new ViewModel(
			array('ceps' => $dados)
		);
    }

	public function saveAction()
	{
		$form = new CepForm();
		$request = $this->getRequest();
		$em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

		if ($request->isPost()) {
			$values = $request->getPost();
			$cep = new Cep();
			$form->setInputFilter($cep->getInputFilter());
			$form->setData($values);

			if($form->isValid()) {
				$values = $form->getData();

				if ((int)$values['id'] > 0)
					$cep = $em->find('\Admin\Entity\Cep', $values['id']);					


				$cep->setDescCep($values['desc_cep']);				
				$em->persist($cep);

				try{
					$em->flush();
				}catch(\Exception $e) {
					echo $e; exit;
				}

				return $this->redirect()->toUrl('/admin/ceps/index');
			}
		}

		$id = $this->params()->fromRoute('id', 0);

		if ($id > 0) {
			$cep = $em->find('\Admin\Entity\Cep', $id);
			$form->bind($cep);
		}

		return new ViewModel(
			array('form' => $form)
		);
	}

















    /**
     * Cria ou edita um cep
     * @return void
     */
/*    public function saveAction()
    {
        $form = new CepForm();
        $request = $this->getRequest(); //Pega os dados da requisição
        $em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager'); //EntityManager

        if ($request->isPost()) {
            $values = $request->getPost();
			$cep = new Cep();
			$form->setInputFilter($cep->getInputFilter());
			$form->setData($values);
			
			if ($form->isValid()) {
				$values = $form->getData();			

            if ( (int) $values['id'] > 0)
                $cep = $em->find('\Admin\Model\Cep', $values['id']);
               

            $cep->setDescCep($values['desc_cep']);
            $em->persist($cep);

            try {
                $em->flush();
				$this->flashMessenger()->addSuccessMessage('Cep armazenado com sucesso');
            } catch (\Exception $e) {
				$this->flashMessenger()->addErrorMessage('Erro ao armazenar cep');
            }

            return $this->redirect()->toUrl('/pj/admin/ceps');

		}
        }

        $id = $this->params()->fromRoute('id', 0);

        if ((int) $id > 0) {
            $cep = $em->find('\Admin\Model\cep', $id);			
            $form->bind($cep);
        }


        return new ViewModel(
            array('form' => $form)
        );
    }
*/
    /**
     * Exclui um cep
     * @return void
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        if ($id > 0) {
            $cep = $em->find('\Admin\Entity\Cep', $id);
            $em->remove($cep);

            try {
                $em->flush();
            } catch (\Exception $e) {
                echo $e; exit;
            }
        }

        return $this->redirect()->toUrl('/admin/ceps');
    }
}
