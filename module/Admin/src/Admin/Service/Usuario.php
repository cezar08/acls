<?php

namespace Admin\Service;

use Core\Service\Service;
use Doctrine\ORM\NoResultException;

/**
 * Serviço para autenticação de um usuário simples no sistema
 *
 * @category Admin
 * @package Service
 * @author Cezar
 */
class Usuario extends Service
{

    /**
     * @var string
     */
    protected $entity = '\Admin\Entity\Usuario';

    /**
     * @param null $search
     * @return \Doctrine\ORM\AbstractQuery
     */
    public function fetchAll($search = null)
    {
        $search = mb_strtoupper($search, 'UTF-8');
        $query = $this->getEm()
            ->createQuery('SELECT Usuario FROM \Admin\Entity\Usuario Usuario
            WHERE Usuario.nome like ?1 or Usuario.email like ?1')
            ->setParameter(1, "%$search%")
            ->useResultCache(true, 3600, 'usuarios_list');

        return $query;
    }

    /**
     * @param $id
     * @return null|object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \NoResultException
     */
    public function get($id)
    {
        $obj = $this->getEm()->find($this->entity, (int) $id);

        if (!$obj)
            throw new \NoResultException('Usuário não encontrado');

        return $obj;
    }

    /**
     * @param $data
     * @return null|object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function save($data)
    {
        $usuario = new $this->entity();
        $filters = $usuario->getInputFilter();
        $filters->setData($data);

        if (!$filters->isValid())
            throw new \InvalidArgumentException('Dados inválidos');

        $data = $filters->getValues();

        if ( (int) $data['id'] > 0)
            $usuario = $this->getEm()->find($this->entity, $data['id']);

        $usuario->setNome($data['nome']);
        $usuario->setEmail($data['email']);
        $usuario->setSenha($data['senha']);
        $usuario->setSobrenome($data['sobrenome']);
        $usuario->setRole($data['role']);
        $sexo = $this->getEm()->find('\Admin\Entity\Sexo', $data['sexo']);
        $usuario->setSexo($sexo);
        $usuario->getInteresses()->clear();
        $usuario->setPhoto($data['photo']);


        foreach ($data['interesses'] as $interesse) {
            $interesse = $this->getEm()->find('\Admin\Entity\Interesse', $interesse);
            $usuario->getInteresses()->add($interesse);
        }

        $this->getEm()->persist($usuario);
        $this->getEm()->flush();

        return $usuario;
    }

    /**
     * @param $id
     * @return null|object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \NoResultException
     */
    public function delete($id)
    {
        $obj = $this->getEm()->find($this->entity, (int) $id);

        if (!$obj)
            throw new \NoResultException('Usuário não encontrado');

        $this->getEm()->remove($obj);
        $this->getEm()->flush();
    }

    public function uploadPhoto($file) {
        $target_path = getcwd() . '/public/temp/';
        $target_path = $target_path . basename($file['name']);
        $validator_img = new \Zend\Validator\File\IsImage(array('image/jpg', 'image/png', 'image/jpeg'));
        move_uploaded_file($file['tmp_name'], $target_path);

        if (!$validator_img->isValid($target_path))
            throw new InvalidMagicMimeFileException('O arquivo enviado não é uma imagem válida');

        $rand = uniqid();
        $origem = $target_path;
        $this->thumb($origem);
        $novo = getcwd() . '/public/temp/' . $rand;
        copy($origem, $novo);
        $image = file_get_contents($novo);
        $data = base64_encode($image);
        unlink($origem);
        unlink($novo);

        return $data;
    }

    public function getPhoto($id)
    {
        if ((int) $id <= 0)
            throw new \InvalidArgumentException('Parâmetros inválidos');

        $user = $this->getEm()->find($this->entity, (int) $id);

        if (!$user)
            throw new NoResultException('Usuário não existe');

        $base = null;

        if ($user->getPhoto() != null) {
            $stream = stream_get_contents($user->getPhoto());
            $base = base64_decode($stream);
        }

        return $base;
    }

    private function thumb($origem)
    {
        $size =  getimagesize($origem);
        $image_p = imagecreatetruecolor(160, 120);
        $image = imagecreatefromjpeg($origem);
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, 160, 120, $size[0], $size[1]);
        imagejpeg($image_p, $origem, 50);
    }

}

