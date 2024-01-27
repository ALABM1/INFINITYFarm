<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231205235324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Categtrans (id_cat_tra INT AUTO_INCREMENT NOT NULL, nom_cat_tra VARCHAR(150) NOT NULL, descrip_cat_tra VARCHAR(250) NOT NULL, PRIMARY KEY(id_cat_tra)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Messages (id INT AUTO_INCREMENT NOT NULL, destinataire INT DEFAULT NULL, source INT DEFAULT NULL, text VARCHAR(255) DEFAULT NULL, date DATE DEFAULT NULL, INDEX IDX_22747CC0FEA9FF92 (destinataire), INDEX IDX_22747CC05F8A7F73 (source), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Reclamations (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, telephone VARCHAR(15) DEFAULT NULL, date DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Transaction (id_tra INT AUTO_INCREMENT NOT NULL, categ_tra VARCHAR(150) NOT NULL, type_tra VARCHAR(150) NOT NULL, date_tra DATETIME NOT NULL, montant INT NOT NULL, PRIMARY KEY(id_tra)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Users (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, mail VARCHAR(255) DEFAULT NULL, role VARCHAR(255) DEFAULT NULL, ville VARCHAR(255) DEFAULT NULL, sexe VARCHAR(10) DEFAULT NULL, specialite VARCHAR(100) DEFAULT NULL, certification LONGBLOB DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activite (idAct INT AUTO_INCREMENT NOT NULL, objetAct VARCHAR(255) NOT NULL, descriptionAct VARCHAR(255) NOT NULL, distAct VARCHAR(255) NOT NULL, emailDist VARCHAR(255) NOT NULL, speciesRES VARCHAR(255) NOT NULL, etatAct VARCHAR(255) NOT NULL, PRIMARY KEY(idAct)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE materiel (idparc INT DEFAULT NULL, idMat INT AUTO_INCREMENT NOT NULL, nommat VARCHAR(255) NOT NULL, etatMat VARCHAR(255) NOT NULL, QuantiteMat DOUBLE PRECISION NOT NULL, dateAjout DATE NOT NULL, nomparc VARCHAR(255) NOT NULL, INDEX IDX_18D2B091191304A1 (idparc), PRIMARY KEY(idMat)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parc (idparc INT AUTO_INCREMENT NOT NULL, nomparc VARCHAR(255) NOT NULL, adresseParc VARCHAR(255) NOT NULL, superficieParc DOUBLE PRECISION NOT NULL, PRIMARY KEY(idparc)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ressource (idterrain INT DEFAULT NULL, idRes INT AUTO_INCREMENT NOT NULL, typeRes VARCHAR(255) NOT NULL, speciesRes VARCHAR(255) NOT NULL, quantiteRes INT NOT NULL, INDEX IDX_939F4544177201DF (idterrain), PRIMARY KEY(idRes)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE terrain (idTerrain INT AUTO_INCREMENT NOT NULL, nomTerrain VARCHAR(255) NOT NULL, localisation VARCHAR(255) NOT NULL, superficie DOUBLE PRECISION NOT NULL, PRIMARY KEY(idTerrain)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Messages ADD CONSTRAINT FK_22747CC0FEA9FF92 FOREIGN KEY (destinataire) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE Messages ADD CONSTRAINT FK_22747CC05F8A7F73 FOREIGN KEY (source) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE materiel ADD CONSTRAINT FK_18D2B091191304A1 FOREIGN KEY (idparc) REFERENCES parc (idparc)');
        $this->addSql('ALTER TABLE ressource ADD CONSTRAINT FK_939F4544177201DF FOREIGN KEY (idterrain) REFERENCES terrain (idTerrain)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Messages DROP FOREIGN KEY FK_22747CC0FEA9FF92');
        $this->addSql('ALTER TABLE Messages DROP FOREIGN KEY FK_22747CC05F8A7F73');
        $this->addSql('ALTER TABLE materiel DROP FOREIGN KEY FK_18D2B091191304A1');
        $this->addSql('ALTER TABLE ressource DROP FOREIGN KEY FK_939F4544177201DF');
        $this->addSql('DROP TABLE Categtrans');
        $this->addSql('DROP TABLE Messages');
        $this->addSql('DROP TABLE Reclamations');
        $this->addSql('DROP TABLE Transaction');
        $this->addSql('DROP TABLE Users');
        $this->addSql('DROP TABLE activite');
        $this->addSql('DROP TABLE materiel');
        $this->addSql('DROP TABLE parc');
        $this->addSql('DROP TABLE ressource');
        $this->addSql('DROP TABLE terrain');
    }
}
