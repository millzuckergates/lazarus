CREATE TABLE IF NOT EXISTS `assoc_races_religion_jouable`
(
    `idRace`     int(11) NOT NULL,
    `idReligion` int(11) NOT NULL,
    PRIMARY KEY (`idRace`, `idReligion`),
    FOREIGN KEY (`idRace`) REFERENCES `races` (`id`),
    FOREIGN KEY (`idReligion`) REFERENCES `religions` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

CREATE TABLE `assoc_sorts_effets_param`
(
    `idEffet`   int(11) NOT NULL,
    `idSort`    int(11) NOT NULL,
    `idParam`   int(11) NOT NULL,
    `valeur`    text,
    `valeurMin` text,
    `valeurMax` text,
    `position`  int(2)  NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;


ALTER TABLE `sorts`
    ADD `messageRP` TEXT;

INSERT INTO `autorisations` (`id`, `libelle`, `type`, `nomTechnique`)
VALUES ('28', 'Gestion des gifs', 'Administration', 'administration_gestion_gif');

INSERT INTO `autorisations` (`id`, `libelle`, `type`, `nomTechnique`)
VALUES ('29', 'Consultation des compétences', 'Gameplay', 'gameplay_gestion_competence_consulter');
INSERT INTO `autorisations` (`id`, `libelle`, `type`, `nomTechnique`)
VALUES ('30', 'Modification des compétences', 'Gameplay', 'gameplay_gestion_competence_modifier');
INSERT INTO `autorisations` (`id`, `libelle`, `type`, `nomTechnique`)
VALUES ('31', 'Consultation des équipements', 'Gameplay', 'gameplay_gestion_equipement_consulter');
INSERT INTO `autorisations` (`id`, `libelle`, `type`, `nomTechnique`)
VALUES ('32', 'Modification des équipements', 'Gameplay', 'gameplay_gestion_equipement_modifier');
INSERT INTO `autorisations` (`id`, `libelle`, `type`, `nomTechnique`)
VALUES ('33', 'Consultation des créatures', 'Gameplay', 'gameplay_gestion_creature_consulter');
INSERT INTO `autorisations` (`id`, `libelle`, `type`, `nomTechnique`)
VALUES ('34', 'Modification des créatures', 'Gameplay', 'gameplay_gestion_creature_modifier');
INSERT INTO `autorisations` (`id`, `libelle`, `type`, `nomTechnique`)
VALUES ('35', 'Consultation des cartes', 'Gameplay', 'gameplay_cartes_consulter');
INSERT INTO `autorisations` (`id`, `libelle`, `type`, `nomTechnique`)
VALUES ('36', 'Modification des cartes', 'Gameplay', 'gameplay_cartes_modifier');


CREATE TABLE `assoc_cartes_effets_param`
(
    `idEffet`   int(11) NOT NULL,
    `idCarte`   int(11) NOT NULL,
    `idParam`   int(11) NOT NULL,
    `valeur`    text,
    `valeurMin` text,
    `valeurMax` text,
    `action`    varchar(80),
    `position`  int(2)  NOT NULL
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;


//################ SQL pour les talents #################//
DROP TABLE IF EXISTS `categories_talent`;
DROP TABLE IF EXISTS `familles_talent`;
DROP TABLE IF EXISTS `assoc_familletalents_contraintes_param`;
DROP TABLE IF EXISTS `arbres_talent`;
DROP TABLE IF EXISTS `assoc_arbretalents_contraintes_param`;
DROP TABLE IF EXISTS `talents`;
DROP TABlE IF EXISTS `assoc_talents_effets_param`;
DROP TABlE IF EXISTS `assoc_talents_contraintes_param`;
DROP TABLE IF EXISTS `genealogie_talents`;


CREATE TABLE IF NOT EXISTS `categories_talent`
(
    `id`          int(11)     NOT NULL auto_increment,
    `nom`         varchar(60) NOT NULL,
    `description` text        NOT NULL,
    `image`       varchar(80) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `familles_talent`
(
    `id`          int(11)     NOT NULL auto_increment,
    `idCategorie` int(11)     NOT NULL,
    `nom`         varchar(60) NOT NULL,
    `description` text        NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`idCategorie`) REFERENCES `categories_talent` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `assoc_familletalents_contraintes_param`
(
    `idContrainte` int(11) NOT NULL,
    `idFamille`    int(11) NOT NULL,
    `idParam`      int(11) NOT NULL,
    `valeur`       text,
    `valeurMin`    text,
    `valeurMax`    text,
    `position`     int(2),
    PRIMARY KEY (`idContrainte`, `idFamille`, `idParam`, `position`),
    FOREIGN KEY (`idContrainte`) REFERENCES `contraintes` (`id`),
    FOREIGN KEY (`idFamille`) REFERENCES `familles_talent` (`id`),
    FOREIGN KEY (`idParam`) REFERENCES `parametres` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;


CREATE TABLE IF NOT EXISTS `arbres_talent`
(
    `id`          int(11)     NOT NULL auto_increment,
    `idFamille`   int(11)     NOT NULL,
    `nom`         varchar(60) NOT NULL,
    `description` text        NOT NULL,
    `image`       text        NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`idFamille`) REFERENCES `familles_talent` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `assoc_arbretalents_contraintes_param`
(
    `idContrainte` int(11) NOT NULL,
    `idArbre`      int(11) NOT NULL,
    `idParam`      int(11) NOT NULL,
    `valeur`       text,
    `valeurMin`    text,
    `valeurMax`    text,
    `position`     int(2),
    PRIMARY KEY (`idContrainte`, `idArbre`, `idParam`, `position`),
    FOREIGN KEY (`idContrainte`) REFERENCES `contraintes` (`id`),
    FOREIGN KEY (`idArbre`) REFERENCES `arbres_talent` (`id`),
    FOREIGN KEY (`idParam`) REFERENCES `parametres` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `talents`
(
    `id`          int(11)     NOT NULL auto_increment,
    `idArbre`     int(11)     NOT NULL,
    `nom`         varchar(60) NOT NULL,
    `description` text        NOT NULL,
    `isActif`     int(1)      NOT NULL default 1,
    `image`       varchar(80) NOT NULL,
    `niveau_max`  int(2)      NOT NULL default 0,
    `rang`        int(2)      NOT NULL default 0,
    `position`    int(2)      NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`idArbre`) REFERENCES `arbres_talent` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `assoc_talents_effets_param`
(
    `idEffet`   int(11) NOT NULL,
    `idTalent`  int(11) NOT NULL,
    `idParam`   int(11) NOT NULL,
    `valeur`    text,
    `valeurMin` text,
    `valeurMax` text,
    `position`  int(2),
    PRIMARY KEY (`idEffet`, `idTalent`, `idParam`, `position`),
    FOREIGN KEY (`idEffet`) REFERENCES `effets` (`id`),
    FOREIGN KEY (`idTalent`) REFERENCES `talents` (`id`),
    FOREIGN KEY (`idParam`) REFERENCES `parametres` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `assoc_talents_contraintes_param`
(
    `idContrainte` int(11) NOT NULL,
    `idTalent`     int(11) NOT NULL,
    `idParam`      int(11) NOT NULL,
    `valeur`       text,
    `valeurMin`    text,
    `valeurMax`    text,
    `position`     int(2),
    PRIMARY KEY (`idContrainte`, `idTalent`, `idParam`, `position`),
    FOREIGN KEY (`idContrainte`) REFERENCES `contraintes` (`id`),
    FOREIGN KEY (`idTalent`) REFERENCES `talents` (`id`),
    FOREIGN KEY (`idParam`) REFERENCES `parametres` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `genealogie_talents`
(
    `idPere` int(11) NOT NULL,
    `idFils` int(11) NOT NULL,
    PRIMARY KEY (`idPere`, `idFils`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;