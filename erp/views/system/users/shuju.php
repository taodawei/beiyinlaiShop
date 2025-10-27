<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$allusers = $db->get_var("select count(*) from users where comId=$comId");
$today = $db->get_var("select count(*) from users where comId=$comId and dtTime>='".date("Y-m-d 00:00:00")."'");
$areas = $db->get_results("select count(*) as num,province from users where comId=$comId group by province");
$types = $db->get_results("select count(*) as num,type from users where comId=$comId group by type");
$str = '';
if(!empty($areas)){
    foreach ($areas as $a) {
        $title = $db->get_var("select title from demo_area where id=$a->province");
        if(empty($title))$title='其他';
        $title = str_replace('省','',$title);
        $str.=',{name:"'.$title.'",value:'.$a->num.'},';
    }
}
if(!empty($str))$str=substr($str,1);
$levels = $db->get_results("select count(*) as num,level from users where comId=$comId group by level");
$levelstr = '';
if(!empty($levels)){
    foreach ($levels as $l){
        $level = $db->get_var("select title from user_level where id=$l->level");
        if(empty($level))$level='无';
        $levelstr .= ",{value:".$l->num.",name:'".$level."（".$l->num."人）'}";
    }
}
if(!empty($levelstr))$levelstr=substr($levelstr,1);
$sexs = $db->get_results("select count(*) as num,sex from users where comId=$comId group by sex");
$sexstr = '';
if(!empty($sexs)){
    foreach ($sexs as $l){
        $sex = '未知';
        if($l->sex==1){
            $sex = '男';
        }else{
            $sex = '女';
        }
        $sexstr .= ",{value:".$l->num.",name:'".$sex."（".$l->num."人）'}";
    }
}
if(!empty($sexstr))$sexstr=substr($sexstr,1);
$typestr = '';
if(!empty($types)){
    foreach ($types as $l){
        if($l->type==1){
            $sex = '普通';
        }else{
            $sex = '其他';
        }
        $typestr .= ",{value:".$l->num.",name:'".$sex."（".$l->num."人）'}";
    }
}
if(!empty($typestr))$typestr=substr($typestr,1);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit" />
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/echarts.min.js"></script>
    <script type="text/javascript" src="js/users/china.js"></script>
    <script type="text/javascript" src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <style>
        .layui-table-body tr{height:50px}
        .layui-table-view{margin:10px;}
    </style>
