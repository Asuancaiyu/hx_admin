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
    var formSelects = layui.formSelects;

    //第一个实例
    tableIns = table.render({
        elem: '#table1'
        /* ,height: 700*/
        /*,width:TBW*/
        ,url:getMemberGroupListUrl //数据接口
        ,height: 'full-70'
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
            ,{field: 'group_name', title: '组名',width:150}
            ,{field: 'description', title: '组描述',width:200}
            ,{fixed:'right', title: '操作',width:150, align:'center', toolbar: '#barDemo'}
        ]],
        id:'table1',
    });

    function renderData() {
        //var group_name=$.trim($('input[name="group_name"]').val());
        var where={};
        /*if(username!=''){
            where.group_name=group_name;
        }*/
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

    var openIndex=null;
    function iniAddEditDataCtn(type,oldData) {
        var ctn=$('#addEditData'),
            data={};
        ctn.find('input').val('');
        ctn.find('textarea').val('');
        if (type=='add'){
            ctn.removeClass('edit-data');
            ctn.addClass('add-data');
            var url=gaddMemberGroupUrl;
        }else if(type == 'edit'){
            ctn.removeClass('add-data');
            ctn.addClass('edit-data');
            var url=saveMemberGroupUrl;
            data.group_id=oldData.id;
        }

        ctn.find('#dataSubmit').unbind('click').on('click',function () {
            var groupName=ctn.find('input').val();
            var description=ctn.find('textarea').val();
            data.group_name=groupName;
            data.description=description;
            $.ajax({
                url: url,
                data: data,
                type:'post',
                dataType:'json',
                error:function (xhr) {
                    layer.msg(xhr.status,{icon:2});
                },
                success:function (res) {
                    if (res.code==0){
                        renderData();
                        layer.msg('完成',{icon:1});
                        layer.close(openIndex);
                    }else{
                        layer.msg(res.msg,{icon:2});
                    }
                }
            });
        });
        if (typeof oldData == 'undefined'){
            return false;
        }
        ctn.find('input[name="group_name"]').val(oldData.group_name);
        ctn.find('textarea[name="description"]').val(oldData.description);
    }
    
    //头工具栏事件
    table.on('toolbar(table1)', function(obj){
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var checkStatus = table.checkStatus(obj.config.id);
        //checkStatus.data;选中行的数据
        switch(layEvent){
            case 'add':
                iniAddEditDataCtn('add');
                openIndex=layer.open({
                    type:1,
                    title:'',
                    area:['400px','255px'],
                    content:$('#addEditData'),
                });
                break;
            case 'delete':
                var checkData = checkStatus.data;
                var arr=[];
                for (var i in checkData){
                    arr.push(checkData[i]['id']);
                }
                layer.confirm('真的删除选中的行么？', function(index){
                    top.layer.close(index);
                    var index2=layer.msg('正在删除...', {
                        icon: 16
                        ,shade: 0.01
                    });
                    $.ajax({
                        url:deleteMemberGroupUrl,
                        data:{group_id:arr},
                        type:'post',
                        dataType:'json',
                        error:function (xhr) {
                            layer.msg('超时'+xhr.status,{icon:2});
                        },
                        success:function (data) {
                            if(data.code==0){
                                layer.msg("完成",{icon:1});
                                renderData();
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
            case 'del'://删除单列
                var index1=layer.confirm('真的删除行么', function(index){
                    top.layer.close(index);
                    var index2=layer.msg('正在删除...', {
                        icon: 16
                        ,shade: 0.01
                    });
                    $.ajax({
                        url:deleteMemberGroupUrl,
                        data:{group_id:data.id},
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
            case 'edit': //编辑行
                iniAddEditDataCtn('edit',data);
                openIndex=layer.open({
                    type:1,
                    title:'修改 '+data.group_name+' 组信息',
                    area:['400px','295px'],
                    content:$('#addEditData'),
                });
                break;

        }
    });

});
var layer=layui.layer;
