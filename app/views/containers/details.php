<!--<meta name="csrf-token" content="--><?php //echo csrf_token(); ?><!--">-->
<!DOCTYPE html>
<html>
<head>
    <title>YYF-YUNYIN YAF FRAMEWORK</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/js/vue/element-ui.css">
    <script src="/js/vue/vue.js"></script>
    <script src="/js/vue/axios.min.js"></script>
    <script src="/js/vue/element-ui.js"></script>
    <style type="text/css">
        #menu .routerLinkActive {
            background:#2e88db;
            text-decoration: none !important;
            color:#fff !important;
        }

        .com-nav .main .nav>a.routerLinkActive{color:#fff;text-decoration: none !important;}
        .com-nav .main .nav>a.routerLinkActive:after{width:100%;left:0;text-decoration: none !important;}
        .el-table__row .el-button {
            margin-left: 0;
        }
        .cell {
            padding: 0 !important;
            text-align: center;
        }
    </style>
</head>
<body>
<div id="app">
    <div class="fixed-head">
        <!--顶部-->
        <header class='com-head'>
            <section>
                <div class="l">
                    <a href="#" class='logo'></a>
                </div>
                <div class="r">
                    <a href="http://devops.eastmoney.com:11111" target="_blank">证券监控平台</a>
                    <a href="https://devops.eastmoney.com:11112" target="_blank">证券运维平台</a>
                    <span style="font-size: 14px;color: #fff;margin-left: 10px;">在线人数： <span style="color: red;" v-html="onlinePeople"></span></span>
                </div>
            </section>
        </header>
        <!--导航-->
        <nav class='com-nav'>
            <div class='main'>
                <div class="logo"></div>
                <div class="nav">
                    <a href="/foo">DevCloud</a>
                    <a href="/bar">敏捷管理</a>
                    <a href="/foo">镜像与服务</a>
                    <a href="/foo">Git仓库</a>
                    <a href="/foo">帮助</a>
                </div>
                <div class="avator">
                    <el-dropdown>
                              <span class="el-dropdown-link">
                                <img src="/img/avator.png"/>
                              </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item>
                                <a to="/showMine">个人主页</a>
                            </el-dropdown-item>
                            <el-dropdown-item @click.native="logout">退出</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </div>
            </div>
        </nav>
    </div>
    <!--内容-->
    <div class="com-main clearfix" id="menu">
        <!--左边栏-->
        <ul class="left-slide">
            <li v-for="menu in menus">
                <div class="type" v-html="'<i class=\'icon icon-jxfw\'></i>'+menu.name"></div>
                <div class="link">
                    <a :href="submenu.path" :class="{ routerLinkActive: submenu.path==='<?php echo $url ?? '';?>' }" v-for="submenu in menu.children" v-html="submenu.name"></a>
                </div>
            </li>
        </ul>
        <!--主要内容区-->
        <div id="content" class='right-content'>
            <!-- 路由出口 -->
            <!-- 路由匹配到的组件将渲染在这里 -->

            <template>
                <el-row>
                    <el-col :span="24" style="text-align: center;">
                        <div class="grid-content bg-purple-dark">
                            <h1>容器详情</h1>
                            <el-form ref="form" :model="form" label-width="80px" style="width: 800px;margin: 50px 0 0 200px;">
                                <el-form-item label="容器ID">
                                    <el-input disabled v-model="form.container_id"></el-input>
                                </el-form-item>
                                <el-form-item label="容器名称">
                                    <el-input disabled v-model="form.container_name"></el-input>
                                </el-form-item>
                                <el-form-item label="镜像名称">
                                    <el-input disabled v-model="form.image_name"></el-input>
                                </el-form-item>
                                <el-form-item label="ssh账号">
                                    <el-input disabled v-model="form.ssh.account"></el-input>
                                </el-form-item>
                                <el-form-item label="ssh密码">
                                    <el-input disabled v-model="form.ssh.pass"></el-input>
                                </el-form-item>
                                <el-form-item label="IP地址">
                                    <el-input disabled v-model="form.hosts"></el-input>
                                </el-form-item>
                                <el-form-item label="端口映射">
                                    <el-input disabled v-model="form.mapPorts"></el-input>
                                </el-form-item>
                                <el-form-item label="容器状态">
                                    <el-input disabled v-model="form.state"></el-input>
                                </el-form-item>
                                <!--<el-form-item>-->
                                <!--<el-button type="primary" @click="onSubmit">立即创建</el-button>-->
                                <!--<el-button>取消</el-button>-->
                                <!--</el-form-item>-->
                            </el-form>
                        </div>
                    </el-col>
                </el-row>
            </template>



        </div>
    </div>
</div>
<script>


    // 4. 创建和挂载根实例。
    // 记得要通过 router 配置参数注入路由，
    // 从而让整个应用都有路由功能
    const app = new Vue({
        data: {
            onlinePeople: 3,
            message: 'Hello Vue!',
            menus: [],
            containerId: "<?php echo $id; ?>",
            form: {
                container_id: '',
                container_name: '',
                image: '',
                ssh: {
                    account: '',
                    pass: ''
                },
                mapPorts: '',
                hosts: '',
                state: '',
            }
        },
        methods: {
            logout() {
                axios.get('/logout').then(function (response) {
                    let data = response.data;
                    if( data.status === 0 ) {
                        window.location.href = '/mine';
                    }
                }).catch(function (error) {

                })
            },
            getMenus() {
                let _this = this;
                axios.get('/menu/getMenus').then(function (response) {
                    _this.menus = response.data;
                    console.log(response);
                }).catch(function (error) {
                    console.log(error);
                });
            },
            onSubmit() {
                console.log('submit!');
            },
            getDetails() {
                let _this = this;
                axios.get('/containers/getDetails', {params : {
                    id: _this.containerId
                }}).then(function (response) {
                    console.log(response);
//                    let data = response.data;
                    _this.form = response.data;
                }).catch(function (error) {

                });
            }
        },
        mounted() {
            this.getMenus();
            this.getDetails();
        }
    }).$mount('#app');
</script>
</body>
</html>