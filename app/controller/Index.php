<?php
namespace app\controller;

use app\BaseController;
use elasticSearch\MyElasticSearch;
use think\facade\Db;


class Index extends BaseController
{
    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V' . \think\facade\App::version() . '<br/><span style="font-size:30px;">14载初心不改 - 你值得信赖的PHP框架</span></p><span style="font-size:25px;">[ V6.0 版本由 <a href="https://www.yisu.com/" target="yisu">亿速云</a> 独家赞助发布哟 ]</span></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="ee9b1aa918103c4fc"></think>';
    }

    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }

    public function hi($name = 'ThinkPHP6')
    {
        return 'hi,' . $name;
    }


    public function esBatchUpset(){
        $my_es = new MyElasticSearch();
        $index = 'tea_goods';
//1.1 批量或单个添加更新文档
        $data[1]['id'] = 1;
        $data[1]['goods_name'] = '凤凰单枞鸭屎香茶叶大瓶装';
        $data[1]['goods_no'] = 'B6-10098';
        $data[1]['goods_id'] = 1;
        $data[1]['price'] = 210.5;
        $data[1]['create_time'] = time();

        $data[2]['id'] = 2;
        $data[2]['goods_name'] = '忆江南碧螺春铁盒装';
        $data[2]['goods_no'] = 'B2-01812';
        $data[2]['goods_id'] = 2;
        $data[2]['price'] = 527.8;
        $data[2]['create_time'] = time();

        $data[3]['id'] = 3;
        $data[3]['goods_name'] = '凤凰单枞鸭屎香茶叶';
        $data[3]['goods_no'] = 'B5-01806';
        $data[3]['goods_id'] = 3;
        $data[3]['price'] = 217.6;
        $data[3]['create_time'] = time();

        $data[4]['id'] = 4;
        $data[4]['goods_name'] = '信阳毛尖，灌装280g';
        $data[4]['goods_no'] = 'A9-02012';
        $data[4]['goods_id'] = 4;
        $data[4]['price'] = 110.8;
        $data[4]['create_time'] = time();

        $data[5]['id'] = 5;
        $data[5]['goods_name'] = '忆江南雨前龙井500g，礼盒装';
        $data[5]['goods_no'] = 'D6-02018';
        $data[5]['goods_id'] = 5;
        $data[5]['price'] = 110.8;
        $data[5]['create_time'] = time();

        $data[6]['id'] = 6;
        $data[6]['goods_name'] = '西湖碧螺春，大包袋装1000g，顺丰包邮';
        $data[6]['goods_no'] = 'A9-12028';
        $data[6]['goods_id'] = 6;
        $data[6]['price'] = 110.8;
        $data[6]['create_time'] = time();

        $res = $my_es->bulkAddOrUpdate($index, $data, '插入成功');
        die;

//1.2 搜索
//条件搜
        $goods_ids = [1091, 1092, 1098, 1099];
        $params = makeESParams($params, 'goods_id', 'filter', 'terms', $goods_ids);
    }


    public function makeIndex(){
        //$setting_and_mapping['settings'] = ['number_of_shards' => 3, 'number_of_replicas' => 1];
        $setting_and_mapping['settings'] = ['number_of_shards' => 1, 'number_of_replicas' => 1];
        $setting_and_mapping['mappings']['properties'] = [
            'id' => ['type' => 'long'],
            'goods_id' => ['type' => 'long'],
            'goods_no' => ['type' => 'keyword'],
            "goods_name"=> [                      //需要分词的字段这样写
                'analyzer'=> "ik_max_word",       //ik_smart粗粒度分词  ik_max_word精细分词
                'type'=> "text",
                'fields'=> [
                    'keyword'=> [
                        "type"=> "keyword",
                        "ignore_above"=> 256
                    ]
                ]
            ],
            'create_time' => ['type' => 'long'],
        ];
        $my_es = new MyElasticSearch();
        $index = 'tea_goods';
        //这个方法会报 索引不存在错误，待修复，不过建议用kibana 命令行那里去新建。。。
        $res = $my_es->createIndex($index, $setting_and_mapping);
        pr($res);

    }

    //1.2 搜索
    public function getTea(){
        $my_es = new MyElasticSearch();
        $index = 'tea_goods';
        //分词搜，高亮，排序，分页，筛选字段等
        //$search_item = "goods_name";
        //$search_word = "忆江南雨前龙井";
        //$sort = [['sort_item' => 'price', 'sort_rule' => 'desc'],['sort_item' => 'id', 'sort_rule' => 'desc']];
        //$params = makeESParams($params, $search_item, 'must', 'match', trim($search_word));
        //$res = $my_es->search($index, $params, '0', '20', 'goods_name', ['goods_name','goods_id'], $sort);
        //pr($res);

        //条件搜，排序，分页，筛选字段等
        $goods_ids = [1, 2, 3, 4,5,6,7,8];
        $params = makeESParams($params, 'goods_id', 'filter', 'terms', $goods_ids);
        $sort = [['sort_item' => 'price', 'sort_rule' => 'desc'],['sort_item' => 'id', 'sort_rule' => 'desc']];
        $res = $my_es->search($index, $params, '0', '20', '', ['goods_name','goods_id'], $sort);
        pr($res);
    }
}
