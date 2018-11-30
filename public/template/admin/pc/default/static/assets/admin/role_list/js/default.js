layui.use(['table','form','element'], function(){
    var table = layui.table;
    var element = layui.element;
    var form = layui.form;

    //第一个实例
    var tableIns = table.render({
        elem: '#table1'
        /* ,height: 700*/
        /*,width:TBW*/
        ,url:getListUrl //数据接口
        ,height: 'full-70'//210
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
            ,{field: 'role_name', title: '角色名',width:120, sort: true}
            ,{field: 'description', title: '说明'}
            /*,{width:130, title: '权限',width:130,align:'center',event:'loginLog',style:'color: #009688;cursor:pointer',templet:function () {
                    return '<span class="cursor-pointer color-green">查看</span>'
             }}*/
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
    });

    //头工具栏事件
    table.on('toolbar(table1)', function(obj){
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var checkStatus = table.checkStatus(obj.config.id);
        //checkStatus.data;选中行的数据
        switch(layEvent){
            case 'add':
                $('#roleAddWindow').fadeIn();
                break;
            case 'delete':
                var checkData = checkStatus.data;
                var arr=[];
                for (var i in checkData){
                    arr.push(checkData[i]['id']);
                }
                layer.confirm('真的删除选中的行么？', function(index){
                    layer.close(index);
                    var index2=layer.msg('正在删除...', {
                        icon: 16
                        ,shade: 0.01
                    });
                    $.ajax({
                        url:deleteApi,
                        data:{id:arr},
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
                    layer.close(index);
                    var index2=layer.msg('正在删除...', {
                        icon: 16
                        ,shade: 0.01
                    });
                    $.ajax({
                        url:deleteApi,
                        data:{id:data.id},
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
                    title:data.role_name+' 信息修改',
                    area:['525px','470px'],
                    content:editPageUrl+'?id='+data.id,
                });
                break;
        }
    });

//---------------------------------------------------------------------
//------------------------    添加角色    ------------------------------
//---------------------------------------------------------------------
    //权限树形图
    var setting = {
            view: {
                selectedMulti: false
            },
            check: {
                chkboxType : { "Y" : "s", "N" : "s" },
                enable: true
            },
            data: {
                simpleData: {
                    checked : "checked",
                    isParent : "isParent",
                    idKey: 'id',
                    pIdKey: 'pid',
                    enable: true
                }
            },
            callback: {
                onCheck: onCheck,
            }
        },
        $operationRightObj=null;
    $.ajax({
        url:getAuthList,
        type:'get',
        dataType:'json',
        error:function (xhr) {
            layer.msg(xhr.status,{icon:2});
        },
        success: function (data) {
            if (data.code==0){
                var zNodes =data.data;
                $operationRightObj=$.fn.zTree.init($("#OTree"), setting, zNodes);
            }else{
                layer.msg(data.msg,{icon:2});
            }
        }
    });
    function onCheck(event,treeId,treeNode) {
        var nodes = $operationRightObj.getCheckedNodes(true);
        //console.log(nodes);
    }
    //角色名是否重复
    $('#roleAddWindow').find('input[name="name"]').change(function () {
        $(this).val($.trim($(this).val()));
        var nameStr=$(this).val(),
            formLabel=$(this).closest('.layui-form-label');
        if (!nameStr){
            return false;
        }
        var data={
            name:nameStr
        };
        $.ajax({
            url:queryAuthNameApi,
            data:data,
            type:'get',
            dataType:'json',
            error:function (xhr) {
                layer.msg('添加失败'+xhr.status,{icon:2});
            },
            success:function (msg) {
                if (msg.code == 0){

                }else{
                    formLabel.find('.layui-form-mid').text('名称已存在');
                    layer.msg(msg.msg,{icon:2});
                }
            }
        });
    });
    //添加角色
    form.on('submit(addSubmit)', function(data){
        //console.log(data.elem) //被执行事件的元素DOM对象，一般为button对象
        //console.log(data.form) //被执行提交的form对象，一般在存在form标签时才会返回
        //console.log(data.field) //当前容器的全部表单字段，名值对形式：{name: value}
        var obj=$operationRightObj.getCheckedNodes(true),
            data=data.field,
            newObj=[];
        for (var i in obj){
            newObj.push(obj[i].id);
        }
        data.authOperation=newObj;
        $.ajax({
            url:addApi,
            data:data,
            type:'get',
            dataType:'json',
            error:function (xhr) {
                layer.msg('添加失败'+xhr.status,{icon:2});
            },
            success:function (msg) {
                if (msg.code == 0){
                    renderData();
                    layer.msg('完成',{icon:1});
                    $('#roleAddWindow').fadeOut();
                }else{
                    layer.msg(msg.msg,{icon:2});
                }
            }
        });
        return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
    });

});
var layer=layui.layer;
$(function () {

    $('#filterBtn').click(function (e) {
        $('#searchCard').stop().slideToggle();
        $(this).find('i').toggleClass('layui-icon-down').toggleClass('layui-icon-up');
    });
    
    $('#closeRoleWindow').click(function () {
        $('#roleAddWindow').fadeOut();
    });

    $('#rightWindowBtn ').click(function () {
        $(this).toggleClass('layui-layer-maxmin');
        $('#roleRightWindow').toggleClass('roleRightWindowMax');
    });
});