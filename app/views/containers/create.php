<!--<meta name="csrf-token" content="--><?php //echo csrf_token(); ?><!--">-->
<!DOCTYPE html>
<html>
<head>
    <title>YYF</title>
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
                    <a :href="submenu.path" :class="{ routerLinkActive: submenu.path==='<?php echo $url;?>' }" v-for="submenu in menu.children" v-html="submenu.name"></a>
                </div>
            </li>
        </ul>
        <!--主要内容区-->
        <div id="content" class='right-content'>
            <!-- 路由出口 -->
            <!-- 路由匹配到的组件将渲染在这里 -->
            <template>
                <div v-loading="loading">
                    <form @submit.prevent="createContainer">
                        <div class="right-head"><i class="cj"></i>创建容器</div>
                        <div class="right-main">
                            <span>实例名称：</span>
                            <input type='text' v-model="createForm.name" required />
                            <span class="ml">镜像选择：</span>
                            <select v-model="createForm.image" required>
                                <option value="">请选择镜像</option>
                                <option v-for="item in images" :value="item.id">{{ item.name }}</option>
                            </select>
                        </div>
                        <div class="right-head"><i class="dk"></i>端口映射</div>
                        <div style="color:red;margin-left: 50px;margin-bottom: -40px;font-size: 15px;">默认会创建80,22,3000端口</div>
                        <div class="right-main" id="mapPorts">
                            <div class="port" v-for="(item,index) in ports" style="margin-top: 10px">
                                <span>外部端口： </span>
                                <input type='text' @blur="detectionPort(index, 'external')" v-model="createForm.ports[index].external" required /><span class="ml">内部端口：</span><input type='text' @blur="detectionPort(index, 'internal')" v-model="createForm.ports[index].internal" required />
                            </div>
                        </div>
                        <div class="add" id="addPorts" @click="addPorts" style="cursor: pointer;font-size: 14px"><i></i>创建多个端口</div>
                        <div class="serve">
                            <p>容器简介：</p>
                            <textarea v-model="createForm.introduce"></textarea>
                            <div class="sure">
                                <button>确认</button>
                                <button type="button" class="qx" @click="cancelForm">取消</button>
                            </div>
                        </div>
                    </form>
                </div>
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
            loading: false,
            ports: [1],
            images: [],
            createForm: {
                name: '',
                image: '',
                ports: [
                    {
                        external: '',
                        internal: ''
                    }
                ],
                introduce: ''
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
            addPorts() {
                this.ports.push(1);
                this.createForm.ports.push({
                    external: '',
                    internal: ''
                });
                console.log(this.createForm.ports);
            },
            createContainer() {
                let _this = this;
                let _duration = 10 * 1000;
                _this.loading = true;
                axios.get('/container/create', {params:_this.createForm}).then(function (response) {
                    let res = response.data;
                    if(res.status === 0) {
                        setTimeout(function () {
                            _this.$message({
                                message: res.message,
                                type: 'success',
                                duration: 1000
                            });
                            _this.cancelForm();
//                            _this.$router.push({path: 'mine'});
                            _this.loading = false;
                            window.location.href = '/mine';
                        }, _duration);
                    }else {
                        _this.$message.error(res.message);
                        _this.loading = false;
                    }
                }).catch(function (error) {
                    _this.$message.error('创建失败');
                    _this.loading = false;
                })
            },
            cancelForm() {
                this.ports = [1];
                this.createForm = {
                    name: '',
                    image: '',
                    ports: [
                        {
                            external: '',
                            internal: ''
                        }
                    ],
                    introduce: ''
                };
            },
            getImages() {
                let _this = this;
                axios.get('/containers/images').then(function (response) {
                    _this.images = response.data;
                }).catch(function (error) {

                })
            },
            detectionPort(index, type) {
                let _this = this;
                let port = '';
                if(type === 'external') {
                    port = _this.createForm.ports[index].external;
                }else {
                    port = _this.createForm.ports[index].internal;
                }

                axios.get('/detection/ports', {params:{
                    port: port,
                    type: type
                }}).then(function (response) {
                    let data = response.data;
                    if(data.status !== 0) {
                        _this.$message.error(data.message);
                        if(type === 'external') {
                            _this.createForm.ports[index].external = '';
                        }else {
                            _this.createForm.ports[index].internal = '';
                        }
                    }
                }).catch(function (error) {

                });
            }
        },
        mounted() {
            this.getMenus();
            this.getImages();
        }
    }).$mount('#app');
</script>
</body>
</html>
