<?php

namespace VendaBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemPedidoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pedidoId', NumberType::class)
            ->add('produtoId', NumberType::class, [
                'label' => 'ID Produto',
            ])
            ->add('quantidade', NumberType::class, [
                'label' => 'Qtd',
                'required' => true,
            ])
            ->add('precoUnitario', NumberType::class, [
                'label' => 'Vlr.Un.',
                'attr' => ['readonly' => true]
            ])
            ->add('percentualDesconto', NumberType::class, [
                'label' => '% Desc.',
                'data' => 0.00,
            ])
            ->add('total', NumberType::class,
                array(
                    'disabled' => false,
                    'attr' => ['readonly' => true]
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VendaBundle\Entity\ItemPedido',
            'allow_extra_fields' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vendabundle_itempedido';
    }
}