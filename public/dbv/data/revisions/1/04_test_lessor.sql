INSERT INTO lessor ( user_id, url_id )
	SELECT user.user_id, url.url_id
	FROM user, url 
		WHERE user.name_fulnam = 'Best hire place'
		AND url.title_desc = 'the google';