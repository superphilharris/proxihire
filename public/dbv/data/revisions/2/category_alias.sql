INSERT IGNORE INTO category (name_fulnam) VALUES ('construction'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'construction'
	FROM category WHERE category.name_fulnam = 'construction';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'building'
	FROM category WHERE category.name_fulnam = 'construction';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'renovation'
	FROM category WHERE category.name_fulnam = 'construction';
INSERT IGNORE INTO category (name_fulnam) VALUES ('ladder'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'ladder'
	FROM category WHERE category.name_fulnam = 'ladder';
INSERT IGNORE INTO category (name_fulnam) VALUES ('fence'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'fence'
	FROM category WHERE category.name_fulnam = 'fence';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'fencing'
	FROM category WHERE category.name_fulnam = 'fence';
INSERT IGNORE INTO category (name_fulnam) VALUES ('portaloo'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'portaloo'
	FROM category WHERE category.name_fulnam = 'portaloo';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'loo'
	FROM category WHERE category.name_fulnam = 'portaloo';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'toilet'
	FROM category WHERE category.name_fulnam = 'portaloo';
INSERT IGNORE INTO category (name_fulnam) VALUES ('catering'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'catering'
	FROM category WHERE category.name_fulnam = 'catering';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'event'
	FROM category WHERE category.name_fulnam = 'catering';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'party'
	FROM category WHERE category.name_fulnam = 'catering';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'wedding'
	FROM category WHERE category.name_fulnam = 'catering';
INSERT IGNORE INTO category (name_fulnam) VALUES ('speaker'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'speaker'
	FROM category WHERE category.name_fulnam = 'speaker';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'sound'
	FROM category WHERE category.name_fulnam = 'speaker';
INSERT IGNORE INTO category (name_fulnam) VALUES ('tent'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'tent'
	FROM category WHERE category.name_fulnam = 'tent';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'hall'
	FROM category WHERE category.name_fulnam = 'tent';
INSERT IGNORE INTO category (name_fulnam) VALUES ('dishes'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'dishes'
	FROM category WHERE category.name_fulnam = 'dishes';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'cutlery'
	FROM category WHERE category.name_fulnam = 'dishes';
INSERT IGNORE INTO category (name_fulnam) VALUES ('spoon'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'spoon'
	FROM category WHERE category.name_fulnam = 'spoon';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'teaspoon'
	FROM category WHERE category.name_fulnam = 'spoon';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'tablespoon'
	FROM category WHERE category.name_fulnam = 'spoon';
INSERT IGNORE INTO category (name_fulnam) VALUES ('plate'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'plate'
	FROM category WHERE category.name_fulnam = 'plate';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'platter'
	FROM category WHERE category.name_fulnam = 'plate';
INSERT IGNORE INTO category (name_fulnam) VALUES ('decoration'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'decoration'
	FROM category WHERE category.name_fulnam = 'decoration';
INSERT IGNORE INTO category (name_fulnam) VALUES ('vase'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'vase'
	FROM category WHERE category.name_fulnam = 'vase';
INSERT IGNORE INTO category (name_fulnam) VALUES ('flower'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'flower'
	FROM category WHERE category.name_fulnam = 'flower';
INSERT IGNORE INTO category (name_fulnam) VALUES ('travel'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'travel'
	FROM category WHERE category.name_fulnam = 'travel';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'tourism'
	FROM category WHERE category.name_fulnam = 'travel';
INSERT IGNORE INTO category (name_fulnam) VALUES ('bike'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'bike'
	FROM category WHERE category.name_fulnam = 'bike';
INSERT IGNORE INTO category (name_fulnam) VALUES ('vehicle'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'vehicle'
	FROM category WHERE category.name_fulnam = 'vehicle';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'car'
	FROM category WHERE category.name_fulnam = 'vehicle';
INSERT IGNORE INTO category (name_fulnam) VALUES ('campervan'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'campervan'
	FROM category WHERE category.name_fulnam = 'campervan';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'camper'
	FROM category WHERE category.name_fulnam = 'campervan';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'campa'
	FROM category WHERE category.name_fulnam = 'campervan';
INSERT IGNORE INTO category (name_fulnam) VALUES ('van'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'van'
	FROM category WHERE category.name_fulnam = 'van';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'truck'
	FROM category WHERE category.name_fulnam = 'van';
INSERT IGNORE INTO category (name_fulnam) VALUES ('sedan'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'sedan'
	FROM category WHERE category.name_fulnam = 'sedan';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'car'
	FROM category WHERE category.name_fulnam = 'sedan';
INSERT IGNORE INTO category (name_fulnam) VALUES ('kayak'); 
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'kayak'
	FROM category WHERE category.name_fulnam = 'kayak';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'boat'
	FROM category WHERE category.name_fulnam = 'kayak';
INSERT IGNORE INTO category_alias (category_id, alias_fulnam) 
	SELECT category.category_id, 'jetski'
	FROM category WHERE category.name_fulnam = 'kayak';
