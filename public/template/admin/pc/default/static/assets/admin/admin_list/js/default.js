var tableIns=null;
layui.use('table', function(){
    var table = layui.table;
    var form = layui.form;

    //第一个实例
    tableIns = table.render({
        elem: '#table1'
        /* ,height: 700*/
        /*,width:TBW*/
        ,url:getAdminListUrl //数据接口
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
            ,{field: 'username', title: '用户名',width:150,event:'userInfo', sort: true,style:'color: #009688;cursor:pointer'}
            ,{field: 'realname', title: '实名', width:150}
            //,{field: 'nickname', title: '昵称', width:100}
            ,{field: 'phone', title: '手机号',width:150}
            //,{field: 'email', title: '邮箱',width:200}
            //,{field: 'role', minWidth:150, title: '角色'}
            ,{width:130, title: '登陆日志',width:130,align:'center',event:'loginLog',style:'color: #009688;cursor:pointer',templet:function () {
                    return '<span class="cursor-pointer color-green">查看</span>'
                }}
            ,{field: 'status', title: '账号状态', align:'center',width:120, sort: true,templet:'#statusTpl'}
            ,{fixed:'right', title: '操作',width:150, align:'center', toolbar: '#barDemo'}
        ]],
        id:'table1',
    });

    function renderData() {
        var username=$.trim($('input[name="username"]').val());
        var realname=$.trim($('input[name="realname"]').val());
        var phone=$.trim($('input[name="phone"]').val());
        var where={};
        if(username!=''){
            where.username=username;
        }
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
            case 'add':
                layer.open({
                    type:2,
                    title:'新增管理员',
                    content:addAdminPageUrl,
                    area:['600px','610px'],
                });
                break;
            case 'delete':
                var checkData = checkStatus.data;
                var arr=[];
                for (var i in checkData){
                    arr.push(checkData[i]['id']);
                }
                top.layer.confirm('真的删除选中的行么？', function(index){
                    top.layer.close(index);
                    var index2=layer.msg('正在删除...', {
                        icon: 16
                        ,shade: 0.01
                    });
                    $.ajax({
                        url:doDeleteAdminUrl,
                        data:{uid:arr},
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
                var index1=top.layer.confirm('真的删除行么', function(index){
                    top.layer.close(index);
                    var index2=layer.msg('正在删除...', {
                        icon: 16
                        ,shade: 0.01
                    });
                    $.ajax({
                        url:doDeleteAdminUrl,
                        data:{uid:data.id},
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
                layer.open({
                    type:2,
                    title:'正在修改 '+data.username+' 的信息',
                    area:['600px','610px'],
                    content:adminEditPageUrl+'?uid='+data.id,
                });
                break;
            case 'status':
                alert(1);
                break;
            case 'userInfo':
                $('#userInfoCtn').removeClass('hide');
                $('#userInfoCtn .cRealname').text(data.realname);
                $('#userInfoCtn .cUsername').text(data.username);
                $('#userInfoCtn .cNickname').text(data.nickname);
                $('#userInfoCtn .cPhone').text(data.phone);
                $('#userInfoCtn .cEmail').text(data.email);
                    break;
            case 'loginLog':
                parent.layer.open({
                    type:2,
                    title:data.username+'的登陆信息',
                    content:getAdminLoginLogUrl,
                    area:['600px','500px'],
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

    //用户禁用|启用
    form.on('switch(statusCheckbox)',function(obj){

        var status=obj.elem.checked ? 1 : 0;
        $.ajax({
            url:setAdminStatusUrl,
            data:{uid:$(obj.elem).data('id'),status:status},
            type:'get',
            dataType:'json',
            error:function (xhr) {
                layer.msg('操作失败！'+xhr.status,{icon:2});
            },
            success:function (data) {
                if (data.code!=0){
                    layer.tips('操作失败！',obj.othis,{tips:[4,'#000']});
                }else{
                    layer.tips("完成！",obj.othis,{tips:[4,'#000']});
                    $(obj.elem).val(status);
                }
            },
            complete:function () {
                //form.render('checkbox','statusCheckbox'); 渲染无效
            }
        });
        //
    });
});
var layer=layui.layer;
$(function () {

    $('#filterBtn').click(function (e) {
        $('#searchCard').stop().slideToggle();
        $(this).find('i').toggleClass('layui-icon-down').toggleClass('layui-icon-up');
    });

    /**
     * 关闭个人信息弹窗
     */
    $('#userInfoCtn .closeBtn').click(function () {
        $('#userInfoCtn').addClass('hide');
    });
});