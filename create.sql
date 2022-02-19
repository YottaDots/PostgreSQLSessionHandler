/* ---------------------------------------------------------------------- */
/* Script generated with: DeZign for Databases V9.2.1                     */
/* Target DBMS:           PostgreSQL 9                                    */
/* Project file:          YottaDots_Websocketv7.0.dez                     */
/* Project name:                                                          */
/* Author:                                                                */
/* Script type:           Database creation script                        */
/* Created on:            2022-02-19 10:01                                */
/* ---------------------------------------------------------------------- */


/* ---------------------------------------------------------------------- */
/* To create a good sessionid using pgcrypto is enough. should be installed by the way otherwise it doesnt work.*/
/* ---------------------------------------------------------------------- */

--DROP extension pgcrypto; /* only use the drop when it already exists */
CREATE EXTENSION pgcrypto;
SELECT gen_random_uuid() AS wwwww;
select pg_get_functiondef(to_regproc('gen_random_uuid'));

/* ---------------------------------------------------------------------- */
/* Add sequences                                                          */
/* ---------------------------------------------------------------------- */

CREATE SEQUENCE sessiondata_idsessiondata_seq INCREMENT 1 START 1;

/* ---------------------------------------------------------------------- */
/* Add table "sessiondata"                                         */
/* ---------------------------------------------------------------------- */

CREATE TABLE sessiondata (
        idsessiondata TEXT  NOT NULL,
        sessiondata TEXT  NOT NULL,
        lastchanged TIMESTAMP  NOT NULL,
        CONSTRAINT pk_sessiondata PRIMARY KEY (idsessiondata)
);

/* ---------------------------------------------------------------------- */
/*
 Create an unique sessionid
    function can be called:
    SELECT createuniquesessionid(); --beaware that gen_random_uuid needs to be existing
 */
/* ---------------------------------------------------------------------- */


CREATE OR REPLACE FUNCTION createuniquesessionid () RETURNS text AS
$BODY$
DECLARE
    new_sessionid text ;
    new_sessionidexists text;
BEGIN

    SELECT gen_random_uuid() INTO new_sessionid;
    /*check if the sessionid already exist*/
    SELECT idsessiondata FROM sessiondata WHERE idsessiondata = new_sessionid INTO new_sessionidexists;
    IF new_sessionidexists IS NULL THEN
        INSERT INTO sessiondata (
            "idsessiondata",
            "sessiondata",
            "lastchanged"
        ) VALUES (
                     new_sessionid,
                     '',
                     now()
                 );
    ELSE
        /*
        the value has already been found and so not unique, continue with generating a new value till there is one found which is unique
         */
        SELECT createuniquesessionid() INTO new_sessionid;
    END IF;
    RETURN new_sessionid;
END;
$BODY$
    LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
