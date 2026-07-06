<?php

declare(strict_types=1);

namespace Tests\Models;

use App\Models\Agence;
use App\Models\Trajet;
use App\Models\Utilisateur;
use Tests\DatabaseTestCase;

/**
 * Tests unitaires du modèle Trajet, couvrant les opérations d'écriture
 * (création, modification, suppression) exigées par le cahier des charges.
 */
final class TrajetTest extends DatabaseTestCase
{
    private Trajet $trajetModel;
    private int $idAgenceDepart;
    private int $idAgenceArrivee;
    private int $idUtilisateur;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trajetModel = new Trajet($this->pdo);
        $agenceModel = new Agence($this->pdo);

        $this->idAgenceDepart = $agenceModel->create('Paris');
        $this->idAgenceArrivee = $agenceModel->create('Lyon');

        $this->pdo->exec(
            "INSERT INTO utilisateur (nom, prenom, email, telephone, mot_de_passe, role)
             VALUES ('Martin', 'Julien', 'julien.martin@klaxon.fr', '0611223344', 'hash', 'employe')"
        );
        $this->idUtilisateur = (int) $this->pdo->lastInsertId();
    }

    /**
     * @return array<string, mixed>
     */
    private function jeuDeDonneesValide(): array
    {
        return [
            'gdh_depart'            => '2027-02-01 08:00:00',
            'gdh_arrivee'           => '2027-02-01 10:30:00',
            'nb_places_total'       => 4,
            'nb_places_disponibles' => 3,
            'id_agence_depart'      => $this->idAgenceDepart,
            'id_agence_arrivee'     => $this->idAgenceArrivee,
            'id_utilisateur'        => $this->idUtilisateur,
        ];
    }

    public function testCreateInsereUnNouveauTrajet(): void
    {
        $id = $this->trajetModel->create($this->jeuDeDonneesValide());

        $this->assertGreaterThan(0, $id);

        $trajet = $this->trajetModel->findWithDetails($id);
        $this->assertNotNull($trajet);
        $this->assertSame(3, (int) $trajet['nb_places_disponibles']);
    }

    public function testUpdateModifieLesDonneesDuTrajet(): void
    {
        $id = $this->trajetModel->create($this->jeuDeDonneesValide());

        $donneesModifiees = array_merge($this->jeuDeDonneesValide(), ['nb_places_disponibles' => 1]);
        $resultat = $this->trajetModel->update($id, $donneesModifiees);

        $this->assertTrue($resultat);
        $trajet = $this->trajetModel->findWithDetails($id);
        $this->assertSame(1, (int) $trajet['nb_places_disponibles']);
    }

    public function testDeleteSupprimeLeTrajet(): void
    {
        $id = $this->trajetModel->create($this->jeuDeDonneesValide());

        $resultat = $this->trajetModel->delete($id);

        $this->assertTrue($resultat);
        $this->assertNull($this->trajetModel->findWithDetails($id));
    }

    public function testIsAuthorRetourneVraiPourLAuteurReel(): void
    {
        $id = $this->trajetModel->create($this->jeuDeDonneesValide());

        $this->assertTrue($this->trajetModel->isAuthor($id, $this->idUtilisateur));
    }

    public function testIsAuthorRetourneFauxPourUnAutreUtilisateur(): void
    {
        $id = $this->trajetModel->create($this->jeuDeDonneesValide());

        $this->assertFalse($this->trajetModel->isAuthor($id, $this->idUtilisateur + 999));
    }

    public function testFindDisponiblesExclutLesTrajetsSansPlace(): void
    {
        $donneesComplet = array_merge($this->jeuDeDonneesValide(), ['nb_places_disponibles' => 0]);
        $this->trajetModel->create($donneesComplet);
        $idDisponible = $this->trajetModel->create($this->jeuDeDonneesValide());

        $resultats = $this->trajetModel->findDisponibles();

        $this->assertCount(1, $resultats);
        $this->assertSame($idDisponible, (int) $resultats[0]['id_trajet']);
    }

    public function testFindDisponiblesExclutLesTrajetsPasses(): void
    {
        $donneesPassees = array_merge($this->jeuDeDonneesValide(), [
            'gdh_depart'  => '2020-01-01 08:00:00',
            'gdh_arrivee' => '2020-01-01 10:00:00',
        ]);
        $this->trajetModel->create($donneesPassees);

        $resultats = $this->trajetModel->findDisponibles();

        $this->assertCount(0, $resultats);
    }
}
