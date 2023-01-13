DROP TABLE IF EXISTS `bonus`;
DROP TABLE IF EXISTS `assoc_royaumes_bonus_param`;
DROP TABLE IF EXISTS `assoc_races_bonus_param`;
DROP TABLE IF EXISTS `assoc_religions_bonus_param`;
DROP TABLE IF EXISTS `bonus_param`;

CREATE TABLE IF NOT EXISTS `bonus`
(
    `id`          int(11)      NOT NULL auto_increment,
    `nom`         varchar(60)  NOT NULL,
    `image`       varchar(200) NOT NULL,
    `description` text         NOT NULL,
    `fichier`     varchar(60)  NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `bonus_param`
(
    `idBonus`  int(11) NOT NULL,
    `idParam`  int(11) NOT NULL,
    `position` int(3)  NOT NULL,
    PRIMARY KEY (`idBonus`, `idParam`, `position`),
    FOREIGN KEY (`idBonus`) REFERENCES `bonus` (`id`),
    FOREIGN KEY (`idParam`) REFERENCES `parametres` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `assoc_royaumes_bonus_param`
(
    `idBonus`   int(11) NOT NULL,
    `idRoyaume` int(11) NOT NULL,
    `idParam`   int(11) NOT NULL,
    `valeur`    text,
    `position`  int(2),
    PRIMARY KEY (`idBonus`, `idRoyaume`, `idParam`, `position`),
    FOREIGN KEY (`idBonus`) REFERENCES `bonus` (`id`),
    FOREIGN KEY (`idRoyaume`) REFERENCES `royaumes` (`id`),
    FOREIGN KEY (`idParam`) REFERENCES `parametres` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `assoc_races_bonus_param`
(
    `idBonus`  int(11) NOT NULL,
    `idRace`   int(11) NOT NULL,
    `idParam`  int(11) NOT NULL,
    `valeur`   text,
    `position` int(2),
    PRIMARY KEY (`idBonus`, `idRace`, `idParam`, `position`),
    FOREIGN KEY (`idBonus`) REFERENCES `bonus` (`id`),
    FOREIGN KEY (`idRace`) REFERENCES `races` (`id`),
    FOREIGN KEY (`idParam`) REFERENCES `parametres` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE IF NOT EXISTS `assoc_religions_bonus_param`
(
    `idBonus`    int(11) NOT NULL,
    `idReligion` int(11) NOT NULL,
    `idParam`    int(11) NOT NULL,
    `valeur`     text,
    `position`   int(2),
    PRIMARY KEY (`idBonus`, `idReligion`, `idParam`, `position`),
    FOREIGN KEY (`idBonus`) REFERENCES `bonus` (`id`),
    FOREIGN KEY (`idReligion`) REFERENCES `religions` (`id`),
    FOREIGN KEY (`idParam`) REFERENCES `parametres` (`id`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

INSERT INTO `bonus` (`id`, `nom`, `image`, `description`, `fichier`)
VALUES ('1', 'Don d\'une compétence', 'public/img/site/illustrations/bonus/defaut.png',
        'Ce bonus permet d\'accorder un certains rang à une compétence.', 'Bonus_Competence'),
       ('2', 'Don d\'un talent', 'public/img/site/illustrations/bonus/defaut.png',
        'Ce bonus permet d\'accorder un certains nombre de point dans un talent.', 'Bonus_Talent'),
       ('3', 'Choix d\'une compétence', 'public/img/site/illustrations/bonus/defaut.png',
        'Ce bonus permet d\'accorder un rang à une compétence au choix parmi une liste.', 'Bonus_Choix_Competence'),
       ('4', 'Choix d\'un talent', 'public/img/site/illustrations/bonus/defaut.png',
        'Ce bonus permet d\'accorder un certains nombre de point dans un talent à choisir parmi une liste.',
        'Bonus_Choix_Talent');

INSERT INTO `bonus_param` (`idBonus`, `idParam`, `position`)
VALUES ('1', '3', '1'),
       ('1', '5', '2');
INSERT INTO `bonus_param` (`idBonus`, `idParam`, `position`)
VALUES ('2', '3', '1'),
       ('2', '4', '2'),
       ('3', '2', '1'),
       ('3', '3', '2'),
       ('4', '2', '1'),
       ('4', '4', '2');

INSERT INTO `parametres` (`id`, `nom`, `description`, `type`)
VALUES ('5', 'IdElement2', 'Permet d\'utiliser un second critère en tant qu\'id d\'un élément.', 'Commun');
INSERT INTO `autorisations` (`id`, `libelle`, `type`, `nomTechnique`)
VALUES (NULL, 'Gestion des Actualités', 'Administration', 'gestion_news');