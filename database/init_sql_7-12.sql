DROP TABLE IF EXISTS `dc_new_bonus_num`;
CREATE TABLE `dc_new_bonus_num` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(20) DEFAULT '',
  `time` int(11) DEFAULT '0' COMMENT '当天时间戳',
  `game_id` int(11) DEFAULT '0' COMMENT '游戏id',
  `num` int(11) DEFAULT '0' COMMENT '奖励领取数',
  `status` tinyint(3) DEFAULT '0' COMMENT '0-未完成，1-已完成',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `dc_new_game_record`;
CREATE TABLE `dc_new_game_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) DEFAULT '0' COMMENT '玩家id',
  `promoter_player_id` int(11) DEFAULT '0' COMMENT '推广人',
  `game_id` int(11) DEFAULT '0' COMMENT '游戏id',
  `win_num` int(11) DEFAULT '0' COMMENT '赢牌次数',
  `status` tinyint(3) DEFAULT '0' COMMENT '0-未达成，1-已达成，2-已发红包',
  `bonus_id` int(11) DEFAULT '0' COMMENT '红包id',
  `done_num` int(11) DEFAULT '0' COMMENT '达成数',
  `done_time` int(11) DEFAULT '0' COMMENT '达成时间',
  `done_date` varchar(20) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `dc_new_player_detail`;
CREATE TABLE `dc_new_player_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `promoter_player_id` int(11) DEFAULT '0' COMMENT '推广人',
  `player_id` int(11) DEFAULT '0' COMMENT '被推广人',
  `add_time` int(11) DEFAULT '0' COMMENT '推广时间',
  `add_date` varchar(20) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `dc_new_promoter`;
CREATE TABLE `dc_new_promoter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) DEFAULT '0' COMMENT '推广人id',
  `promote_num` int(11) DEFAULT '0' COMMENT '推广的人数',
  `promote_cost` bigint(20) DEFAULT '0' COMMENT '推广的玩家消耗金币数',
  `status` tinyint(3) DEFAULT '0' COMMENT '0-未达成，1-已达成，2-已发红包',
  `add_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0' COMMENT '达成时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


alter table dc_wx_bonus_log modify `type` int(11) DEFAULT '0' COMMENT '类型：1-提现，2-邀新活动红包';

INSERT INTO `dc_u3d_king`.`dc_config` (`config_name`, `config_desc`, `config_type`, `config_start_time`, `config_end_time`, `config_config`, `config_status`, `config_create_time`) VALUES ('new_player_bonus', '邀新送红包活动', '0', '1531756800', '1532016000', '{\"bonus_day_num\":5000,\"game_id\":20020400,\"game_name\":\"\\u7ea2\\u4e2d\\u9ebb\\u5c06\",\"win_num\":3,\"bonus\":2}', '1', '0');
