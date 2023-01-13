DROP TABLE IF EXISTS `competences`;
DROP TABLE IF EXISTS `rangs_competence`;
DROP TABLE IF EXISTS `assoc_rangs_competence`;
DROP TABLE IF EXISTS `assoc_caracs_competence`;
DROP TABLE IF EXISTS `assoc_competences_personnage`;
DROP TABLE IF EXISTS `assoc_competences_effets_param`;
DROP TABLE IF EXISTS `assoc_competences_contraintes_param`;

CREATE TABLE IF NOT EXISTS `competences`
(
    `id`               int(11)      NOT NULL auto_increment,
    `nom`              varchar(60)  NOT NULL,
    `image`            varchar(200) NOT NULL,
    `description`      text         NOT NULL,
    `type`             varchar(60)  NOT NULL,
    `isActif`          int(1),
    `coutPA`           int(2),
    `caracAssoc`       int(11),
    `messageRP`        text,
    `evenementLanceur` text,
    `evenementGlobal`  text,
    `activable`        int(1),
    `idRangBloque`     int(11),
    `isEnseignable`    int(1)       NOT NULL,
    `isEntrainable`    int(1),
    `idRangAutonome`   int(11),
    PRIMARY KEY (`id`),
    FOREIGN KEY (`idRangBloque`) REFERENCES `rangs_competence` (`id`),
    FOREIGN KEY (`idRangAutonome`) REFERENCES `rangs_competence` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `rangs_competence`
(
    `id`              int(11)     NOT NULL auto_increment,
    `nom`             varchar(90) NOT NULL,
    `niveau`          int(2)      NOT NULL,
    `pointAAtteindre` int(8)      NOT NULL,
    `description`     text,
    PRIMARY KEY (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;


CREATE TABLE IF NOT EXISTS `assoc_rangs_competence`
(
    `idRang`            int(11) NOT NULL,
    `idCompetence`      int(11) NOT NULL,
    `nbPointAAtteindre` int(8)  NOT NULL,
    PRIMARY KEY (`idRang`, `idCompetence`),
    FOREIGN KEY (`idRang`) REFERENCES `rangs_competence` (`id`),
    FOREIGN KEY (`idCompetence`) REFERENCES `competences` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `assoc_caracs_competence`
(
    `idCarac`       int(11) NOT NULL,
    `idCompetence`  int(11) NOT NULL,
    `modificateur`  int(8)  NOT NULL,
    `isInfluenceur` int(1)  NOT NULL,
    PRIMARY KEY (`idCarac`, `idCompetence`),
    FOREIGN KEY (`idCarac`) REFERENCES `caracteristiques` (`id`),
    FOREIGN KEY (`idCompetence`) REFERENCES `competences` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `assoc_competences_personnage`
(
    `idPerso`      int(11) NOT NULL,
    `idCompetence` int(11) NOT NULL,
    `nbPoint`      int(8)  NOT NULL,
    `idRang`       int(11) NOT NULL,
    `idRangBloque` int(11),
    PRIMARY KEY (`idPerso`, `idCompetence`),
    FOREIGN KEY (`idPerso`) REFERENCES `personnages` (`id`),
    FOREIGN KEY (`idCompetence`) REFERENCES `competences` (`id`),
    FOREIGN KEY (`idRang`) REFERENCES `rangs_competence` (`id`),
    FOREIGN KEY (`idRangBloque`) REFERENCES `rangs_competence` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `assoc_competences_effets_param`
(
    `idEffet`      int(11) NOT NULL,
    `idCompetence` int(11) NOT NULL,
    `idParam`      int(11) NOT NULL,
    `valeur`       text,
    `valeurMin`    text,
    `valeurMax`    text,
    `position`     int(2),
    `action`       varchar(150),
    PRIMARY KEY (`idEffet`, `idCompetence`, `idParam`, `position`),
    FOREIGN KEY (`idEffet`) REFERENCES `effets` (`id`),
    FOREIGN KEY (`idCompetence`) REFERENCES `competences` (`id`),
    FOREIGN KEY (`idParam`) REFERENCES `parametres` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `assoc_competences_contraintes_param`
(
    `idContrainte` int(11) NOT NULL,
    `idCompetence` int(11) NOT NULL,
    `idParam`      int(11) NOT NULL,
    `valeur`       text,
    `valeurMin`    text,
    `valeurMax`    text,
    `position`     int(2),
    PRIMARY KEY (`idContrainte`, `idCompetence`, `idParam`, `position`),
    FOREIGN KEY (`idContrainte`) REFERENCES `contraintes` (`id`),
    FOREIGN KEY (`idCompetence`) REFERENCES `competences` (`id`),
    FOREIGN KEY (`idParam`) REFERENCES `parametres` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

ALTER TABLE `sorts`
    ADD `isJS`         INT(1) NOT NULL DEFAULT '0' AFTER `messageRP`,
    ADD `isJSEV`       INT(1) NOT NULL DEFAULT '0' AFTER `isJS`,
    ADD `eventLanceur` TEXT   NULL AFTER `isJSEV`,
    ADD `eventGlobal`  TEXT   NULL AFTER `eventLanceur`;