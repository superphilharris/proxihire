INSERT INTO asset_rate ( asset_id, duration_hrs, price_dlr )
	SELECT asset.asset_id, 12, 50
	FROM asset, user
		WHERE user.name_fulnam = 'Best hire place' 
		AND asset.lessor_user_id = user.user_id;