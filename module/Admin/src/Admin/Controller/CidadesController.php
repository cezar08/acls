<?php

namespace Admin\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use \Admin\Entity\Cidade as Cidade;
use \Admin\Form\Cidade as CidadeForm;

/**
 * Controlador que gerencia os cidades
 *
 * @category Admin
 * @package Controller
 * @author  Cezar Junior de Souza <cezar08@unochapeco.edu.br>
 */
class CidadesController extends AbstractActionController
{
    /**
     * Exibe os cidades
     * @return void
     */
    public function indexAction()
    {
        $em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $dados = $em->getRepository('\Admin\Entity\Cidade')->findAll();
        
        return new ViewModel(
            array('cidades' => $dados)
        );
    }

    public function saveAction()
    {
        $form = new CidadeForm();
        $request = $this->getRequest();
        $em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        if ($request->isPost()) {
            $values = $request->getPost();
            $cidade = new Cidade();
            $form->setInputFilter($cidade->getInputFilter());
            $form->setData($values);

            if($form->isValid()) {
                $values = $form->getData();

                if ((int)$values['id'] > 0)
                    $cidade = $em->find('\Admin\Entity\Cidade', $values['id']);                 


                $cidade->setDescCidade($values['desc_cidade']);             
                $em->persist($cidade);

                try{
                    $em->flush();
                }catch(\Exception $e) {
                    echo $e; exit;
                }

                return $this->redirect()->toUrl('/admin/cidades/index');
            }
        }

        $id = $this->params()->fromRoute('id', 0);

        if ($id > 0) {
            $cidade = $em->find('\Admin\Entity\Cidade', $id);
            $form->bind($cidade);
        }

        return new ViewModel(
            array('form' => $form)
        );
    }

















    /**
     * Cria ou edita um cidade
     * @return void
     */
/*    public function saveAction()
    {
        $form = new CidadeForm();
        $request = $this->getRequest(); //Pega os dados da requisição
        $em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager'); //EntityManager

        if ($request->isPost()) {
            $values = $request->getPost();
            $cidade = new Cidade();
            $form->setInputFilter($cidade->getInputFilter());
            $form->setData($values);
            
            if ($form->isValid()) {
                $values = $form->getData();         

            if ( (int) $values['id'] > 0)
                $cidade = $em->find('\Admin\Model\Cidade', $values['id']);
               

            $cidade->setDescCidade($values['desc_cidade']);
            $em->persist($cidade);

            try {
                $em->flush();
                $this->flashMessenger()->addSuccessMessage('Cidade armazenado com sucesso');
            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage('Erro ao armazenar cidade');
            }

            return $this->redirect()->toUrl('/pj/admin/cidades');

        }
        }

        $id = $this->params()->fromRoute('id', 0);

        if ((int) $id > 0) {
            $cidade = $em->find('\Admin\Model\cidade', $id);            
            $form->bind($cidade);
        }


        return new ViewModel(
            array('form' => $form)
        );
    }
*/
    /**
     * Exclui um cidade
     * @return void
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $em =  $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        if ($id > 0) {
            $cidade = $em->find('\Admin\Entity\Cidade', $id);
            $em->remove($cidade);

            try {
                $em->flush();
            } catch (\Exception $e) {
                echo $e; exit;
            }
        }

        return $this->redirect()->toUrl('/admin/cidades');
    }
}
