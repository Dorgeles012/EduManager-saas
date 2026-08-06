<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller as BaseController;

/**
 * Classe de base pour tous les contrôleurs de l'espace Parent.
 * Intègre la vérification d'affiliation parent/enfant.
 */
abstract class ParentController extends BaseController
{
    use ParentAccessTrait;
}
