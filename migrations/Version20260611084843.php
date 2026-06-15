<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260611084843 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, slug VARCHAR(120) NOT NULL, icon VARCHAR(10) DEFAULT NULL, color VARCHAR(7) DEFAULT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_64C19C1989D9B62 (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE code_example (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(200) NOT NULL, code LONGTEXT NOT NULL, language VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, feature_id INT NOT NULL, INDEX IDX_564BDF0360E4B879 (feature_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE feature (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, slug VARCHAR(170) NOT NULL, description LONGTEXT NOT NULL, since_version VARCHAR(10) DEFAULT NULL, difficulty VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, category_id INT NOT NULL, UNIQUE INDEX UNIQ_1FD77566989D9B62 (slug), INDEX IDX_1FD7756612469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE feature_tag (feature_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_41E4F23060E4B879 (feature_id), INDEX IDX_41E4F230BAD26311 (tag_id), PRIMARY KEY (feature_id, tag_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE feature_term (feature_id INT NOT NULL, term_id INT NOT NULL, INDEX IDX_F72568FE60E4B879 (feature_id), INDEX IDX_F72568FEE2C35FC (term_id), PRIMARY KEY (feature_id, term_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, slug VARCHAR(120) NOT NULL, color VARCHAR(7) NOT NULL, UNIQUE INDEX UNIQ_389B783989D9B62 (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE term (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, slug VARCHAR(170) NOT NULL, definition LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_A50FE78D5E237E06 (name), UNIQUE INDEX UNIQ_A50FE78D989D9B62 (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE code_example ADD CONSTRAINT FK_564BDF0360E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id)');
        $this->addSql('ALTER TABLE feature ADD CONSTRAINT FK_1FD7756612469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE feature_tag ADD CONSTRAINT FK_41E4F23060E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feature_tag ADD CONSTRAINT FK_41E4F230BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feature_term ADD CONSTRAINT FK_F72568FE60E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feature_term ADD CONSTRAINT FK_F72568FEE2C35FC FOREIGN KEY (term_id) REFERENCES term (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE code_example DROP FOREIGN KEY FK_564BDF0360E4B879');
        $this->addSql('ALTER TABLE feature DROP FOREIGN KEY FK_1FD7756612469DE2');
        $this->addSql('ALTER TABLE feature_tag DROP FOREIGN KEY FK_41E4F23060E4B879');
        $this->addSql('ALTER TABLE feature_tag DROP FOREIGN KEY FK_41E4F230BAD26311');
        $this->addSql('ALTER TABLE feature_term DROP FOREIGN KEY FK_F72568FE60E4B879');
        $this->addSql('ALTER TABLE feature_term DROP FOREIGN KEY FK_F72568FEE2C35FC');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE code_example');
        $this->addSql('DROP TABLE feature');
        $this->addSql('DROP TABLE feature_tag');
        $this->addSql('DROP TABLE feature_term');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE term');
    }
}
