<?php

namespace VendaBundle\Controller;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\BrowserKit\Response;
use VendaBundle\Entity\ItemPedido;
use VendaBundle\Entity\Pedido;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use VendaBundle\Form\ItemPedidoType;

/**
 * Pedido controller.
 *
 * @Route("pedido")
 */
class PedidoController extends Controller
{
    /**
     *
     * @Route("/{id}/deleteItemPedido/{idItemPedido}", name="pedido_deleteItemPedido")
     * @Method({"GET", "POST"})
     */
    public function deleteItemPedidoAction(Request $request, $id, $idItemPedido)
    {
        $itemPedidoRepository = $this->getDoctrine()->getRepository('VendaBundle:ItemPedido');
        $itemPedidoRepository->deleteItemPedido($idItemPedido);
        $this->recalculaTotalPedido($id);

        return $this->redirectToRoute('pedido_additem', array('id' => $id));
    }

    /**
     *
     * @Route("/{id}/additem", name="pedido_additem")
     * @Method({"GET", "POST"})
     */
    public function addItemAction(Request $request, $id)
    {
        $newProduto = new ItemPedido();
        $newProdutoForm = $this->createForm('VendaBundle\Form\ItemPedidoType', $newProduto);
        $newProdutoForm->handleRequest($request);

        $itemPedidoRepository = $this->getDoctrine()->getRepository('VendaBundle:ItemPedido');
        $produtos = $itemPedidoRepository->findByIdPedido($id);

        $totalPedido = $itemPedidoRepository->getTotalPedido($id);

        if ($newProdutoForm->isSubmitted() && $newProdutoForm->isValid()) {

            if ($newProdutoForm["quantidade"]->getData() > 0 && $newProdutoForm["percentualDesconto"]->getData() >= 0) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($newProduto);
                $em->flush();

                $this->recalculaTotalPedido($id);

                return $this->redirectToRoute('pedido_additem', array('id' => $id));
            }
            else
            {
                $this->get('session')->getFlashBag()->add('error', 'Quantidade deve ser maior que zero e % Desconto deve ser maior ou igual a zero.');

                return $this->render('pedido/addItem.html.twig', array(
                    'produtos' => $produtos,
                    'newProdutoForm' => $newProdutoForm->createView(),
                    'idPedido' => $id,
                    'totalPedido' => $totalPedido['total']
                ));
            }
        }

        return $this->render('pedido/addItem.html.twig', array(
            'produtos' => $produtos,
            'newProdutoForm' => $newProdutoForm->createView(),
            'idPedido' => $id,
            'totalPedido' => $totalPedido['total']
        ));
    }

    private function recalculaTotalPedido($id)
    {
        $itemPedidoRepository = $this->getDoctrine()->getRepository('VendaBundle:ItemPedido');
        $totalPedido = $itemPedidoRepository->getTotalPedido($id);

        $PedidoRepository = $this->getDoctrine()->getRepository('VendaBundle:Pedido');
        $PedidoRepository->setValorTotal($id, $totalPedido['total']);
    }

    /**
     * Lists all pedido entities.
     *
     * @Route("/", name="pedido_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $formPesquisar = $this->createFormBuilder()
            ->setMethod('GET')
            ->add('pesquisar', TextType::class)
            ->add('pesquisarpor', ChoiceType::class, array(
                    'choices' => array(
                        'Cliente' => 'cliente',
                        'Emissão' => 'emissao'
                    )
                )
            )
            ->getForm();

        $formPesquisar->handleRequest($request);

        if($formPesquisar->isSubmitted() && $formPesquisar->isValid())
        {
            $query = $request->get('form')['pesquisar'];
            if ($request->get('form')['pesquisarpor'] == "emissao")
            {
                if (date_create_from_format('d/m/Y', $query) !== FALSE) {
                    $format = 'd/m/Y';
                    $query = date_create_from_format($format, $request->get('form')['pesquisar']);
                }
                else
                {
                    $this->get('session')->getFlashBag()->add('error', 'A data deve estar no formato dd/mm/aaaa. Ex: 01/01/2017');

                    $pedidos = $em->getRepository('VendaBundle:Pedido')->findAll();

                    return $this->render('pedido/index.html.twig', array(
                        'pedidos' => $pedidos,
                        'formPesquisar' => $formPesquisar->createView()
                    ));
                }

                $pedidos = $em->getRepository('VendaBundle:Pedido')->findByEmissao(date_format($query,"Y-m-d"));
            }
            else
            {
                //echo $request->get('form')['pesquisar'];
                //die('teste');

                $pedidos = $em->getRepository('VendaBundle:Pedido')->findByCliente($query);
            }
        }
        else
        {
            $pedidoRepository = $this->getDoctrine()->getRepository('VendaBundle:Pedido');
            $pedidos = $pedidoRepository->findPedidos();
        }

        return $this->render('pedido/index.html.twig', array(
            'pedidos' => $pedidos,
            'formPesquisar' => $formPesquisar->createView()
        ));
    }

    /**
     * Creates a new pedido entity.
     *
     * @Route("/new", name="pedido_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $pedido = new Pedido();

        $form = $this->createForm('VendaBundle\Form\PedidoType', $pedido);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($pedido);
            $em->flush();

            return $this->redirectToRoute('pedido_show', array('id' => $pedido->getId()));
        }

        return $this->render('pedido/new.html.twig', array(
            'pedido' => $pedido,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a pedido entity.
     *
     * @Route("/{id}", name="pedido_show")
     * @Method("GET")
     */
    public function showAction(Pedido $pedido)
    {
        $deleteForm = $this->createDeleteForm($pedido);
        $pedidoRepository = $this->getDoctrine()->getRepository('VendaBundle:Pedido');
        $pedido = $pedidoRepository->getPedido($pedido->getId());

        $itemPedidoRepository = $this->getDoctrine()->getRepository('VendaBundle:ItemPedido');
        $produtos = $itemPedidoRepository->findByIdPedido($pedido['id']);

        return $this->render('pedido/show.html.twig', array(
            'pedido' => $pedido,
            'delete_form' => $deleteForm->createView(),
            'produtos' => $produtos
        ));
    }

    /**
     * Deletes a pedido entity.
     *
     * @Route("/{id}", name="pedido_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Pedido $pedido)
    {
        $form = $this->createDeleteForm($pedido);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($pedido);
            $em->flush();
        }

        return $this->redirectToRoute('pedido_index');
    }

    /**
     * Creates a form to delete a pedido entity.
     *
     * @param Pedido $pedido The pedido entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Pedido $pedido)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pedido_delete', array('id' => $pedido->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

}
