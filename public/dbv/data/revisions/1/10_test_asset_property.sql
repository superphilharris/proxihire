INSERT INTO asset_property ( asset_id, name_fulnam, datatype_id, value_mxd )
	SELECT asset.asset_id, 'weight', datatype.datatype_id, '200.0'
	FROM asset, user, datatype
		WHERE user.name_fulnam = 'Best hire place' 
		AND asset.lessor_user_id = user.user_id
		AND datatype.datatype_abbr = 'float';