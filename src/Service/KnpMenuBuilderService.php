<?php

namespace App\Service;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

readonly class KnpMenuBuilderService
{

    public function __construct(private FactoryInterface $factory, private AuthorizationCheckerInterface $authChecker)
    {
    }

    public function createMainMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $menu->addChild('Accueil', ['route' => 'app_home']);
        $menu->addChild('Evenements', ['route' => 'app_event_index']);
        return $this->setAttributes($menu);
    }

//    private function addItems($menu)
//    {
//        $menu->addChild('Home', ['route' => 'homepage']);
//        $menu->addChild('Admin', ['route' => '']);
//        $menu['Admin']->addChild('Home Admin', ['route' => 'espace-reserve']);
//        $menu['Admin']->addChild('Home Sonata', ['route' => 'sonata_admin_dashboard']);
//        $menu['Admin']->addChild('Sport', ['route' => 'app_sport_index']);
//        $menu['Admin']->addChild('Delegation', ['route' => 'app_delegation_index']);
//        $menu['Admin']->addChild('Sportif', ['route' => 'app_sportif_index']);
//        $menu['Admin']->addChild('Ville', ['route' => 'app_ville_index']);
//        $menu['Admin']->addChild('Evenements', ['route' => 'app_evenement_index']);
//        $menu['Admin']->addChild('Actualités', ['route' => 'app_actualite_index']);
//        $menu->addChild('User', ['route' => '']);
//        $menu['User']->addChild('Liste des sport', ['route' => 'app_liste_sports']);
//        $menu['User']->addChild('Liste des sportifs', ['route' => 'app_liste_sportifs']);
//        $menu['User']->addChild('Liste des événements', ['route' => 'app_liste_evenements']);
//        $menu['User']->addChild('Liste des actualités', ['route' => 'app_liste_actualites']);
//
//        return $menu;
//    }

    private function setAttributes($menu)
    {
        foreach ($menu as $item) {
            $item->setLinkAttribute('class', 'nav-link text-decoration-none text-white');
            if ($item->getAttribute('dropdown')) {
                $item->setChildrenAttribute('class', 'dropdown-menu');
                $item->setAttribute('class', 'nav-item dropdown');
                $item->setLinkAttribute('class', 'nav-link dropdown-toggle');
                $item->setLinkAttribute('data-toggle', 'dropdown');
            } else {
                $item->setAttribute('class', 'nav-item');
            }
        }

        $menu->setChildrenAttribute('class', 'nav nav-pills');
        return $menu;
    }

//    private function addItemsUser($menu)
//    {
//        $menu->addChild('Home', ['route' => 'homepage']);
//        $menu->addChild('Liste des sport', ['route' => 'app_liste_sports']);
//        $menu->addChild('Liste des sportifs', ['route' => 'app_liste_sportifs']);
//        $menu->addChild('Liste des événements', ['route' => 'app_liste_evenements']);
//        $menu->addChild('Liste des actualités', ['route' => 'app_liste_actualites']);
//        return $menu;
//    }
}
