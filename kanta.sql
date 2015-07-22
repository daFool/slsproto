-- ======================================================================
-- ===   Sql Script for Database : PostgreSQL db
-- ===
-- === Build : 15
-- ======================================================================

DROP TABLE logi;
DROP TABLE kayttajatunnistus;
DROP TABLE Pelaaja;
DROP TABLE ProtoTaidot;
DROP TABLE Taito;
DROP TABLE Sessio;
DROP TABLE Ongelma;
DROP TABLE ProtonTagit;
DROP TABLE tagi;
DROP TABLE Versiot;
DROP TABLE Proto;
DROP TABLE kayttajarooli;
DROP TABLE kayttaja;

-- ======================================================================

CREATE TABLE kayttaja
  (
    nimi                varchar(255),
    slsjasennumero      varchar(255),
    puhelin             varchar(255),
    sahkoposti          varchar(255),
    syntymavuosi        varchar(255),
    tunniste            varchar(255),
    vahvistus           varchar(255),
    vahvistuslahetetty  timestamptz,
    tila                varchar(10),
    lisatty             timestamptz    default now(),
    muokattu            timestamptz,
    muokkaaja           varchar(255),
    sukupuoli           varchar(255),

    primary key(tunniste),

    foreign key(muokkaaja) references kayttaja(tunniste) on update SET NULL on delete SET NULL,

    CHECK(tila IN ('prospekti', 'käyttäjä', 'superadmin', 'admin', 'pelaaja', 'suunnittelija'))
  );

-- ======================================================================

CREATE TABLE kayttajarooli
  (
    kohde      varchar(255),
    kayttaja   varchar(255),
    roolinimi  varchar(255),

    primary key(kohde,kayttaja),

    foreign key(kayttaja) references kayttaja(tunniste) on update CASCADE on delete CASCADE
  );

-- ======================================================================

CREATE TABLE Proto
  (
    nimi                 name,
    omistaja             name,
    luotu                timestamptz    default now(),
    muokattu             timestamptz,
    kuvaus               text,
    suunnittelijat       varchar(255),
    sosiaalisuus         decimal,
    taktiikka            decimal,
    strategia            decimal,
    fiilis               decimal,
    uutuus               decimal,
    mekaniikka           decimal,
    idea                 decimal,
    tuuri                decimal,
    kesto                int2,
    minimipelaajamaara   int2,
    maksimipelaajamaara  int2,
    saannot              varchar(255),
    id                   serial,
    kohdeyleiso          varchar(255),
    luoja                varchar(255),
    omistaja_ktunnus     varchar(255),
    status               varchar(20)    default 'private',
    sijainti             varchar(255),

    primary key(id),

    foreign key(luoja) references kayttaja(tunniste) on update CASCADE on delete CASCADE,
    foreign key(omistaja_ktunnus) references kayttaja(tunniste),

    CHECK(status IN ('public', 'private', 'limited'))
  );

-- ======================================================================

CREATE TABLE Versiot
  (
    versio    varchar(255),
    proto     int,
    luotu     timestamptz    default now(),
    kuvaus    text,
    muokattu  timestamptz,

    foreign key(proto) references Proto(id) on update CASCADE on delete CASCADE
  );

-- ======================================================================

CREATE TABLE tagi
  (
    id         serial,
    nimi       name,
    selitys    varchar(255),
    lisatty    timestamptz,
    lisaaja    varchar(255),
    muokattu   timestamptz,
    muokkaaja  timestamptz,

    primary key(id),

    foreign key(lisaaja) references kayttaja(tunniste) on update SET NULL on delete SET NULL
  );

-- ======================================================================

CREATE TABLE ProtonTagit
  (
    proto  int,
    tagi   int,

    foreign key(proto) references Proto(id) on update CASCADE on delete CASCADE,
    foreign key(tagi) references tagi(id) on update CASCADE on delete CASCADE
  );

-- ======================================================================

CREATE TABLE Ongelma
  (
    proto     int,
    id        serial,
    luotu     timestamptz   default now(),
    muutettu  timestamptz,
    kuvaus    text,
    laji      varchar(50),
    korjattu  timestamptz,
    korjaus   text,

    primary key(id),

    foreign key(proto) references Proto(id) on update CASCADE on delete CASCADE,

    CHECK(laji IN ('kehitysidea', 'sääntövirhe', 'komponenttivirhe'))
  );

-- ======================================================================

CREATE TABLE Sessio
  (
    id                     serial,
    ajankohta              timestamptz    not null default now(),
    proto                  int,
    kuvaus                 text,
    saantoselitys_alkoi    timestamptz,
    saantoselitys_loppui   timestamptz,
    setup_alkoi            timestamptz,
    setup_loppui           timestamptz,
    vuoron_kesto           int2,
    kierroksen_kesto       int2,
    kierroksia             int2,
    peli_alkoi             timestamptz,
    peli_loppui            timestamptz,
    lopputoimet_alkoivat   timestamptz,
    lopputoimet_loppuivat  timestamptz,
    pelaajia               timestamptz,
    luoja                  varchar(255),
    saannotluettu          varchar(29),
    versio                 varchar(255),

    primary key(id),

    foreign key(proto) references Proto(id) on update CASCADE on delete CASCADE,
    foreign key(luoja) references kayttaja(tunniste) on update SET NULL on delete SET NULL,

    CHECK(saannotluettu IN ('silmäilty', 'ei', 'kyllä'))
  );

-- ======================================================================

CREATE TABLE Taito
  (
    id    serial,
    nimi  varchar(255),

    primary key(id)
  );

-- ======================================================================

CREATE TABLE ProtoTaidot
  (
    proto  int,
    taito  int,

    foreign key(proto) references Proto(id) on update CASCADE,
    foreign key(taito) references Taito(id) on update CASCADE on delete CASCADE
  );

-- ======================================================================

CREATE TABLE Pelaaja
  (
    sessio        int            not null,
    tunnus        varchar(255),
    numero        int2,
    sosiaalisuus  int2,
    tuuri         int2,
    taktiikka     int2,
    strategia     int2,
    ostaisitko    bool,
    pelaisitko    bool,
    kelle         varchar(255),
    assosiaatiot  varchar(255),
    fiilis        int2,
    uutuus        int2,
    mekaniikka    int2,
    idea          int2,
    sijoitus      int2,
    tulos         varchar(255),
    kokemus       int2,
    nimi          varchar(255),
    aiemmin       bool,

    foreign key(sessio) references Sessio(id) on update CASCADE on delete CASCADE,
    foreign key(tunnus) references kayttaja(tunniste) on update CASCADE on delete CASCADE
  );

-- ======================================================================

CREATE TABLE kayttajatunnistus
  (
    kayttaja        varchar(255),
    tyyppi          varchar(255),
    salaisuusavain  varchar(255),
    salaisuus       varchar(255),

    foreign key(kayttaja) references kayttaja(tunniste) on update CASCADE on delete CASCADE
  );

-- ======================================================================

CREATE TABLE logi
  (
    kuka      varchar(255),
    koska     timestamptz    default now(),
    mita      text,
    tiedosto  varchar(255),
    mika      varchar(255),
    rivi      int4,
    luokka    varchar(255)
  );

-- ======================================================================

