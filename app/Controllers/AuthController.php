<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\Utilisateur;

/**
 * Gère l'authentification des utilisateurs : affichage du formulaire de
 * connexion, vérification des identifiants, et déconnexion.
 */
final class AuthController extends Controller
{
    private Utilisateur $utilisateurModel;

    public function __construct()
    {
        $this->utilisateurModel = new Utilisateur();
    }

    /**
     * Affiche le formulaire de connexion.
     *
     * @return void
     */
    public function showLogin(): void
    {
        if (Session::isAuthenticated()) {
            $this->redirect('/');
        }

        $this->render('auth/login', [
            'csrfField' => Csrf::field(),
            'erreur'    => null,
        ]);
    }

    /**
     * Traite la soumission du formulaire de connexion.
     *
     * @return void
     */
    public function login(): void
    {
        $this->requireValidCsrf();

        $email = trim((string) ($_POST['email'] ?? ''));
        $motDePasse = (string) ($_POST['mot_de_passe'] ?? '');

        // Validation défensive des entrées : rien ne doit être vide,
        // et l'email doit respecter un format valide avant toute requête SQL.
        if ($email === '' || $motDePasse === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->render('auth/login', [
                'csrfField' => Csrf::field(),
                'erreur'    => 'Veuillez saisir une adresse email valide et votre mot de passe.',
            ]);

            return;
        }

        $utilisateur = $this->utilisateurModel->findByEmailWithPassword($email);

        if ($utilisateur === null || !password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
            $this->render('auth/login', [
                'csrfField' => Csrf::field(),
                'erreur'    => 'Adresse email ou mot de passe incorrect.',
            ]);

            return;
        }

        // Le hash du mot de passe ne doit jamais être conservé en session.
        unset($utilisateur['mot_de_passe']);

        Session::login($utilisateur);

        $destination = $utilisateur['role'] === 'admin' ? '/admin' : '/';
        $this->redirectWithFlash('success', 'Connexion réussie. Bienvenue ' . $utilisateur['prenom'] . ' !', $destination);
    }

    /**
     * Déconnecte l'utilisateur courant.
     *
     * @return void
     */
    public function logout(): void
    {
        Session::logout();
        $this->redirect('/');
    }
}
