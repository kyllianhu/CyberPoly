<?php
class GameController extends BaseController
{
    // Vérifie la session puis affiche le plateau de jeu
    public function index(): void
    {
        $this->requireAuth();

        $this->render('game/board', [
            'pageTitle'  => 'CyberSafe Monopoly — Plateau de jeu',
            'activePage' => 'game',
            'pseudo'     => $_SESSION['pseudo'] ?? 'Joueur',
        ], 'main');
    }
}
