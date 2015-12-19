SELECT 
	c.name_fulnam,
    summary.*
FROM
    (SELECT 
        category_id,
		COUNT(DISTINCT (lessor_user_id)) AS unique_lessors,
        count(*) as number_of_assets
    FROM
        asset
    GROUP BY category_id
    ORDER BY number_of_assets DESC) AS summary
        LEFT JOIN
    category c ON summary.category_id = c.category_id;

