<?php

namespace VendaBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use VendaBundle\Entity\Pedido;
use VendaBundle\Entity\Pessoa;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;

/**
 * Pessoa controller.
 *
 * @Route("pessoa")
 */
class PessoaController extends Controller
{
    /**
     * Finds and displays a pessoa entity.
     *
     * @Route("/autocomplete", name="pessoa_autocomplete")
     * @Method({"GET"})
     */
    public function autocompleteAction(Request $request)
    {
        $nomes = array();
        $term = trim(strip_tags($request->get('term')));

        $pessoaRepository = $this->getDoctrine()->getRepository('VendaBundle:Pessoa');
        $entities = $pessoaRepository->createQueryBuilder('p')
            ->select('p.nome as label, p.nome as nome, p.id as id')
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
     * Lists all pessoa entities.
     *
     * @Route("/", name="pessoa_index")
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
                        'Nome' => 'nome',
                        'Data de Nascimento' => 'dataNascimento'
                    )
                )
            )
            ->getForm();

        $formPesquisar->handleRequest($request);

        if($formPesquisar->isSubmitted() && $formPesquisar->isValid())
        {
            $query = $request->get('form')['pesquisar'];
            if ($request->get('form')['pesquisarpor'] == "dataNascimento")
            {
                if (date_create_from_format('d/m/Y', $query) !== FALSE) {
                    $format = 'd/m/Y';
                    $query = date_create_from_format($format, $request->get('form')['pesquisar']);
                }
                else
                {
                    $this->get('session')->getFlashBag()->add('error', 'A data deve estar no formato dd/mm/aaaa. Ex: 01/01/2017');

                    $pessoas = $em->getRepository('VendaBundle:Pessoa')->findAll();

                    return $this->render('pessoa/index.html.twig', array(
                        'pessoas' => $pessoas,
                        'formPesquisar' => $formPesquisar->createView()
                    ));
                }
            }

            $pessoas = $em->getRepository('VendaBundle:Pessoa')->findby(
                array(
                    $request->get('form')['pesquisarpor'] => $query
                )
            );

        }
        else
        {
            $pessoas = $em->getRepository('VendaBundle:Pessoa')->findAll();
        }

        return $this->render('pessoa/index.html.twig',
            [
                'pessoas' => $pessoas,
                'formPesquisar' => $formPesquisar->createView()
            ]
        );
    }

    /**
     * Creates a new pessoa entity.
     *
     * @Route("/new", name="pessoa_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $pessoa = new Pessoa();
        $form = $this->createForm('VendaBundle\Form\PessoaType', $pessoa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($pessoa);
                $em->flush();

                return $this->redirectToRoute('pessoa_show', array('id' => $pessoa->getId()));
            }
            catch (\Exception $e)
            {
                $this->get('session')->getFlashBag()->add('error', 'Erro ao cadastrar pessoa, verifique se o cadastro já existe.');

                return $this->render('produto/edit.html.twig', array(
                    'pessoa' => $pessoa,
                    'form' => $form->createView(),
                ));
            }

        }

        return $this->render('pessoa/new.html.twig', array(
            'pessoa' => $pessoa,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a pessoa entity.
     *
     * @Route("/{id}", name="pessoa_show")
     * @Method("GET")
     */
    public function showAction(Pessoa $pessoa)
    {
        $deleteForm = $this->createDeleteForm($pessoa);

        return $this->render('pessoa/show.html.twig', array(
            'pessoa' => $pessoa,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing pessoa entity.
     *
     * @Route("/{id}/edit", name="pessoa_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Pessoa $pessoa)
    {
        $deleteForm = $this->createDeleteForm($pessoa);
        $editForm = $this->createForm('VendaBundle\Form\PessoaType', $pessoa);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pessoa_edit', array('id' => $pessoa->getId()));
        }

        return $this->render('pessoa/edit.html.twig', array(
            'pessoa' => $pessoa,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a pessoa entity.
     *
     * @Route("/{id}", name="pessoa_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Pessoa $pessoa)
    {
        $form = $this->createDeleteForm($pessoa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($pessoa);
            $em->flush();
        }

        return $this->redirectToRoute('pessoa_index');
    }

    /**
     * Creates a form to delete a pessoa entity.
     *
     * @param Pessoa $pessoa The pessoa entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Pessoa $pessoa)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pessoa_delete', array('id' => $pessoa->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

}