</head>
<body>
    <div class="mendianguanli">
        <div class="mendianguanli_up">
            <img src="images/biao_24.png"/> 会员数据
        </div>
        <div class="mendianguanli_down">
            <div class="huiyuanshuju">
                <div class="huiyuanshuju_1">
                    <ul>
                        <li class="huiyuanshuju_1_line">
                            <h2><?=$allusers?></h2>门店会员
                        </li>
                        <li class="huiyuanshuju_1_line">
                            <h2><?=$today?></h2>今日新增会员
                        </li>
                        <li>
                            <h2>0</h2>领过优惠
                        </li>
                        <div class="clearBoth"></div>
                    </ul>
                </div>
                <div class="huiyuanshuju_2">
                    <div class="huiyuanshuju_2_01 huiyuanshuju_2_01_right">
                        <div id="types" style="height:350px;"></div>
                    </div>
                    <div class="huiyuanshuju_2_01 huiyuanshuju_2_01_right">
                        <div id="levels" style="height:350px;"></div>
                    </div>
                    <div class="huiyuanshuju_2_01">
                        <div id="sexs" style="height:350px;"></div>
                    </div>
                    <div class="clearBoth"></div>
                </div>
                <div class="huiyuanshuju_3">
                    <div class="huiyuanshuju_3_up">
                        会员区域分布数据
                    </div>
                    <div class="huiyuanshuju_3_down">
                        <div id="china-map" style="width:1000px;margin:0px auto;height:800px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var levels = echarts.init(document.getElementById('levels'));
        var option = {
            title: {
                text: '会员等级数据',
                left:'130px'
            },
            backgroundColor: '#fff',
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b}: {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                x: 'right',
                data:[<?=$levelstr?>]
            },
            series: [
            {
                name:'人数',
                type:'pie',
                radius: ['50%', '70%'],
                avoidLabelOverlap: false,
                label: {
                    normal: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        show: true,
                        textStyle: {
                            fontSize: '30',
                            fontWeight: 'bold'
                        }
                    }
                },
                labelLine: {
                    normal: {
                        show: false
                    }
                },
                data:[<?=$levelstr?>]
            }
            ]
        };
        levels.setOption(option);
        var sexs = echarts.init(document.getElementById('sexs'));
        var option = {
            title: {
                text: '会员性别数据',
                left:'130px'
            },
            backgroundColor: '#fff',
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b}: {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                x: 'right',
                data:[<?=$sexstr?>]
            },
            series: [
            {
                name:'人数',
                type:'pie',
                radius: ['50%', '70%'],
                avoidLabelOverlap: false,
                label: {
                    normal: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        show: true,
                        textStyle: {
                            fontSize: '30',
                            fontWeight: 'bold'
                        }
                    }
                },
                labelLine: {
                    normal: {
                        show: false
                    }
                },
                data:[<?=$sexstr?>]
            }
            ]
        };
        sexs.setOption(option);
        var types = echarts.init(document.getElementById('types'));
        var option = {
            title: {
                text: '会员类型数据',
                left:'130px'
            },
            backgroundColor: '#fff',
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b}: {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                x: 'right',
                data:[<?=$typestr?>]
            },
            series: [
            {
                name:'人数',
                type:'pie',
                radius: ['50%', '70%'],
                avoidLabelOverlap: false,
                label: {
                    normal: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        show: true,
                        textStyle: {
                            fontSize: '30',
                            fontWeight: 'bold'
                        }
                    }
                },
                labelLine: {
                    normal: {
                        show: false
                    }
                },
                data:[<?=$typestr?>]
            }
            ]
        };
        types.setOption(option);
        var myChart = echarts.init(document.getElementById('china-map'));
        var option = {
            title : {
                text: '会员分布',
                subtext: '各省份会员量汇总',
                x:'center'
            },
        tooltip : {//提示框组件。
            trigger: 'item'//数据项图形触发，主要在散点图，饼图等无类目轴的图表中使用。
        },
        legend: {
            orient: 'horizontal',//图例的排列方向
            x:'left',//图例的位置
            data:['会员分布']
        },

        visualMap: {//颜色的设置  dataRange
            x: 'left',
            y: 'center',
            splitList: [
            {start: 1500, color:'#006EDD'},
            {start: 900, end: 1500, color:'#236eDD'},
            {start: 310, end: 1000, color:'#4B9EE8'},
            {start: 200, end: 300, color:'#70B7EE'},
            {start: 10, end: 200, color:'#95CFF4'},
            {end: 10,color:'#BBE7F9'}
            ],
//            min: 0,
//            max: 2500,
//            calculable : true,//颜色呈条状
            text:['高','低'],// 文本，默认为数值文本
            color: ['#E0022B', '#E09107', '#A3E00B']
        },
        toolbox: {//工具栏
            show: true,
            orient : 'vertical',//工具栏 icon 的布局朝向
            x: 'right',
            y: 'center',
            feature : {//各工具配置项。
                mark : {show: true},
                dataView : {show: true, readOnly: false},//数据视图工具，可以展现当前图表所用的数据，编辑后可以动态更新。
                restore : {show: true},//配置项还原。
                saveAsImage : {show: true}//保存为图片。
            }
        },
        roamController: {//控制地图的上下左右放大缩小 图上没有显示
            show: true,
            x: 'right',
            mapTypeControl: {
                'china': true
            }
        },
        series : [
        {
            name: '会员数量',
            type: 'map',
            mapType: 'china',
                roam: false,//是否开启鼠标缩放和平移漫游
                itemStyle:{//地图区域的多边形 图形样式
                    normal:{//是图形在默认状态下的样式
                        label:{
                            show:true,//是否显示标签
                            textStyle: {
                                color: "rgb(249, 249, 249)"
                            }
                        }
                    },
                    emphasis:{//是图形在高亮状态下的样式,比如在鼠标悬浮或者图例联动高亮时
                        label:{show:true}
                    }
                },
                top:"3%",//组件距离容器的距离
                data:[<?=$str?>]
            }
            ]
        };
        myChart.setOption(option);
        myChart.on('mouseover', function (params) {
            var dataIndex = params.dataIndex;
        });
    </script>
    <? require('views/help.html');?>
</body>
</html>