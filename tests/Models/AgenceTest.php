<?php

declare(strict_types=1);

namespace Tests\Models;

use App\Models\Agence;
use Tests\DatabaseTestCase;

/**
 * Tests unitaires du modèle Agence, couvrant les opérations d'écriture
 * (création, modification, suppression) exigées par le cahier des charges.
 */
final class AgenceTest extends DatabaseTestCase
{
    private Agence $agenceModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agenceModel = new Agence($this->pdo);
    }

    public function testCreateInsereUneNouvelleAgence(): void
    {
        $id = $this->agenceModel->create('Paris');

        $this->assertGreaterThan(0, $id);

        $agence = $this->agenceModel->find($id);
        $this->assertNotNull($agence);
        $this->assertSame('Paris', $agence['nom_ville']);
    }

    public function testUpdateModifieLeNomDeLaVille(): void
    {
        $id = $this->agenceModel->create('Lyon');

        $resultat = $this->agenceModel->update($id, 'Lyon-Confluence');

        $this->assertTrue($resultat);
        $agence = $this->agenceModel->find($id);
        $this->assertSame('Lyon-Confluence', $agence['nom_ville']);
    }

    public function testDeleteSupprimeLAgence(): void
    {
        $id = $this->agenceModel->create('Marseille');

        $resultat = $this->agenceModel->delete($id);

        $this->assertTrue($resultat);
        $this->assertNull($this->agenceModel->find($id));
    }

    public function testFindAllRetourneLesAgencesTrieesParNom(): void
    {
        $this->agenceModel->create('Toulouse');
        $this->agenceModel->create('Bordeaux');

        $agences = $this->agenceModel->findAll();

        $this->assertCount(2, $agences);
        $this->assertSame('Bordeaux', $agences[0]['nom_ville']);
        $this->assertSame('Toulouse', $agences[1]['nom_ville']);
    }

    public function testIsUsedByTrajetRetourneFauxSansTrajetAssocie(): void
    {
        $id = $this->agenceModel->create('Nantes');

        $this->assertFalse($this->agenceModel->isUsedByTrajet($id));
    }

    public function testIsUsedByTrajetRetourneVraiAvecTrajetAssocie(): void
    {
        $idDepart = $this->agenceModel->create('Lille');
        $idArrivee = $this->agenceModel->create('Rennes');

        $this->pdo->exec(
            "INSERT INTO utilisateur (nom, prenom, email, telephone, mot_de_passe, role)
             VALUES ('Dupont', 'Jean', 'jean.dupont@test.fr', '0600000000', 'hash', 'employe')"
        );
        $idUtilisateur = (int) $this->pdo->lastInsertId();

        $this->pdo->prepare(
            'INSERT INTO trajet (gdh_depart, gdh_arrivee, nb_places_total, nb_places_disponibles,
                                  id_agence_depart, id_agence_arrivee, id_utilisateur)
             VALUES (:depart, :arrivee, 4, 4, :id_depart, :id_arrivee, :id_utilisateur)'
        )->execute([
            ':depart'        => '2027-01-01 08:00:00',
            ':arrivee'       => '2027-01-01 10:00:00',
            ':id_depart'     => $idDepart,
            ':id_arrivee'    => $idArrivee,
            ':id_utilisateur' => $idUtilisateur,
        ]);

        $this->assertTrue($this->agenceModel->isUsedByTrajet($idDepart));
    }
}
