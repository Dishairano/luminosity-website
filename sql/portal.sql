-- Luminosity Portaal — datamodel voor abonnementen, facturen en klantgegevens.
-- Klant- en adminpaneel (WHMCS-achtig) in Luminosity-huisstijl.

USE luminosity;

-- --- Klanten -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS klanten (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    naam            VARCHAR(120) NOT NULL,
    email           VARCHAR(190) NOT NULL,
    wachtwoord_hash VARCHAR(255) NOT NULL,
    bedrijf         VARCHAR(120) NULL,
    btw_nummer      VARCHAR(40)  NULL,
    adres           VARCHAR(160) NULL,
    postcode        VARCHAR(16)  NULL,
    stad            VARCHAR(80)  NULL,
    land            VARCHAR(60)  NOT NULL DEFAULT 'Nederland',
    telefoon        VARCHAR(40)  NULL,
    status          ENUM('actief','geschorst') NOT NULL DEFAULT 'actief',
    aangemaakt      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_klant_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --- Admins --------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    naam            VARCHAR(120) NOT NULL,
    email           VARCHAR(190) NOT NULL,
    wachtwoord_hash VARCHAR(255) NOT NULL,
    rol             ENUM('beheerder','support') NOT NULL DEFAULT 'beheerder',
    aangemaakt      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_admin_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --- Plannen (producten / betaalde AI's) --------------------------------
CREATE TABLE IF NOT EXISTS plannen (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    code          VARCHAR(40)  NOT NULL,
    naam          VARCHAR(80)  NOT NULL,
    beschrijving  VARCHAR(255) NOT NULL DEFAULT '',
    prijs_maand   DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    prijs_jaar    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    features      TEXT NULL,                 -- JSON-array met feature-regels
    populair      TINYINT(1) NOT NULL DEFAULT 0,
    actief        TINYINT(1) NOT NULL DEFAULT 1,
    sortering     INT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_plan_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --- Abonnementen --------------------------------------------------------
CREATE TABLE IF NOT EXISTS abonnementen (
    id                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    klant_id             INT UNSIGNED NOT NULL,
    plan_id              INT UNSIGNED NOT NULL,
    periode              ENUM('maand','jaar') NOT NULL DEFAULT 'maand',
    status               ENUM('actief','opgezegd','niet_betaald','proef') NOT NULL DEFAULT 'niet_betaald',
    start_datum          DATE NULL,
    volgende_factuur     DATE NULL,
    opgezegd_op          TIMESTAMP NULL,
    mollie_customer_id   VARCHAR(64) NULL,
    mollie_subscription_id VARCHAR(64) NULL,
    aangemaakt           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_ab_klant (klant_id),
    CONSTRAINT fk_ab_klant FOREIGN KEY (klant_id) REFERENCES klanten(id) ON DELETE CASCADE,
    CONSTRAINT fk_ab_plan  FOREIGN KEY (plan_id)  REFERENCES plannen(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --- Facturen ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS facturen (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nummer        VARCHAR(24) NOT NULL,
    klant_id      INT UNSIGNED NOT NULL,
    abonnement_id INT UNSIGNED NULL,
    status        ENUM('concept','open','betaald','vervallen','geannuleerd') NOT NULL DEFAULT 'open',
    subtotaal     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    btw_percentage DECIMAL(5,2) NOT NULL DEFAULT 21.00,
    btw_bedrag    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    totaal        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    uitgiftedatum DATE NOT NULL,
    vervaldatum   DATE NOT NULL,
    betaald_op    TIMESTAMP NULL,
    mollie_payment_id VARCHAR(64) NULL,
    aangemaakt    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_factuur_nummer (nummer),
    KEY idx_fac_klant (klant_id),
    CONSTRAINT fk_fac_klant FOREIGN KEY (klant_id) REFERENCES klanten(id) ON DELETE CASCADE,
    CONSTRAINT fk_fac_ab    FOREIGN KEY (abonnement_id) REFERENCES abonnementen(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS factuurregels (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    factuur_id  INT UNSIGNED NOT NULL,
    omschrijving VARCHAR(190) NOT NULL,
    aantal      INT NOT NULL DEFAULT 1,
    prijs       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    regeltotaal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    PRIMARY KEY (id),
    KEY idx_regel_factuur (factuur_id),
    CONSTRAINT fk_regel_factuur FOREIGN KEY (factuur_id) REFERENCES facturen(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --- Betalingen ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS betalingen (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    factuur_id    INT UNSIGNED NOT NULL,
    mollie_payment_id VARCHAR(64) NULL,
    bedrag        DECIMAL(10,2) NOT NULL,
    status        VARCHAR(32) NOT NULL DEFAULT 'open',  -- open|paid|failed|expired|canceled
    methode       VARCHAR(40) NULL,
    aangemaakt    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_bet_factuur (factuur_id),
    CONSTRAINT fk_bet_factuur FOREIGN KEY (factuur_id) REFERENCES facturen(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --- Seed: plannen (de "betaalde AI's") ---------------------------------
INSERT INTO plannen (code, naam, beschrijving, prijs_maand, prijs_jaar, features, populair, sortering)
VALUES
 ('gratis','Gratis','De lokale, offline taal-engine. Altijd gratis.',0.00,0.00,
   '["Lokale offline taal-engine","Onbeperkt commando\\u0027s vertalen","Geen cloud-AI","Community-ondersteuning"]',0,1),
 ('plus','Plus','Cloud-AI-assistent met hogere limieten.',9.00,90.00,
   '["Alles van Gratis","Cloud-AI-assistent","Begrijpelijke foutuitleg met AI","Hogere limieten","E-mailondersteuning"]',1,2),
 ('pro','Pro','Geavanceerde AI-modellen en API-toegang.',19.00,190.00,
   '["Alles van Plus","Geavanceerde AI-modellen","API-toegang","Teamfuncties (tot 5 leden)","Prioriteitsondersteuning"]',0,3)
ON DUPLICATE KEY UPDATE naam=VALUES(naam), beschrijving=VALUES(beschrijving),
   prijs_maand=VALUES(prijs_maand), prijs_jaar=VALUES(prijs_jaar), features=VALUES(features),
   populair=VALUES(populair), sortering=VALUES(sortering);
