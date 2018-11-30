var form=layui.form;
var layer=layui.layer;
$(function () {
    var app=$('#LAY_app'),k='layadmin-side-shrink',s='layadmin-side-spread-sm';


    var config={
        theme:$('body').attr('theme'),
    };
    ini();
    //初始化
    function ini() {
       // themeStyle(config.theme);
    }

    //显示/缩小侧边栏
    $('#LAY_app_flexible').click(function () {
        if ($(window).width()<992){
            app.toggleClass(s);
            app.removeClass(k);
        }else {
            //菜单栏只显示图标
            app.toggleClass(k);
        }
        $(this).toggleClass('layui-icon-shrink-right');
        $(this).toggleClass('layui-icon-spread-left');
    });
    //遮罩层的事件
    app.find('.layadmin-body-shade').click(function () {
        app.removeClass(s);
        app.removeClass(k);
    });

    //刷新页面
    $('#refresh').click(function () {
        appBody.find('div.layui-show iframe').attr('src',appBody.find('div.layui-show iframe').attr('src'));
    });

    //浏览器窗口变化监控
    $(window).resize(function () {
        if($(this).width()>=992){
            app.removeClass(s);
        }else{
            app.removeClass(k);
        }
    });



    function themeTogger() {

        $('#themeArr').css({
            right:0,
            top:'25.5px',
        });
    }
    var time=0;
    function bgShade(s) {
        var shade=$('.layui-layer-shade');
        if(s){
            time++;
            if(shade.length<=0){
                $('body').append('<div class="layui-layer-shade" id="layui-layer-shade'+time+'" time="'+time+'" style="z-index: 19891020; background-color: rgb(0, 0, 0); opacity: 0.1;"></div>');
            }else{
                $('#layui-layer-shade'+time).show();
            }
        }else{
            $('#layui-layer-shade'+time).remove();
        }
    }
    //遮罩层事件
    $('body').on('click','.layui-layer-shade',function () {
        var time=$(this).attr('time');
        $('div.admin-model'+time).animate({
            right:'-100%',
            opacity:0,
        },300).removeClass('admin-model'+time);
        bgShade(false);
    });

    //主题设置栏
    $('#theme').click(function () {
        bgShade(true);
        if(!$('#themeArr')[0].hasAttribute('show')){
            $('#themeArr').attr('show',true);
        }else{
            $('#themeArr').attr('show',true);
        }
        $('#themeArr').addClass('admin-model'+time);
        $('#themeArr').attr('time',time);
        $('#themeArr').stop().animate({
            right:0,
            top:'25.5px',
            opacity:1,
        },300);

        $('.setTheme').each(function (e) {
            if($(this).data('alias')==config.theme){
                $(this).addClass('layui-this');
                return;
            }
        });
        $('#themeArr').show();
    });

    function themeStyle(name) {
        var str='';
        switch (name){
            case 'blue':
                str="  .layui-side-menu, .layadmin-pagetabs .layui-tab-title li:after, .layadmin-pagetabs .layui-tab-title li.layui-this:after, .layui-layer-admin .layui-layer-title, .layadmin-side-shrink .layui-side-menu .layui-nav > .layui-nav-item > .layui-nav-child {\n" +
                    "        background-color: #03152A !important;\n" +
                    "    }\n" +
                    "\n" +
                    "    .layui-nav-tree .layui-this, .layui-nav-tree .layui-this > a, .layui-nav-tree .layui-nav-child dd.layui-this, .layui-nav-tree .layui-nav-child dd.layui-this a {\n" +
                    "        background-color: #3B91FF !important;\n" +
                    "    }\n" +
                    "\n" +
                    "    .layui-layout-admin .layui-logo {\n" +
                    "        background-color: #03152A !important;\n" +
                    "    }";
                break;
            case 'coffee':
                str = '.layui-side-menu,.layadmin-pagetabs .layui-tab-title li:after,.layadmin-pagetabs .layui-tab-title li.layui-this:after,.layui-layer-admin .layui-layer-title,.layadmin-side-shrink .layui-side-menu .layui-nav>.layui-nav-item>.layui-nav-child{background-color:#2E241B !important;}.layui-nav-tree .layui-this,.layui-nav-tree .layui-this>a,.layui-nav-tree .layui-nav-child dd.layui-this,.layui-nav-tree .layui-nav-child dd.layui-this a{background-color:#A48566 !important;}.layui-layout-admin .layui-logo{background-color:#2E241B !important;}';
                break;
            case 'purple':
                str='.layui-side-menu,.layadmin-pagetabs .layui-tab-title li:after,.layadmin-pagetabs .layui-tab-title li.layui-this:after,.layui-layer-admin .layui-layer-title,.layadmin-side-shrink .layui-side-menu .layui-nav>.layui-nav-item>.layui-nav-child{background-color:#50314F !important;}.layui-nav-tree .layui-this,.layui-nav-tree .layui-this>a,.layui-nav-tree .layui-nav-child dd.layui-this,.layui-nav-tree .layui-nav-child dd.layui-this a{background-color:#7A4D7B !important;}.layui-layout-admin .layui-logo{background-color:#50314F !important;}';
                break;
            case 'ocean':
                str='.layui-side-menu,.layadmin-pagetabs .layui-tab-title li:after,.layadmin-pagetabs .layui-tab-title li.layui-this:after,.layui-layer-admin .layui-layer-title,.layadmin-side-shrink .layui-side-menu .layui-nav>.layui-nav-item>.layui-nav-child{background-color:#344058 !important;}.layui-nav-tree .layui-this,.layui-nav-tree .layui-this>a,.layui-nav-tree .layui-nav-child dd.layui-this,.layui-nav-tree .layui-nav-child dd.layui-this a{background-color:#1E9FFF !important;}.layui-layout-admin .layui-logo{background-color:#1E9FFF !important;}';
                break;
            case 'green':
                str='.layui-side-menu,.layadmin-pagetabs .layui-tab-title li:after,.layadmin-pagetabs .layui-tab-title li.layui-this:after,.layui-layer-admin .layui-layer-title,.layadmin-side-shrink .layui-side-menu .layui-nav>.layui-nav-item>.layui-nav-child{background-color:#3A3D49 !important;}.layui-nav-tree .layui-this,.layui-nav-tree .layui-this>a,.layui-nav-tree .layui-nav-child dd.layui-this,.layui-nav-tree .layui-nav-child dd.layui-this a{background-color:#5FB878 !important;}.layui-layout-admin .layui-logo{background-color:#2F9688 !important;}';
                break;
            case 'yellow':
                str='.layui-side-menu,.layadmin-pagetabs .layui-tab-title li:after,.layadmin-pagetabs .layui-tab-title li.layui-this:after,.layui-layer-admin .layui-layer-title,.layadmin-side-shrink .layui-side-menu .layui-nav>.layui-nav-item>.layui-nav-child{background-color:#20222A !important;}.layui-nav-tree .layui-this,.layui-nav-tree .layui-this>a,.layui-nav-tree .layui-nav-child dd.layui-this,.layui-nav-tree .layui-nav-child dd.layui-this a{background-color:#F78400 !important;}.layui-layout-admin .layui-logo{background-color:#F78400 !important;}';
                break;
            case 'red':
                str='.layui-side-menu,.layadmin-pagetabs .layui-tab-title li:after,.layadmin-pagetabs .layui-tab-title li.layui-this:after,.layui-layer-admin .layui-layer-title,.layadmin-side-shrink .layui-side-menu .layui-nav>.layui-nav-item>.layui-nav-child{background-color:#28333E !important;}.layui-nav-tree .layui-this,.layui-nav-tree .layui-this>a,.layui-nav-tree .layui-nav-child dd.layui-this,.layui-nav-tree .layui-nav-child dd.layui-this a{background-color:#AA3130 !important;}.layui-layout-admin .layui-logo{background-color:#AA3130 !important;}';
                break;
            case 'black':
                str='.layui-side-menu,.layadmin-pagetabs .layui-tab-title li:after,.layadmin-pagetabs .layui-tab-title li.layui-this:after,.layui-layer-admin .layui-layer-title,.layadmin-side-shrink .layui-side-menu .layui-nav>.layui-nav-item>.layui-nav-child{background-color:#24262F !important;}.layui-nav-tree .layui-this,.layui-nav-tree .layui-this>a,.layui-nav-tree .layui-nav-child dd.layui-this,.layui-nav-tree .layui-nav-child dd.layui-this a{background-color:#009688 !important;}.layui-layout-admin .layui-logo{background-color:#3A3D49 !important;}';
                break;
            case 'default':
                str='.layui-side-menu, .layadmin-pagetabs .layui-tab-title li:after, .layadmin-pagetabs .layui-tab-title li.layui-this:after, .layui-layer-admin .layui-layer-title, .layadmin-side-shrink .layui-side-menu .layui-nav > .layui-nav-item > .layui-nav-child {background-color: #20222a !important;}  .layui-nav-tree .layui-this, .layui-nav-tree .layui-this > a, .layui-nav-tree .layui-nav-child dd.layui-this, .layui-nav-tree .layui-nav-child dd.layui-this a {background: #009688 !important;} .layui-layout-admin .layui-logo {background:0 0 !important;}';
                break;

        }

        if($('body').find('#admin_theme').length>0){
            $('body').find('#admin_theme').text(str);
        }else{
            $('body').append('<style id="admin_theme">'+str+'</style>');
        }

    }
    $('.setTheme').click(function () {
        var a=$(this).data('alias');
        if(!$(this).hasClass('layui-this')){
            $('.setTheme.layui-this').removeClass('layui-this');
            $(this).addClass('layui-this');
            themeStyle(a)
        }
    });



    //版权
    $('.about').click(function () {
        var copyright=$('#copyright');
        bgShade(true);
        if(!copyright[0].hasAttribute('show')){
            copyright.attr('show',true);
        }else{
            copyright.attr('show',true);
        }
        copyright.addClass('admin-model'+time);
        copyright.attr('time',time);
        copyright.stop().animate({
            right:0,
            top:'25.5px',
            opacity:1,
        },300);

        copyright.show();
    });

    //页面标签
    var appBody=$('#LAY_app_body');
    var appTabs=$('#LAY_app_tabs');
    var appTabsHeader=$('#LAY_app_tabsheader');
    appTabsHeader.find('li').click(function (e) {
        var i=$(this).index();
        var href=$(this).data('href');
        if(!$(this).hasClass('layui-this')){
            appBody.find('.layadmin-tabsbody-item').removeClass('layui-show');
            appBody.find('.layadmin-tabsbody-item').each(function (i) {
                if($(this).find('iframe').attr('src')==href){
                    exist=true;
                    appBody.find('.layadmin-tabsbody-item').removeClass('layui-show');
                    $(this).addClass('layui-show');
                }
            })
        }
    });
    //删除标签
    appTabsHeader.find('li i.layui-tab-close').click(function (e) {
        var li=$(this).parent();
        var href=li.data('href');
        if(li.index()<=0){
            return false;
        }
        if(li.hasClass('layui-this')){
            appTabsHeader.find('li:eq(0)').addClass('layui-this');
            appBody.find('.layadmin-tabsbody-item:eq(0)').addClass('layui-show');
        }
        appBody.find('.layadmin-tabsbody-item').each(function (i) {
            if($(this).find('iframe').attr('src')==href){
                $(this).remove();
                return;
            }
        })
        li.remove();
        return false;
    });

    //菜单
    var menu=$(document).find('#LAY-system-side-menu');

    menu.find('.menu-item').click(function () {
       var _this=$(this);
       var href=_this.data('href');
       var exist=false;
        appTabsHeader.find('li').each(function (i) {
            if($(this).data('href')==href){
                appTabsHeader.find('li').removeClass('layui-this');
                $(this).addClass('layui-this');
                exist=true;
                return ;
            }
        });
        //标签存在的情况下
        if(exist){
            //appBody.find('.layadmin-tabsbody-item').removeClass('layui-show');
            appBody.find('.layadmin-tabsbody-item').each(function (i) {
                if($(this).find('iframe').attr('src')==href){
                    ex=true;
                    appBody.find('.layadmin-tabsbody-item').removeClass('layui-show');
                    $(this).addClass('layui-show');
                    return;
                }
            })
        }else{//标签不存在的情况下
            appTabsHeader.find('li').removeClass('layui-this');
            var li=appTabsHeader.find('li:eq(0)').clone(true).addClass('layui-this');
            var i=appTabsHeader.find('li:eq(0) i.layui-tab-close').clone(true);
            li=li.data('href',href).html(_this.find('cite').text());
            i.appendTo(li);
            li.appendTo(appTabsHeader);
            appBody.find('.layadmin-tabsbody-item').removeClass('layui-show');

            var item=appBody.find('.layadmin-tabsbody-item:eq(0)').clone(true).addClass('layui-show').find('iframe').attr('src',href).parent().appendTo(appBody);
            $fItem=$('<div class="layadmin-tabsbody-item">\n' +
                '   <iframe src="" frameborder="0"></iframe>\n' +
                '   <div class="loading"><i class="layui-icon layui-anim layui-anim-rotate layui-anim-loop">&#xe63d;</i></div>\n' +
                '</div>');
            $fItem.addClass('layui-show');
            //var item=appBody.append($fItem).find('iframe').attr('src',href);
            loadStatus(item.find('iframe')[0]);
        }
    });



    function loadStatus(e) {
        $(e).siblings('.loading').show();
        if (e.attachEvent) {
            e.attachEvent("onload", function() {
                $(e).siblings('.loading').remove();
            });
        } else {
            e.onload = function() {
                $(e).siblings('.loading').remove();
            };
        }
    }

    //修改个人密码
    var setPwdLay=null;
    $('#setPwd').click(function () {
        setPwdLay=layer.open({
            type: 1,
            title: '修改密码',
            area:['400px','250px'],
            content: '<form class="layui-form" action="" style="padding: 15px 20px 20px"><div class="layui-form-item" >\n' +
            '      <input type="hidden" value="" id=""><input type="password" name="pwd" required  lay-verify="required" placeholder="请输入新密码" autocomplete="off" class="layui-input">\n' +
            '  </div>' +
            ' <div class="layui-form-item">\n' +
            '      <input type="password" name="repwd" required  lay-verify="required" placeholder="再次输入密码" autocomplete="off" class="layui-input">\n' +
            '  </div>'+
            '<div class="layui-form-item">\n' +
            '      <button class="layui-btn" lay-submit lay-filter="formSetPwd">立即提交</button>\n' +
            '  </div></form>'
        });
    });

    form.on('submit(formSetPwd)',function (data) {
        var url=$('#setPwd').attr('lay-href');
        $.ajax({
            url:url,
            data:data.field,
            type:'post',
            dataType:'json',
            error:function (xhr) {
                layer.msg('超时'+xhr.status,{icon:2});
            },
            success:function (data) {
                if(data.code==0){
                    layer.msg('完成',{icon:1});
                    layer.close(setPwdLay);
                }else{
                    layer.msg(data.msg,{icon:2});
                }
            }
        });
        return false;
    });

    $('#setInfo').click(function () {
        var url=$('#setInfo').attr('lay-href');
        top.layer.open({
            type:2,
            title:'个人基本信息',
            area:['600px','470px'],
            content:url,
        });
    });

});