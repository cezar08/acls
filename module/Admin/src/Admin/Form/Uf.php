<?php

namespace Admin\Form;

use \Zend\Form\Form as Form;
use \Zend\Form\Element;

class Uf extends Form
{

    public function __construct()
    {
        parent::__construct('uf');
        $this->setAttribute('action', '');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'id',
            'type' => 'hidden',

        ));

        $this->add(array(
            'name' => 'desc_uf',
            'type' => 'text',
            'options' => array(
                'label' => 'Descrição:'
            ),
            'attributes' => array(
                'placeholder' => 'Informe o valor',
                'id' => 'desc_ufs'
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'value' => 'Salvar'
            )
        ));

    }

}
