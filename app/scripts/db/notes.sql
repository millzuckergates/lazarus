DROP TABLE IF EXISTS `news`;

CREATE TABLE IF NOT EXISTS `news`
(
    `id`               int(11)      NOT NULL auto_increment,
    `date`             int(13)      NOT NULL,
    `titre`            varchar(250) NOT NULL,
    `idAuteur`         int(11)      NOT NULL,
    `nomAuteur`        varchar(150) NOT NULL,
    `texte`            text         NOT NULL,
    `type`             varchar(3)   NOT NULL,
    `idDestinataire`   int(11),
    `typeDestinataire` varchar(80),
    PRIMARY KEY (`id`),
    FOREIGN KEY (`idAuteur`) REFERENCES `personnages` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

INSERT INTO `autorisations` (`id`, `libelle`, `type`, `nomTechnique`)
VALUES (NULL, 'Accès à l\'interface MJ', 'Administration', 'acces_interface_mj');

CREATE TABLE IF NOT EXISTS `textures`
(
    `id`    INT(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
    `nom`   VARCHAR(60)                        NOT NULL UNIQUE,
    `image` VARCHAR(256)                       NOT NULL
);
INSERT INTO `textures`(`nom`, `image`)
VALUES ('Feu', '/var/www/lazarus/public/img/site/interface/textures/feu.png');
INSERT INTO `textures`(`nom`, `image`)
VALUES ('Rochers', '/var/www/lazarus/public/img/site/interface/textures/rocks.png');

ALTER TABLE `matricesinterieur`
    ADD COLUMN idTexture1 INT(11),
    ADD COLUMN idTexture2 INT(11),
    ADD COLUMN idTexture3 INT(11);
ALTER TABLE `matricesmdm`
    ADD COLUMN idTexture1 INT(11),
    ADD COLUMN idTexture2 INT(11),
    ADD COLUMN idTexture3 INT(11);
ALTER TABLE `matricesexterieur`
    ADD COLUMN idTexture1 INT(11),
    ADD COLUMN idTexture2 INT(11),
    ADD COLUMN idTexture3 INT(11);
ALTER TABLE `matricesville`
    ADD COLUMN idTexture1 INT(11),
    ADD COLUMN idTexture2 INT(11),
    ADD COLUMN idTexture3 INT(11);