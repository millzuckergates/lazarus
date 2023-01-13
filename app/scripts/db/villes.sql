DROP TABLE IF EXISTS `villes`;
DROP TABLE IF EXISTS `bannissements_ville`;
DROP TABLE IF EXISTS `autorisations_ville`;
DROP TABLE IF EXISTS `gestionnaires_ville`;

CREATE TABLE IF NOT EXISTS `villes`
(
    `id`               int(11)      NOT NULL auto_increment,
    `nom`              varchar(60)  NOT NULL,
    `image`            varchar(200) NOT NULL,
    `description`      text         NOT NULL,
    `idArticle`        int(11),
    `idRoyaumeOrigine` int(11)      NOT NULL,
    `idRoyaumeActuel`  int(11)      NOT NULL,
    `messageAccueil`   text         NOT NULL,
    `isNaissance`      int(1)       NOT NULL,
    `xMin`             int(6),
    `xMax`             int(6),
    `yMin`             int(6),
    `yMax`             int(6),
    PRIMARY KEY (`id`),
    FOREIGN KEY (`idRoyaumeOrigine`) REFERENCES `royaumes` (`id`),
    FOREIGN KEY (`idRoyaumeActuel`) REFERENCES `royaumes` (`id`),
    FOREIGN KEY (`idArticle`) REFERENCES `articles` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `bannissements_ville`
(
    `idPerso`      int(11) NOT NULL,
    `idVille`      int(11) NOT NULL,
    `dateDebut`    int(11) NOT NULL,
    `dateFin`      int(11) NOT NULL,
    `raison`       text    NOT NULL,
    `idBannisseur` int(11) NOT NULL,
    PRIMARY KEY (`idPerso`, `idVille`),
    FOREIGN KEY (`idPerso`) REFERENCES `personnages` (`id`),
    FOREIGN KEY (`idVille`) REFERENCES `villes` (`id`),
    FOREIGN KEY (`idBannisseur`) REFERENCES `personnages` (`id`)

) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `autorisations_ville`
(
    `idPerso`        int(11) NOT NULL,
    `idVille`        int(11) NOT NULL,
    `dateDebut`      int(11) NOT NULL,
    `dateFin`        int(11) NOT NULL,
    `raison`         text    NOT NULL,
    `idAutorisateur` int(11) NOT NULL,
    PRIMARY KEY (`idPerso`, `idVille`),
    FOREIGN KEY (`idPerso`) REFERENCES `personnages` (`id`),
    FOREIGN KEY (`idVille`) REFERENCES `villes` (`id`),
    FOREIGN KEY (`idAutorisateur`) REFERENCES `personnages` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `gestionnaires_ville`
(
    `idPerso` int(11)     NOT NULL,
    `idVille` int(11)     NOT NULL,
    `droit`   varchar(30) NOT NULL,
    PRIMARY KEY (`idPerso`, `idVille`),
    FOREIGN KEY (`idPerso`) REFERENCES `personnages` (`id`),
    FOREIGN KEY (`idVille`) REFERENCES `villes` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;