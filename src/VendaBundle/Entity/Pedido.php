<?php

namespace VendaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Pedido
 *
 * @ORM\Entity(repositoryClass="VendaBundle\Repository\PedidoRepository")
 * @ORM\Table(name="pedido")
 */
class Pedido
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private $id;

    /**
     * @var Pessoa
     *
     * @ORM\ManyToOne(targetEntity="Pessoa")
     * @ORM\Column(name="idCliente", type="integer", nullable=false)
     */
    private $idCliente;

    /**
     * @return Pessoa
     */
    public function getIdCliente()
    {
        return $this->idCliente;
    }

    /**
     * @param Pessoa $idCliente
     */
    public function setIdCliente($idCliente)
    {
        $this->idCliente = $idCliente;
    }

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="emissao", type="date")
     */
    private $emissao;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $total;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set emissao
     *
     * @param \DateTime $emissao
     *
     * @return Pedido
     */
    public function setEmissao($emissao)
    {
        $this->emissao = $emissao;

        return $this;
    }

    /**
     * Get emissao
     *
     * @return \DateTime
     */
    public function getEmissao()
    {
        return $this->emissao;
    }

    /**
     * Set total
     *
     * @param string $total
     *
     * @return Pedido
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return string
     */
    public function getTotal()
    {
        return $this->total;
    }

}

