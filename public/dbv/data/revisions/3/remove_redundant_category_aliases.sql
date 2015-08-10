DELETE FROM category_alias
	USING category
		INNER JOIN category_alias
	WHERE category.name_fulnam = category_alias.alias_fulnam;
