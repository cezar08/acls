<?php

namespace Admin\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use \Admin\Entity\Interesse as Interesse;
use \Admin\Form\Interesse as InteresseForm;

/**
 * Controlador que gerencia os interesses
 *
 * @category Admin
 * @package Controller
 * @author  Cezar Junior de Souza <cezar08@unochapeco.edu.br>
 */
class InteressesController extends AbstractActionController
{
    /**
     * Exibe os interesses
     * @return void
     */
    public function indexAction()
    {
		$em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$dados = $em->getRepository('\Admin\Entity\Interesse')->findAll();
		
		return new ViewModel(
			array('interesses' => $dados)
		);
    }

	public function saveAction()
	{
		$form = new InteresseForm();
		$request = $this->getRequest();
		$em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

		if ($request->isPost()) {
			$values = $request->getPost();
			$interesse = new Interesse();
			$form->setInputFilter($interesse->getInputFilter());
			$form->setData($values);

			if($form->isValid()) {
				$values = $form->getData();

				if ((int)$values['id'] > 0)
					$interesse = $em->find('\Admin\Entity\Interesse', $values['id']);					


				$interesse->setDescInteresse($values['desc_interesse']);				
				$em->persist($interesse);

				try{
					$em->flush();
				}catch(\Exception $e) {
					echo $e; exit;
				}

				return $this->redirect()->toUrl('/admin/interesses/index');
			}
		}

		$id = $this->params()->fromRoute('id', 0);

		if ($id > 0) {
			$interesse = $em->find('\Admin\Entity\Interesse', $id);
			$form->bind($interesse);
		}

		return new ViewModel(
			array('form' => $form)
		);
	}

















    /**
     * Cria ou edita um interesse
     * @return void
     */
/*    public function saveAction()
    {
        $form = new InteresseForm();
        $request = $this->getRequest(); //Pega os dados da requisição
        $em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager'); //EntityManager

        if ($request->isPost()) {
            $values = $request->getPost();
			$interesse = new Interesse();
			$form->setInputFilter($interesse->getInputFilter());
			$form->setData($values);
			
			if ($form->isValid()) {
				$values = $form->getData();			

            if ( (int) $values['id'] > 0)
                $interesse = $em->find('\Admin\Model\Interesse', $values['id']);
               

            $interesse->setDescInteresse($values['desc_interesse']);
            $em->persist($interesse);

            try {
                $em->flush();
				$this->flashMessenger()->addSuccessMessage('Interesse armazenado com sucesso');
            } catch (\Exception $e) {
				$this->flashMessenger()->addErrorMessage('Erro ao armazenar interesse');
            }

            return $this->redirect()->toUrl('/pj/admin/interesses');

		}
        }

        $id = $this->params()->fromRoute('id', 0);

        if ((int) $id > 0) {
            $interesse = $em->find('\Admin\Model\Interesse', $id);			
            $form->bind($interesse);
        }


        return new ViewModel(
            array('form' => $form)
        );
    }
*/
    /**
     * Exclui um interesse
     * @return void
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        if ($id > 0) {
            $interesse = $em->find('\Admin\Entity\Interesse', $id);
            $em->remove($interesse);

            try {
                $em->flush();
            } catch (\Exception $e) {
                echo $e; exit;
            }
        }

        return $this->redirect()->toUrl('/admin/interesses');
    }
}
