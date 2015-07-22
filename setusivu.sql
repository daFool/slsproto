CREATE OR REPLACE VIEW
    setusivu
    (
        id,
        ajankohta,
        nimi,
        luoja,
        kesto,
        pelaajia
    ) AS
SELECT
    s.id,
    s.ajankohta,
    p.nimi,
    s.luoja,
    (s.lopputoimet_loppuivat - s.saantoselitys_alkoi) AS kesto,
    (
        SELECT
            COUNT(*) AS COUNT
        FROM
            pelaaja
        WHERE
            (
                pelaaja.sessio = s.id)) AS pelaajia
FROM
    (sessio s
JOIN
    proto p
ON
    ((
            s.proto = p.id)));