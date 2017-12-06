<?php



namespace VendaBundle\Menu;


use Knp\Menu\MenuFactory;

class Builder
{
    public function mainMenu(MenuFactory $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class' , 'nav navbar-nav');
        $menu->addChild('Início', ['route' => 'homepage']);
        $menu->addChild('Produtos', ['route' => 'produto_index']);
        $menu->addChild('Pessoas', ['route' => 'pessoa_index']);
        $menu->addChild('Pedidos', ['route' => 'pedido_index']);
        return $menu;
    }
}