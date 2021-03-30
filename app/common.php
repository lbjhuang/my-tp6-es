<?php
// 应用公共文件
/* * 组装ES请求参数   Lamb  2020/03/28
 * @param $params    @组装参数本身
 * @param $item      @搜索字段
 * @param $condition @条件关键字：must[必须包含],must_not[必须不包含],filter[过滤],should[或者or]等
 * @param $model     @搜索匹配模式：match[分词匹配],match_phrase[不分词匹配，短语匹配],match_phrase_prefix[开头匹配],regexp[正则表达式匹配],range[范围from,to],term[等于=],terms[包含in]等
 * @param $value     @搜索值
 * @return mixed     @返回组装参数的引用传递
 */
function makeESParams(&$params, $item, $condition, $model, $value) {
    $params[$item]['item'] = $item;
    $params[$item]['value'] = $value;
    $params[$item]['condition'] = $condition;
    $params[$item]['model'] = $model;
    return $params;
}


function curl($url, $send_data, $method = 'get', $is_json=false) {
    if ($method == 'get' && !empty($send_data)) {
        $url .= '?' . http_build_query($send_data, '&');
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    if ($method == 'post') {
        $header = array("Content-Type:application/json;charset=UTF-8");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if($is_json){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $send_data);
        }else{
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($send_data, JSON_UNESCAPED_UNICODE));
        }
        curl_setopt($ch, CURLOPT_POST, 1);
    }
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 200);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function pr($data, $is_stop = true) {
    if (is_array($data)) {
        echo '<pre style="background:#DEE1E6">';
        print_R($data);
    } else {
        echo $data;
    }
    if ($is_stop)
        die();
}