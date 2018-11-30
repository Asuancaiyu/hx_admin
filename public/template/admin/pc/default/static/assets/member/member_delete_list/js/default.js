function   formatDate(now)   {
    var   now= new Date(now);
    var   year=now.getFullYear();
    var   month=now.getMonth()+1;
    var   date=now.getDate();
    var   hour=now.getHours();
    var   minute=now.getMinutes();
    var   second=now.getSeconds();
    return   year+"-"+fixZero(month,2)+"-"+fixZero(date,2)+" "+fixZero(hour,2)+":"+fixZero(minute,2)+":"+fixZero(second,2);
}
//时间如果为单位数补0
function fixZero(num,length){
    var str=""+num;
    var len=str.length;     var s="";
    for(var i=length;i-->len;){
        s+="0";
    }
    return s+str;
}

var tableIns=null;
layui.use('table', function(){
    var table = layui.table;
    var form = layui.form;

    //第一个实例
    tableIns = table.render({
        elem: '#table1'
        /* ,height: 700*/
        /*,width:TBW*/
        ,url:getMemberListUrl //数据接口
        ,where:{'is_delete':1}
        ,height: 'full-210'
        ,method: 'post'
        ,toolbar: '#dataTool'
        ,defaultToolbar:['filter','print','exports']
        ,cellMinWidth: 80 //全局定义常规单元格的最小宽度
        /*,even: true*/
        ,limit: 40
        ,limits: [20,40,60,80,100,200,300]
        ,page: true //开启分页
        ,cols: [[ //表头
            {fixed:'left', type: 'checkbox', title: '全选', width:60, sort: true}
            ,{field: 'id', title: '编号', width:80, sort: true, hide:true}
            ,{field: 'phone', title: '手机号',width:150}
            ,{field: 'email', title: '邮箱',width:200}
            ,{field: 'realname', title: '实名', width:150}
            ,{field: 'register_ip', title: '注册IP', align:'center', width:200}
            ,{field: 'register_time', title: '注册日期', align:'center', width:200, templet:function (d) {
                    return formatDate(d.register_time*1000);
                }}
            ,{field: 'delete_time', title: '删除日期', align:'center', width:200, templet:function (d) {
                    return formatDate(d.delete_time*1000);
                }}
            ,{fixed:'right', title: '操作',width:150, align:'center', toolbar: '#barDemo'}
        ]],
        id:'table1',
    });

    function renderData() {

        var realname=$.trim($('input[name="realname"]').val());
        var phone=$.trim($('input[name="phone"]').val());
        var where={};

        if(realname!=''){
            where.realname=realname;
        }
        if (phone!=''){
            where.phone=phone;
        }
        tableIns.reload({
            where: where //设定异步数据接口的额外参数
        });
        return false;
    }
    //筛选查询
    $('#subSearch').click(function () {
        renderData();
        return false;
    });

    //头工具栏事件
    table.on('toolbar(table1)', function(obj){
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var checkStatus = table.checkStatus(obj.config.id);
        //checkStatus.data;选中行的数据
        switch(layEvent){
            case 'delete':
                var checkData = checkStatus.data;
                var arr=[];
                for (var i in checkData){
                    arr.push(checkData[i]['id']);
                }
                top.layer.confirm('真的删除选中的用户吗？(注:不可恢复)',{title:'',closeBtn:0}, function(index){
                    top.layer.close(index);
                    var index2=layer.msg('正在删除...', {
                        icon: 16
                        ,shade: 0.01
                    });
                    $.ajax({
                        url:doDeleteMemberUrl,
                        data:{uid:arr,real:1},
                        type:'post',
                        dataType:'json',
                        error:function (xhr) {
                            layer.msg('超时'+xhr.status,{icon:2});
                        },
                        success:function (data) {
                            if(data.code==0){
                                layer.msg("完成",{icon:1});
                                tableIns.reload();
                            }else{
                                layer.msg(data.msg,{icon:2});
                            }
                        },
                        complete:function () {
                            layer.close(index2);
                        }
                    });

                });
                break;
        };
    });

    //监听工具条
    table.on('tool(table1)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data; //获得当前行数据
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var tr = obj.tr; //获得当前行 tr 的DOM对象

        switch(layEvent){
            case 'delete'://删除单列
                var index1=layer.confirm('真的删除该用户吗？(注:不可恢复)',{title:'',closeBtn:0}, function(index){
                    top.layer.close(index);
                    var index2=layer.msg('正在删除...', {
                        icon: 16
                        ,shade: 0.01
                    });
                    $.ajax({
                        url:doDeleteMemberUrl,
                        data:{uid:data.id,real:1},
                        type:'post',
                        dataType:'json',
                        error:function (xhr) {
                            layer.msg('超时'+xhr.status,{icon:2});
                        },
                        success:function (data) {
                            if(data.code==0){
                                layer.msg("完成",{icon:1});

                                tableIns.reload();
                            }else{
                                layer.msg(data.msg,{icon:2});
                            }
                        },
                        complete:function () {
                            layer.close(index2);
                        }
                    });

                });
                break;
            case 'restore': //编辑行
                var index1=layer.confirm('确认要恢复该用户吗？',{title:'',closeBtn:0}, function(index){
                    $.ajax({
                        url:restoreDeleteMemberURL,
                        data:{member_id:data.id},
                        type:'post',
                        dataType:'json',
                        error:function (xhr) {
                            layer.msg('超时'+xhr.status,{icon:2});
                        },
                        success:function (data) {
                            if(data.code==0){
                                layer.msg("完成",{icon:1});
                                obj.del(); //删除对应行（tr）的DOM结构，并更新缓存
                            }else{
                                layer.msg(data.msg,{icon:2});
                            }
                        },
                        complete:function () {
                            layer.close(index2);
                        }
                    });
                });
                break;

        }
    });
    //监听行单击事件（单击事件为：rowDouble）
    /*table.on('row(table1)', function(obj){
        var data = obj.data;

        layer.alert(JSON.stringify(data), {
            title: '当前行数据：'
        });

        //标注选中样式
        obj.tr.addClass('layui-table-click').siblings().removeClass('layui-table-click');
    });*/


});
var layer=layui.layer;
$(function () {

    $('#filterBtn').click(function (e) {
        $('#searchCard').stop().slideToggle();
        $(this).find('i').toggleClass('layui-icon-down').toggleClass('layui-icon-up');
    });

});