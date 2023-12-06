#!/bin/bash

DATE_NOW=$(date +%s)

if [ "$1" = "Relative" ]; then
	/usr/bin/psql --tuples-only -d IBSng -U ibs -c "select users.user_id from users where  (user_id in (select rel_exp_date.user_id from (select attr_value::bigint,user_id from user_attrs where attr_name='first_login') as first_login, (select attr_value::bigint,user_id from user_attrs where attr_name='rel_exp_date' union select group_attrs.attr_value::bigint,user_id from users,group_attrs where users.group_id=group_attrs.group_id and group_attrs.attr_name='rel_exp_date' and user_id not in (select user_attrs.user_id from user_attrs where user_attrs.attr_name='rel_exp_date')) as rel_exp_date where rel_exp_date.user_id=first_login.user_id and first_login.attr_value+rel_exp_date.attr_value < $DATE_NOW ))" | tr -d '[:blank:]' | sort -h
elif [ "$1" = "Absolute" ]; then
	/usr/bin/psql --tuples-only -d IBSng -U ibs -c "select user_id from (select count(user_id) as count,user_id from (select user_attrs.user_id from user_attrs where ( (user_attrs.attr_name = 'abs_exp_date' and (cast(user_attrs.attr_value as bigint) < cast('$DATE_NOW' as bigint)) ) )  union all select users.user_id from users,groups,group_attrs where users.group_id = groups.group_id and groups.group_id = group_attrs.group_id and user_id not in (select user_id from user_attrs where attr_name='abs_exp_date') and (group_attrs.attr_name = 'abs_exp_date' and (cast(group_attrs.attr_value as bigint) < cast('$DATE_NOW' as bigint)) ) ) as all_attrs group by user_id) as filtered_attrs where count=1" | tr -d '[:blank:]' | sort -h
else
	echo "failed command"
fi
