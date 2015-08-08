INSERT INTO category_alias ( category_id, alias_fulnam ) 
	SELECT category.category_id, 'renovation'
	FROM category WHERE category.name_fulnam = 'construction';
