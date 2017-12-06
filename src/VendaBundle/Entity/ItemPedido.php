<?php

namespace VendaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ItemPedido
 *
 * @ORM\Table(name="itempedido")
 * @ORM\Entity(repositoryClass="VendaBundle\Repository\ItemPedidoRepository")
 */
class ItemPedido
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="VendaBundle\Entity\Pedido", inversedBy="produtos")
     * @ORM\Column(name="pedido_id", type="integer")
     * */
    private $pedidoId;

    /**
     * @ORM\ManyToOne(targetEntity="VendaBundle\Entity\Produto", inversedBy="produto")
     * @ORM\Column(name="produto_id", type="integer")
     * */
    private $produtoId;

    /**
     * @var string
     *
     * @ORM\Column(name="quantidade", type="decimal", precision=10, scale=2)
     */
    private $quantidade;

    /**
     * @var string
     *
     * @ORM\Column(name="precoUnitario", type="decimal", precision=10, scale=2)
     */
    private $precoUnitario;

    /**
     * @var string
     *
     * @ORM\Column(name="percentualDesconto", type="decimal", precision=10, scale=2)
     */
    private $percentualDesconto;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=10, scale=2)
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
     * Set produtoId
     *
     * @param integer $produtoId
     *
     * @return ItemPedido
     */
    public function setProdutoId($produtoId)
    {
        $this->produtoId = $produtoId;

        return $this;
    }

    /**
     * Get produtoId
     *
     * @return int
     */
    public function getProdutoId()
    {
        return $this->produtoId;
    }

    /**
     * Set pedidoId
     *
     * @param integer $pedidoId
     *
     * @return ItemPedido
     */
    public function setPedidoId($pedidoId)
    {
        $this->pedidoId = $pedidoId;

        return $this;
    }

    /**
     * Get pedidoId
     *
     * @return int
     */
    public function getPedidoId()
    {
        return $this->pedidoId;
    }

    /**
     * Set quantidade
     *
     * @param string $quantidade
     *
     * @return ItemPedido
     */
    public function setQuantidade($quantidade)
    {
        $this->quantidade = $quantidade;

        return $this;
    }

    /**
     * Get quantidade
     *
     * @return string
     */
    public function getQuantidade()
    {
        return $this->quantidade;
    }

    /**
     * Set precoUnitario
     *
     * @param string $precoUnitario
     *
     * @return ItemPedido
     */
    public function setPrecoUnitario($precoUnitario)
    {
        $this->precoUnitario = $precoUnitario;

        return $this;
    }

    /**
     * Get precoUnitario
     *
     * @return string
     */
    public function getPrecoUnitario()
    {
        return $this->precoUnitario;
    }

    /**
     * Set percentualDesconto
     *
     * @param string $percentualDesconto
     *
     * @return ItemPedido
     */
    public function setPercentualDesconto($percentualDesconto)
    {
        $this->percentualDesconto = $percentualDesconto;

        return $this;
    }

    /**
     * Get percentualDesconto
     *
     * @return string
     */
    public function getPercentualDesconto()
    {
        return $this->percentualDesconto;
    }

    /**
     * Set total
     *
     * @param string $total
     *
     * @return ItemPedido
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

