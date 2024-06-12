<?php

namespace App\Service;

use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

readonly class KnpMenuBuilderService
{

    public function __construct(private FactoryInterface $factory, private AuthorizationCheckerInterface $authChecker)
    {
    }

    public function createMainMenu()
    {
        $menu = $this->factory->createItem('root');
        $menu->addChild('Accueil', ['route' => 'app_home']);

        $evenements = $menu->addChild('Évènements', ['uri' => '#']);
        $evenements->setAttribute('dropdown', true);
        $evenements->addChild('Liste des événements', ['route' => 'app_event_index']);

        if (!$this->authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            $menu->addChild('Se connecter', ['route' => 'app_login']);
        } else {
            $evenements->addChild('Créer un événement', ['route' => 'app_event_new']);
            $menu->addChild('Profil', ['route' => 'app_profile']);
            $menu->addChild('Se déconnecter', ['route' => 'app_logout']);
        }

        return $this->setAttributes($menu);
    }

    private function setAttributes($menu)
    {
        foreach ($menu as $item) {
            $item->setLinkAttribute('class', 'nav-link text-decoration-none text-white');
            if ($item->getAttribute('dropdown')) {
                $item->setChildrenAttribute('class', 'dropdown-menu bg-dark text-white');
                $item->setAttribute('class', 'nav-item dropdown bg-dark text-white');
                $item->setLinkAttribute('class', 'nav-link dropdown-toggle text-decoration-none text-white');
                $item->setLinkAttribute('data-bs-toggle', 'dropdown');
                $item->setLinkAttribute('role', 'button');
                $item->setLinkAttribute('aria-expanded', 'false');
                foreach ($item->getChildren() as $child) {
                    $child->setLinkAttribute('class', 'dropdown-item text-decoration-none text-white');
                    $child->setAttribute('class', 'dropdown-item');
                }
            } else {
                $item->setAttribute('class', 'nav-item');
            }
            if (in_array($item->getName(), ['Se connecter', 'Profil', 'Se déconnecter'])) {
                $item->setAttribute('class', $item->getAttribute('class') . 'd-flex justify-content-end');
            }
        }

        $menu->setChildrenAttribute('class', 'navbar-nav mb-2 mb-lg-0');
        return $menu;
    }

}
