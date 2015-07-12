INSERT INTO user ( name_fulnam, location_id ) 
	SELECT 'Best hire place', location.location_id
	FROM location WHERE location.name_fulnam = 'Middle of Nowhere';