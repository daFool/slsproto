create or replace view vProtoValues as
select p.*, pr.nimi as proto, pr.id, protoScore(p.pelaisitko, p.ostaisitko, p.aiemmin, p.fiilis)::numeric(5,2) as score from sessio as s
join 
proto as pr 
on s.proto=pr.id
join pelaaja as p
on 
p.sessio=s.id;

create or replace view vProtoWithValues as
select p.nimi, p.omistaja, p.luotu, p.muokattu, p.kuvaus, p.suunnittelijat, p.minimipelaajamaara,
p.maksimipelaajamaara, p.saannot, p.id, p.kohdeyleiso, p.luoja, p.omistaja_ktunnus, p.status, p.sijainti,
pv.fiilis, pv.uutuus, pv.mekaniikka, pv.idea, pv.score, pv.sosiaalisuus, pv.tuuri, pv.taktiikka, pv.strategia from 
(select * from proto) as p
join
(select id, avg(fiilis)::decimal(3,2) as fiilis, avg(uutuus)::decimal(3,2) as uutuus, avg(mekaniikka)::decimal(3,2) as mekaniikka, 
avg(idea)::decimal(3,2) as idea, avg(score)::decimal(5,2) as score, avg(sosiaalisuus)::decimal(3,2) as sosiaalisuus, 
avg(tuuri)::decimal(3,2) as tuuri, 
avg(taktiikka)::decimal(3,2) as taktiikka, avg(strategia)::decimal(3,2) as strategia from
vProtoValues group by id) as pv
on (p.id=pv.id);
