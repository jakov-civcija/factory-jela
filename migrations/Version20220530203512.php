<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220530203512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ext_translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(191) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), INDEX general_translations_lookup_idx (object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE ingredients (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE languages (id INT AUTO_INCREMENT NOT NULL, language VARCHAR(40) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meals (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, status VARCHAR(40) NOT NULL, deletedAt DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, INDEX IDX_E229E6EA12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meals_tags (meals_id INT NOT NULL, tags_id INT NOT NULL, INDEX IDX_F83DC9A688A25CA2 (meals_id), INDEX IDX_F83DC9A68D7B4FB4 (tags_id), PRIMARY KEY(meals_id, tags_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meals_ingredients (meals_id INT NOT NULL, ingredients_id INT NOT NULL, INDEX IDX_DF77A0AB88A25CA2 (meals_id), INDEX IDX_DF77A0AB3EC4DCE (ingredients_id), PRIMARY KEY(meals_id, ingredients_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE meals ADD CONSTRAINT FK_E229E6EA12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE meals_tags ADD CONSTRAINT FK_F83DC9A688A25CA2 FOREIGN KEY (meals_id) REFERENCES meals (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meals_tags ADD CONSTRAINT FK_F83DC9A68D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meals_ingredients ADD CONSTRAINT FK_DF77A0AB88A25CA2 FOREIGN KEY (meals_id) REFERENCES meals (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meals_ingredients ADD CONSTRAINT FK_DF77A0AB3EC4DCE FOREIGN KEY (ingredients_id) REFERENCES ingredients (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meals DROP FOREIGN KEY FK_E229E6EA12469DE2');
        $this->addSql('ALTER TABLE meals_ingredients DROP FOREIGN KEY FK_DF77A0AB3EC4DCE');
        $this->addSql('ALTER TABLE meals_tags DROP FOREIGN KEY FK_F83DC9A688A25CA2');
        $this->addSql('ALTER TABLE meals_ingredients DROP FOREIGN KEY FK_DF77A0AB88A25CA2');
        $this->addSql('ALTER TABLE meals_tags DROP FOREIGN KEY FK_F83DC9A68D7B4FB4');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE ext_translations');
        $this->addSql('DROP TABLE ingredients');
        $this->addSql('DROP TABLE languages');
        $this->addSql('DROP TABLE meals');
        $this->addSql('DROP TABLE meals_tags');
        $this->addSql('DROP TABLE meals_ingredients');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
