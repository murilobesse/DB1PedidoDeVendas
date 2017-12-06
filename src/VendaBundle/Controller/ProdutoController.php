<?php

namespace VendaBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use VendaBundle\Entity\Produto;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Produto controller.
 *
 * @Route("produto")
 */
class ProdutoController extends Controller
{
    /**
     *
     * @Route("/autocomplete", name="produto_autocomplete")
     * @Method({"GET"})
     */
    public function autocompleteAction(Request $request)
    {
        $nomes = array();
        $term = trim(strip_tags($request->get('term')));

        $pessoaRepository = $this->getDoctrine()->getRepository('VendaBundle:Produto');
        $entities = $pessoaRepository->createQueryBuilder('p')
            ->select('p.nome as label, p.id as id, p.precoUnitario as preco')
            ->where('p.nome LIKE :nome')
            ->setParameter('nome', '%'.$term.'%')
            ->getQuery()
            ->getResult();

        $response = new JsonResponse();
        $response->setData($entities);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

    /**
     * Lists all produto entities.
     *
     * @Route("/", name="produto_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        //$produtos = $em->getRepository('VendaBundle:Produto')->findAll();
        $formPesquisar = $this->createFormBuilder()
            ->setMethod('GET')
            ->add('pesquisar', TextType::class)
            ->add('pesquisarpor', ChoiceType::class, array(
                    'choices' => array(
                            'Código' => 'codigo',
                            'Nome' => 'nome',
                            'Preço Unitário' => 'precoUnitario'
                    )
                )
            )
            ->getForm();

        $formPesquisar->handleRequest($request);

        if($formPesquisar->isSubmitted() && $formPesquisar->isValid())
        {
            $produtos = $em->getRepository('VendaBundle:Produto')->findby(
                array(
                    $request->get('form')['pesquisarpor'] => $request->get('form')['pesquisar']
                )
            );
        }
        else
        {
            $produtos = $em->getRepository('VendaBundle:Produto')->findAll();
        }

        return $this->render('produto/index.html.twig',
            [
                'produtos' => $produtos,
                'formPesquisar' => $formPesquisar->createView()
            ]
        );
    }

    /**
     * Creates a new produto entity.
     *
     * @Route("/new", name="produto_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $produto = new Produto();
        $form = $this->createForm('VendaBundle\Form\ProdutoType', $produto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($produto);
                $em->flush();

                return $this->redirectToRoute('produto_show', array('id' => $produto->getId()));
            }
            catch (\Exception $e)
            {
                $this->get('session')->getFlashBag()->add('error', 'Erro ao criar novo produto, verifique se o cadastro já existe.');

                return $this->render('produto/new.html.twig', array(
                    'form' => $form->createView(),
                ));
            }
        }
        return $this->render('produto/new.html.twig', array(
            'produto' => $produto,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a produto entity.
     *
     * @Route("/{id}", name="produto_show")
     * @Method("GET")
     */
    public function showAction(Produto $produto)
    {
        $deleteForm = $this->createDeleteForm($produto);

        return $this->render('produto/show.html.twig', array(
            'produto' => $produto,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing produto entity.
     *
     * @Route("/{id}/edit", name="produto_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Produto $produto)
    {
        $deleteForm = $this->createDeleteForm($produto);
        $editForm = $this->createForm('VendaBundle\Form\ProdutoType', $produto);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try
            {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('produto_edit', array('id' => $produto->getId()));
            }
            catch (\Exception $e)
            {
                $this->get('session')->getFlashBag()->add('error', 'Erro ao editar o produto, verifique se o cadastro já existe.');

                return $this->render('produto/edit.html.twig', array(
                    'produto' => $produto,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
            }

        }

        return $this->render('produto/edit.html.twig', array(
            'produto' => $produto,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a produto entity.
     *
     * @Route("/{id}", name="produto_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Produto $produto)
    {
        $form = $this->createDeleteForm($produto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($produto);
            $em->flush();
        }

        return $this->redirectToRoute('produto_index');
    }

    /**
     * Creates a form to delete a produto entity.
     *
     * @param Produto $produto The produto entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Produto $produto)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('produto_delete', array('id' => $produto->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
