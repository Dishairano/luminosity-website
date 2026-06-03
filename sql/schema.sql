-- Luminosity OS — databaseschema (logboek-tabel uit het ERD).
CREATE DATABASE IF NOT EXISTS luminosity CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE luminosity;

CREATE TABLE IF NOT EXISTS logboek (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    invoertekst VARCHAR(500)  NOT NULL,
    commando    VARCHAR(500)  NOT NULL DEFAULT '',
    status      VARCHAR(32)   NOT NULL,   -- voorbereid | geslaagd | mislukt | geannuleerd | geblokkeerd
    tijdstip    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_tijdstip (tijdstip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
