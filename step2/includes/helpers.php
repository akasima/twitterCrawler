<?php

function shortTime($date)
{
    global $curTime, $oneDayTime, $oneHourTime;
    $dst = strtotime($date);
    $time = $curTime - $dst;

    if ($time < 60) {
        return '지금 막';
    } elseif ($time < $oneHourTime) {
        return sprintf('%s분 전', floor($time/60));
    } elseif ($time < $oneHourTime*6) {
        return sprintf('%s시간 전', floor($time/$oneHourTime));
    } elseif ($time < $oneDayTime) {
        return date('H:i', $dst);
    } elseif ($time < $oneDayTime*2) {
        return '어제';
    } elseif ($time < $oneDayTime*3) {
        return '그제';
    } elseif ($time < $oneDayTime*15) {
        return sprintf('%s일 전', floor($time/$oneDayTime));
    } elseif ($time < $oneDayTime*365) {
        return date('n월 j일', $dst);
    } else {
        return date('Y년 j월', $dst);
    }
}

spl_autoload_register(function($class) {
    include sprintf('%s/classes/%s.php', __DIR__, $class);
});