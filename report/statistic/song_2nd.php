<?php
require_once '../inc/common.php';

header("cache-control:no-cache,must-revalidate");
header("Content-Type:text/html;charset=utf-8");

php_begin();

// 第二届金曲大赏
$pj_id = 4;

$db = new DB_STATISTIC();
// 项目名称
$sql = "SELECT * FROM vote_project WHERE pj_id = {$pj_id}";
$db->query($sql);
$row = $db->fetchRow();

// 总投票数
$total_vote_sum = intval($row['vote_sum']);

$project_str = '<h1>' . $row['pj_name'] . '<br><small>' . date('y年m月d日', strtotime($row['pj_from_time'])) . '-' . date('y年m月d日', strtotime($row['pj_to_time'])) . '</small></h1>';

// 总览
$sql = "SELECT * FROM vote_overview WHERE pj_id = {$pj_id} ORDER BY sort_no";
$db->query($sql);
$rows = $db->fetchAll();
$overview_str = "<dl class='dl-horizontal'>";

foreach ($rows as $row)
{
  $column_name = $row['column_name'];
  $vote_sum = intval($row['vote_sum']);
  $rise_nm = intval($row['rise_nm']);
  $overview_str .= "<dt>$column_name</<dt><dd>$vote_sum 增长：$rise_nm%</dd>";
}
$overview_str .= "</dl>";

// 投票券
$sql = "SELECT * FROM ticket_count WHERE pj_id = {$pj_id} ORDER BY vote_nm";
$db->query($sql);
$rows = $db->fetchAll();
$ticket_str = "<dl class='dl-horizontal'>";

foreach ($rows as $row)
{
  $t_type = $row['t_type'];
  $total_nm = intval($row['total_nm']);
  $use_nm = intval($row['use_nm']);
  $guest_nm = intval($row['guest_nm']);
  $unuse_nm = intval($row['unuse_nm']);
  $ticket_str .= "<dt>$t_type</<dt><dd>总券数：$total_nm 激活券数：$use_nm 未使用券数：$unuse_nm</dd>";
}
$ticket_str .= "</dl>";

// 用户
$sql = "SELECT * FROM user_count WHERE pj_id = {$pj_id} ORDER BY sort_no";
$db->query($sql);
$rows = $db->fetchAll();
$user_str = "<dl class='dl-horizontal'>";

foreach ($rows as $row)
{
  $u_type = $row['u_type'];
  $user_count = intval($row['user_count']);
  $user_sum = intval($row['user_sum']);
  $vote_ret = round(($user_sum * 100) / $total_vote_sum, 1);
  $user_str .= "<dt>投票$u_type</<dt><dd>用户数：$user_count 投票数：$user_sum 总投票占比：$vote_ret %</dd>";
}
$user_str .= "</dl>";

// 金曲排行榜
$sql = "SELECT * FROM vote_rank WHERE pj_id = {$pj_id} AND option_type = 1 ORDER BY total_rank";
$db->query($sql);
$rows = $db->fetchAll();
$song_rank_str = "<dl class='dl-horizontal'>";

$board_flg = true;

foreach ($rows as $row)
{
  $option_name = $row['option_name'];
  $total_rank = intval($row['total_rank']);
  $vote_sum = intval($row['vote_sum']);
  $user_sum = intval($row['user_sum']);
  $onboard = intval($row['onboard']);
  $vote_ret = round(($vote_sum * 100) / $total_vote_sum, 1);
  
  if ($board_flg && $onboard == 0) {
    $song_rank_str .= "<h2>----未入围----</h2><hr>";
    $board_flg = false;
  }
  $song_rank_str .= "<dt>$total_rank ：$option_name</<dt><dd> 得票数：$vote_sum 参与人数：$user_sum 总投票占比：$vote_ret %</dd>";
}
$song_rank_str .= "</dl>";

// 成员排行榜
$sql = "SELECT * FROM vote_rank WHERE pj_id = {$pj_id} AND option_type = 0 ORDER BY total_rank";
$db->query($sql);
$rows = $db->fetchAll();
$member_rank_str = "<dl class='dl-horizontal'>";

$total_vote_sum = 645978;

foreach ($rows as $row)
{
  $option_name = $row['option_name'];
  $total_rank = intval($row['total_rank']);
  $vote_sum = intval($row['vote_sum']);
  $user_sum = intval($row['user_sum']);
  $onboard = intval($row['onboard']);
  $vote_ret = round(($vote_sum * 100) / $total_vote_sum, 1);
  
  $member_rank_str .= "<dt>$total_rank ：$option_name</<dt><dd> 得票数：$vote_sum 参与人数：$user_sum 总投票占比：$vote_ret %</dd>";
}
$member_rank_str .= "</dl>";

// 组合排行榜
$sql = "SELECT * FROM vote_rank WHERE pj_id = {$pj_id} AND option_type = 2 ORDER BY total_rank";
$db->query($sql);
$rows = $db->fetchAll();
$team_rank_str = "<dl class='dl-horizontal'>";

foreach ($rows as $row)
{
  $option_name = $row['option_name'];
  $total_rank = intval($row['total_rank']);
  $vote_sum = intval($row['vote_sum']);
  $user_sum = intval($row['user_sum']);
  $onboard = intval($row['onboard']);
  $vote_ret = round(($vote_sum * 100) / $total_vote_sum, 1);
  
  $team_rank_str .= "<dt>$total_rank ：$option_name</<dt><dd> 得票数：$vote_sum 参与人数：$user_sum 总投票占比：$vote_ret %</dd>";
}
$team_rank_str .= "</dl>";

$rtn_str = '';
$rtn_str .= <<<EOF
    
    $project_str
    
    <div id="stat_list">
      <ul class="nav nav-tabs">
        <li class="active"><a class="btn btn-info" href="#overview_tab" data-toggle="tab">总览</a></li>
        <li><a class="btn btn-info" href="#ticket_tab" data-toggle="tab">投票券</a></li>
        <li><a class="btn btn-info" href="#user_tab" data-toggle="tab">用户</a></li>
        <!--<li><a class="btn btn-info" href="#trend_tab" data-toggle="tab">投票趋势</a></li>-->
        <li><a class="btn btn-info" href="#song_rank_tab" data-toggle="tab">金曲榜</a></li>
        <li><a class="btn btn-info" href="#member_rank_tab" data-toggle="tab">成员榜</a></li>
        <li><a class="btn btn-info" href="#team_rank_tab" data-toggle="tab">组合榜</a></li>
      </ul>
      
      <div class="tab-content" style="padding-top: 10px; font-size:15px;">
        <div class="tab-pane active" id="overview_tab">
          $overview_str
        </div>
        
        <div class="tab-pane" id="ticket_tab">
          $ticket_str
        </div>
        
        <div class="tab-pane" id="user_tab">
          $user_str
        </div>
        
        <!--<div class="tab-pane" id="trend_tab">
        </div>-->
        
        <div class="tab-pane" id="song_rank_tab">
          $song_rank_str
        </div>
        
        <div class="tab-pane" id="member_rank_tab">
          $member_rank_str
        </div>

        <div class="tab-pane" id="team_rank_tab">
          $team_rank_str
        </div>
        
      </div>
      
    </div>
    
EOF;

// 输出内容
php_end($rtn_str);

?>
