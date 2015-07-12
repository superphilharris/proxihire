INSERT INTO category_alias ( category_id, alias_fulnam ) 
	SELECT category.category_id, 'building'
	FROM category WHERE category.name_fulnam = 'construction';