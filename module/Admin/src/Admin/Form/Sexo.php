<?php

namespace Admin\Form;

use \Zend\Form\Form as Form;
use \Zend\Form\Element;

class Sexo extends Form
{

    public function __construct()
    {
        parent::__construct('sexo');
        $this->setAttribute('action', '');
        $this->setAttribute('method', 'post');

        $this->add(
            array(
                'name' => 'id',
                'type' => 'hidden'
            )
        );

        $this->add(array(
            'name' => 'desc_sexo',
            'type' => 'text',
            'options' => array(
                'label' => 'Descrição:'
            ),
            'attributes' => array(
                'placeholder' => 'Informe o valor',
                'id' => 'desc_sexo'
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
