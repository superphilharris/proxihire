insert into asset (category_id, url_id, lessor_user_id)
select c.category_id, a.url_id, a.lessor_user_id from asset a join category c on true where c.name_fulnam = "ladder";

