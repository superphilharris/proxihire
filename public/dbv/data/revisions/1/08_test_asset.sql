INSERT INTO asset ( category_id, url_id, lessor_user_id )
	SELECT category.category_id, url.url_id, user.user_id
	FROM category, url, user
		WHERE category.name_fulnam = 'construction'
		AND url.title_desc is null
		AND user.name_fulnam = 'Best hire place';