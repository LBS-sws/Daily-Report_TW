DROP FUNCTION `CustomerStatus`;
DELIMITER //
CREATE FUNCTION `CustomerStatus`(p_id int unsigned, p_code varchar(20), p_name varchar(1000), p_city char(5)) RETURNS char(1) CHARSET utf8
BEGIN
DECLARE status char(1);
SET status = (
SELECT case when count(a.id)=0 then 'U'
when sum(case when a.status<>'T' then 1 else 0 end) > 0 then 'A'
else 'T'
end
FROM swo_service a
LEFT OUTER JOIN swo_service b ON substring(a.company_name, 1, position(' ' in a.company_name))=substring(b.company_name, 1, position(' ' in b.company_name)) 
AND a.status_dt < b.status_dt AND a.cust_type=b.cust_type
WHERE b.id IS NULL AND a.city=p_city
AND (a.company_id=p_id OR a.company_name like concat(p_code,' %'))
);
RETURN status;
END//
DELIMITER ;