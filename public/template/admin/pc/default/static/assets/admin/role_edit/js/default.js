layui.use(['form','layer','element'],function(){
    var form = layui.form,
        layer = layui.layer,
        element = layui.element;
    
    //初始化
    function ini() {
        getRoleBaseInfo();
        getAuthList();
    }
    ini();

    //获取角色基本信息
    function getRoleBaseInfo() {
        $.ajax({
            url:getRoleBaseInfoApi,
            data: {id:id},
            type:'get',
            dataType:'json',
            error:function (xhr) {
                layer.msg(xhr.status,{icon:2});
            },
            success: function (res) {
                if (res.code==0){
                    var data =res.data;
                    $('input[name="name"]').val(data.role_name);
                    $('textarea[name="desc"]').val(data.description);
                }else{
                    layer.msg(res.msg,{icon:2});
                }
            }
        });
    }
    
    //获取操作权限
    var $operationRightObj=null;
    function getAuthList() {
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

                        isParent : "is_parent",
                        idKey: 'id',
                        pIdKey: 'pid',
                        enable: true
                    },
                    key: {
                        url: "xUrl"
                    }
                },
                callback: {
                    onCheck: onCheck,
                }
            };
        $.ajax({
            url:getAuthListApi,
            data: {id:id,merge:1},
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
    }

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
    form.on('submit(saveSubmit)', function(data){
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
        data.id=id;
        $.ajax({
            url:saveRoleInfoApi,
            data:data,
            type:'get',
            dataType:'json',
            error:function (xhr) {
                layer.msg('添加失败'+xhr.status,{icon:2});
            },
            success:function (msg) {
                if (msg.code == 0){
                    parent.layer.msg('完成',{icon:1});
                    parent.layer.close(parent.layer.getFrameIndex(window.name));
                }else{
                    layer.msg(msg.msg,{icon:2});
                }
            }
        });
        return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
    });

    //选择权限的容器放大
    $('#rightWindowBtn ').click(function () {
        $(this).toggleClass('layui-layer-maxmin');
        $('#roleRightWindow').toggleClass('roleRightWindowMax');
    });
});