<?php

//======================================
// 函数: 获取$url_key相似的每日日统计报表
// 参数: $url_key            url关键字
//       $today             起始时间
//       $endtoday          截至时间
// 返回: 记录数组
//       rpt_title          标题
//       action_count        数量
//======================================
function get_rpt_overview_detail($url_key,$today,$endtoday)
{
  $db = new DB_WWW();
  $sql = "SELECT * FROM rpt_period_url_action WHERE action_url = '{$url_key}' AND from_time_stamp >= '{$today}' AND to_time_stamp <='{$endtoday}' AND rpt_type = 'day'";
  $db->query($sql);
  $rows = $db->fetchRow();
  return $rows;
}

// ======================================
// 函数: 获取$url_key相似的当前周统计报表
// 参数: $url_key           url关键字
//       $week_time         数据获取起始时间
//       $endLastweek       数据获取截至时间
// 返回: url_count           访问总数 
//      
//======================================
function get_week_overview($url_key,$week_time,$endLastweek)
{
  $db = new DB_WWW();
  $sql = "SELECT action_count FROM rpt_period_url_action WHERE action_url  = '{$url_key}' AND from_time_stamp >= '{$week_time}' AND to_time_stamp <='{$endLastweek}' AND rpt_type='week' ";
  $url_count = $db->getField($sql, 'action_count');
  return $url_count;
}

//======================================
// 函数: 创建网址访问统计记录
// 参数: $data          信息数组
// 返回: 0              创建失败
//      创建id          成功
//======================================
function creat_rpt_detail($data)
{
  $db = new DB_WWW();
  $sql = $db->sqlInsert("rpt_period_url_action", $data);
  $q_id = $db->query($sql);
  if ($q_id == 0)
    return 0;
  return $db->insertID();
}

//======================================
// 函数: 更新网址周访问统计记录
// 参数: $data           信息数组
//       $where          更新条件
// 返回: false           更新失败
//       true            成功
//======================================
function upd_rpt_detail($data,$rpt_title)
{
  // 提交时间
  $db = new DB_WWW();
  $where ="rpt_title = '{$rpt_title}'";
  $sql = $db->sqlUpdate("rpt_period_url_action", $data,$where);
  $q_id = $db->query($sql);
  if ($q_id == 0)
    return false;
  return true;
}

?>
