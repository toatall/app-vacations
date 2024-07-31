<?php

use yii\db\Migration;

/**
 * Class m240730_111815_create_procedure
 */
class m240730_111815_create_procedure extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand(<<<SQL
            
            CREATE OR REPLACE PROCEDURE merge_vacations(IN paramCodeOrg VARCHAR, IN paramYear VARCHAR(4), IN paramUpdateHash VARCHAR)
            LANGUAGE plpgsql
            AS $$
            DECLARE
                f RECORD;
                vacationId INT;
            BEGIN
                FOR f IN 
                    SELECT "vacations".* FROM "vacations"
                        RIGHT JOIN "employees" ON "employees"."id" = "vacations"."id_employee"
                        RIGHT JOIN "departments" ON "departments"."id" = "employees"."id_department"
                    WHERE "departments"."org_code" = paramCodeOrg
                    ORDER BY "vacations"."id_employee" ASC, "vacations"."date_from" ASC
                LOOP
                    -- проверяем если есть запись с датой начала -1 день, то изменяем дату на новую		
                    SELECT "id" INTO vacationId
                    FROM "vacations_merged"
                    WHERE f."date_to"::DATE = ("date_from"::DATE - INTERVAL '1 day')::DATE AND "id_employee" = f."id_employee" 
                        AND "vacations_merged"."year" = paramYear;

                    IF vacationId IS NOT NULL THEN
                        UPDATE "vacations_merged"
                            SET "date_from" = f."date_from"
                        WHERE "id" = vacationId;
                        CONTINUE;
                    END IF;

                    -- проверяем если есть запись с датой конца +1 день, то изменяем дату на новую
                    SELECT "id" INTO vacationId
                    FROM "vacations_merged"
                    WHERE ("date_to"::DATE + INTERVAL '1 day')::DATE = f."date_from"::DATE AND "id_employee" = f."id_employee"
                        AND "vacations_merged"."year" = paramYear;

                    IF vacationId IS NOT NULL THEN		
                        UPDATE "vacations_merged"
                            SET "date_to" = f."date_to"
                        WHERE "id" = vacationId;			
                        CONTINUE;
                    END IF;

                    -- просто вставляем новую запись		
                    IF (EXISTS(SELECT 1 FROM "vacations_merged" WHERE "id_employee" = f."id_employee" AND "date_from" = f."date_from"
                            AND "date_to" = f."date_to" AND "vacations_merged"."year" = paramYear)) THEN

                        UPDATE "vacations_merged"
                            SET "update_hash" = paramUpdateHash
                        WHERE "id_employee" = f."id_employee" AND "date_from" = f."date_from"
                            AND "date_to" = f."date_to" AND "vacations_merged"."year" = paramYear;	

                    ELSE                        
                        INSERT INTO "vacations_merged" ("id_employee", "date_from", "date_to", "year", "update_hash")
                            VALUES (f."id_employee", f."date_from", f."date_to", paramYear, paramUpdateHash);

                    END IF;

                END LOOP;
            END;
            $$
        
        SQL)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand('DROP PROCEDURE IF EXISTS merge_vacations(IN code_org VARCHAR, IN paramYear VARCHAR(4), IN update_has VARCHAR)')->execute();
    }

}
