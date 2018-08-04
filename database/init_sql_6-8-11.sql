-- 备份
create table dc_agent_account_info_bak like dc_agent_account_info;
create table dc_agent_account_info_log_bak like dc_agent_account_info_log;
insert into dc_agent_account_info_bak select * from dc_agent_account_info;
insert into dc_agent_account_info_log_bak select * from dc_agent_account_info_log;

-- 保留1,2,4
delete from dc_agent_account_info_log where log_type = 3 and log_agent_id > 90000;

update dc_agent_account_info set agent_account_money = 0 where agent_account_agent_id > 90000;
-- 重置推广7元 -- todo

update dc_agents_promoters_statistics set statistics_status = 0;

SELECT statistics_agents_id,statistics_money_type,SUM(statistics_my_income) as total,statistics_time from dc_agents_promoters_statistics 
where statistics_time <= 1528300800 group by statistics_agents_id having total > 0 order by total desc;

-- 执行统计脚本

-- 统计完后 扣除 已提现