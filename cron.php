<?php
$dir = dirname(__FILE__);
global $black, $red, $green, $yellow, $blue, $purple, $cyan, $white, $reset;

$black = "\033[0;30m";
$red = "\033[0;31m";
$green = "\033[0;32m";
$yellow = "\033[0;33m";
$blue = "\033[0;34m";
$purple = "\033[0;35m";
$cyan = "\033[0;36m";
$white = "\033[0;37m";
$reset = "\033[0m";

function randColor($f=31, $t=36){
    return "\033[0;".rand($f,$t)."m";
}
function prt($str){return randColor() . "$str\n";}
function input($text = '') {
    global $green, $yellow, $reset;
	//echo $text;
	echo "\n$green";
	echo "╔═══[root@linux]\n";
	echo "╚════> $yellow$text: $reset";
	$input = trim(fgets(STDIN));
	return $input;
}
function banner(){
   system("toilet -f big --filter border:metal 'PHP MaxCron'");
   echo "\n\n";
}

function clear(){ system('clear');}

// Main tool 
main:

if (!is_dir("$dir/log")) mkdir("$dir/log");
if (!file_exists("$dir/list.json")) file_put_contents( "$dir/list.json", "[]");

clear();
banner();
echo "$reset 1) " . prt("Chạy cron"); 
echo "$reset 2) " . prt("Danh sách cron"); 
echo "$reset 3) " . prt("Thêm cron"); 
echo "$reset 4) " . prt("Xóa cron"); 
echo "$reset 5) " . prt("Đặt lại tool");
echo "$reset 0) " . prt("Thoát");

$crons = @json_decode(@file_get_contents("$dir/list.json"));
$select = input("Lựa chọn");
switch($select){
    case "1":
        clear();
        banner();
        $time = 0;
        while(true){
            if ($time === time()) sleep(1);
            $time = time();
            foreach ($crons as $i => $cron){
                if ($cron->time + $cron->last <= $time && $cron->status) { 
                    echo "\n$yellow [" . date("d-m-Y H:i:s", $time) . "]$reset " . $cron->cmd;
                    if (!file_exists("$dir/log/" . $cron->log)) file_put_contents("$dir/log/" . $cron->log, "[]");
                    $logs = json_decode(file_get_contents("$dir/log/" . $cron->log));
                    ob_start();
                    exec($cron->cmd . " 2>&1", $output, $code);
                    ob_end_clean();
                    $crons[$i]->last = $time;
                    $logs[] = [
                        "time" => $time,
                        "status" => (!$code),
                        "code" => $code,
                        "output" => $output
                    ];
                    file_put_contents("$dir/log/" . $cron->log, json_encode($logs, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
                    file_put_contents( "$dir/list.json", json_encode($crons, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
                    echo "\n  ==> ";
                    echo (!$code) ? $green . "Thành công$reset" : $red . "Thất bại" . $reset . " (Mã lỗi: $code)\n";
                }
            }
        }
        break;
    case "2":
        clear();
        banner();
        foreach ($crons as $i => $cron){
            $i++; 
            echo "$reset ==> Lệnh " . prt($cron->cmd); 
            echo "$reset  => Trạng thái: " . prt(($cron->status)?"Hoạt động":"Không hoạt động"); 
            echo "$reset  => Thời gian nghỉ: " . prt($cron->time . " giây");
            echo "$reset  => Lần chạy cuối: " . prt(($cron->last === 0) ? "Chưa bao giờ" : date("d-m-Y H:i:s", $cron->last)); 
            echo "$reset  => Log: " . prt("$dir/log/" . $cron->log);
            echo "$reset\n";
        }
        input(randColor() . ("Ấn enter để tiếp tục"));
        break;
    case "3":
        clear();
        banner();
        $s = (int) input("Thời gian giữa các lần chạy (Giây)");
        $c = input("Lệnh");
        $log_file = "php_" . md5($s . $c . time() . rand(0,10e9)) . ".json";
        file_put_contents( "$dir/log/$log_file", "[]");
        $crons[] = [
            "time" => $s,
            "cmd" => $c,
            "last" => 0,
            "log" => $log_file,
            "status" => ($s > 0&& $c != '') ? true : false
        ];
        file_put_contents( "$dir/list.json", json_encode($crons, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)); 
        input(randColor() . ("Ấn enter để tiếp tục"));
        break; 
    case "4";
        clear();
        banner();
        foreach ($crons as $i => $cron){
            $i++; 
            echo "\n  $i) [" . $cron->time . " giây] " . $cron->cmd;
        }
        echo "\n";
        $rms = str_replace(" ", '', input("Nhập các id muốn xóa [1-$i] (Vd: 1,2)"));
        $rms = explode(",", $rms);
        foreach ( array_unique($rms)as $rm){
            $rm = (int) $rm;
            if($rm < 1|| $rm > $i ) continue;
            $rmi = $rm - 1;
            echo "\n$red  Đã xóa$reset [" . $crons[$rmi]->time . "] " . $crons[$rmi]->cmd;
            unlink("$dir/log/" . $crons[$rmi]->log);
            unset($crons[$rmi]);
        }
        file_put_contents( "$dir/list.json", json_encode( array_values($crons), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)); 
        echo "\n";
        input(randColor() . ("Ấn enter để tiếp tục"));
        break;
    case "5";
        file_put_contents( "$dir/list.json", "[]");
        array_map('unlink', glob("$dir/log/php_*.json"));
        break; 
    case "0";
        exit();
}
goto main;
echo "$reset\n";
