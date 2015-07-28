CREATE OR REPLACE FUNCTION "public"."protoscore" (uudestaan boolean, ostaisi boolean, aiemmin boolean, fiilis integer)  RETURNS integer
  VOLATILE
AS $dbvis$
declare score integer;
begin
score:=0;
if uudestaan = true then
  score:=score+10;
else
  score:=score-10;
end if;
if ostaisi = true then
  score:=score+20;
end if;
if aiemmin = true then
  score:=score+10;
end if;
score:=score*(1+fiilis/10);
return score;
end;
$dbvis$ LANGUAGE plpgsql