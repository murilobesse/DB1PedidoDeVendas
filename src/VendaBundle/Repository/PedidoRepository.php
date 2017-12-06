<?php

namespace VendaBundle\Repository;

class PedidoRepository extends \Doctrine\ORM\EntityRepository
{
    public function findPedidos()
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p.id, p.emissao, p.total, c.nome');
        $qb->innerJoin('VendaBundle:Pessoa', 'c', 'WITH', 'p.idCliente = c.id');
        return $qb->getQuery()->getResult();
    }

    public function findByCliente($nomeCliente)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p.id, p.emissao, p.total, c.nome')
            ->innerJoin('VendaBundle:Pessoa', 'c', 'WITH', 'p.idCliente = c.id')
            ->where('c.nome = :nomeCliente')
            ->setParameter('nomeCliente', $nomeCliente);

        return $qb->getQuery()->getResult();
    }

    public function findByEmissao($emissao)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p.id, p.emissao, p.total, c.nome')
            ->innerJoin('VendaBundle:Pessoa', 'c', 'WITH', 'p.idCliente = c.id')
            ->where('p.emissao = :emissao')
            ->setParameter('emissao', $emissao);

        return $qb->getQuery()->getResult();
    }

    public function getPedido($id)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p.id, p.emissao, p.total, c.nome')
            ->leftJoin('VendaBundle:Pessoa', 'c', 'WITH', 'p.idCliente = c.id')
            ->Where('p.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function setValorTotal($idPedido, $totalPedido)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->update()
            ->set('p.total', $totalPedido)
            ->where('p.id = :idPedido')
            ->setParameter('idPedido', $idPedido);
        return $qb->getQuery()->getResult();
    }
}
