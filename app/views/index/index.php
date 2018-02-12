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
                        <a :href="submenu.path" :class="{ routerLinkActive: submenu.path==='<?php echo $url;?>' }" v-for="submenu in menu.children" v-html="submenu.name"></a>
                    </div>
                </li>
            </ul>
            <!--主要内容区-->
            <div id="content" class='right-content'>
                <!-- 路由出口 -->
                <!-- 路由匹配到的组件将渲染在这里 -->

                <template>
                    <el-row>
                        <!--<el-col :span="24" style="padding-left: 20px;margin-bottom: 20px">-->
                        <!--<el-button type="primary" @click="createContainer">新建容器</el-button>-->
                        <!--</el-col>-->
                        <el-col :span="24">
                            <el-table
                                    v-loading="loading"
                                    :data="tableData"
                                    border
                                    style="width: 100%">
                                <el-table-column
                                        label="容器ID"
                                        width="100">
                                    <template scope="scope">
                                        <span style="margin-left: 10px">{{ scope.row.container_id }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column
                                        label="容器名称"
                                        width="200">
                                    <template scope="scope">
                                        <!--<span style="margin-left: 10px">{{ scope.row.container_name }}</span>-->
                                        <a :href="scope.row.router">{{ scope.row.container_name }}</a>
                                    </template>
                                </el-table-column>
                                <el-table-column
                                        label="镜像名称"
                                        width="200">
                                    <template scope="scope">
                                        <span style="margin-left: 10px">{{ scope.row.image_name }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column
                                        label="映射端口">
                                    <template scope="scope">
                                        <span style="margin-left: 10px">{{ scope.row.mapPorts }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column
                                        label="状态" width="100">
                                    <template scope="scope">
                                        <span style="margin-left: 10px">{{ scope.row.state }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column label="操作"
                                                 width="260">
                                    <template scope="scope">
                                        <el-button
                                                size="small"
                                                type="success"
                                                :disabled="scope.row.status==0"
                                                @click="openTerminal(scope.$index, scope.row)">终端</el-button>
                                        <el-button
                                                size="small"
                                                type="info"
                                                :disabled="scope.row.status==0"
                                                @click="handleRestart(scope.$index, scope.row)">重启</el-button>
                                        <el-button
                                                size="small"
                                                type="success"
                                                :disabled="scope.row.status==1"
                                                @click="handleStart(scope.$index, scope.row)">开始</el-button>
                                        <el-button
                                                size="small"
                                                type="warning"
                                                :disabled="scope.row.status==0"
                                                @click="handleStop(scope.$index, scope.row)">停止</el-button>
                                        <el-dropdown trigger="click" style="margin-left: 5px;color: #20A0FF;">
                            <span class="el-dropdown-link">
                                <i class="el-icon-caret-bottom el-icon--right"></i>
                            </span>
                                            <el-dropdown-menu slot="dropdown">
                                                <el-dropdown-item @click.native="addPort(scope.row)">增加端口</el-dropdown-item>
                                                <el-dropdown-item @click.native="handleDelete(scope.$index, scope.row)">删除</el-dropdown-item>
                                            </el-dropdown-menu>
                                        </el-dropdown>
                                    </template>
                                </el-table-column>
                            </el-table>
                        </el-col>
                        <el-col :span="24" style="padding-left: 20px;margin-top: 10px">
                            <el-pagination
                                    @current-change="handleCurrentChange"
                                    layout="prev, pager, next"
                                    :page-size="15"
                                    :total="total">
                            </el-pagination>
                        </el-col>

                        <el-dialog title="请输入密码" size="tiny" :visible.sync="dialogFormVisible">
                            <el-form>
                                <el-form-item label="密码" label-width="120px">
                                    <el-input type="password" v-model="password" auto-complete="off"></el-input>
                                </el-form-item>
                            </el-form>
                            <div slot="footer" class="dialog-footer">
                                <el-button @click="dialogFormVisible = false">取 消</el-button>
                                <el-button type="primary" @click="passwordSubmit">确 定</el-button>
                            </div>
                        </el-dialog>

                        <el-dialog title="新增端口" :visible.sync="addPortFormVisible">
                            <el-form :model="addPortForm" :rules="addPortRules" ref="addPortForm">
                                <el-form-item label="外部端口" label-width="120px" prop="external">
                                    <el-input @blur="detectionPort('external')" v-model="addPortForm.external" auto-complete="off"></el-input>
                                </el-form-item>
                                <el-form-item label="内部端口" label-width="120px" prop="internal">
                                    <el-input @blur="detectionPort('internal')" v-model="addPortForm.internal" auto-complete="off"></el-input>
                                </el-form-item>
                            </el-form>
                            <div slot="footer" class="dialog-footer">
                                <el-button @click="addPortFormVisible = false">取 消</el-button>
                                <el-button type="primary" @click="addPortFormSubmit('addPortForm')">确 定</el-button>
                            </div>
                        </el-dialog>
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
                    loading: false,
                    tableData: [],
                    pagination: {
                        page: 1,
                        perPage: 15,
                        type: 'all',
                    },
                    total: 0,
                    dialogFormVisible: false,
                    addPortFormVisible: false,
                    password: '',
                    currentId: '',
                    addPortForm: {
                        id: 0,
                        cid: '',
                        external: '',
                        internal: '',
                    },
                    addPortRules: {
                        external: [
                            {required: true, message: '请输入外部端口', trigger: 'blur' },
                        ],
                        internal: [
                            {required: true, message: '请输入内部端口', trigger: 'blur' },
                        ],
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
                        }).catch(function (error) {
                            console.log(error);
                        });
                    },
                    detectionPort(type) {
                        let _this = this;
                        let port = '';
                        if(type === 'external') {
                            port = _this.addPortForm.external;
                        }else {
                            port = _this.addPortForm.internal;
                        }

                        axios.post('/detection/ports', {
                            port: port,
                            type: type,
                            cid: _this.currentId
                        }).then(function (response) {
                            let data = response.data;
                            if(data.status !== 0) {
                                _this.$message.error(data.message);
                                if(type === 'external') {
                                    _this.addPortForm.external = '';
                                }else {
                                    _this.addPortForm.internal = '';
                                }
                            }
                        }).catch(function (error) {

                        });
                    },
                    addPortFormSubmit(addPortForm) {
                        let _this = this;
                        _this.$refs[addPortForm].validate((valid) => {
                            if (valid) {
                                _this.addPortFormVisible = false;
                                _this.loading = true;
                                axios.post('/ports/add', _this.addPortForm).then(function (response) {
                                    let res = response.data;
                                    if(res.status === 0 ) {
                                        _this.$message({
                                            type: 'success',
                                            message: res.message
                                        });
                                        _this.getContainers();
                                    }else {
                                        _this.$message.error(res.message);
                                    }
                                    _this.loading = false;
                                }).catch(function (error) {

                                });
                            } else {
                                console.log('error submit!!');
                                return false;
                            }
                        });
                    },
                    addPort(row) {
                        this.currentId = row.id;
                        this.addPortForm.cid = row.container_id;
                        this.addPortForm.id = row.id;
                        this.addPortFormVisible = true;
                    },
                    handleRestart(index, row) {
                        this.operateContainer(row.container_id, 'restart')
                    },
                    handleStart(index, row) {
                        console.log(index, row.id);
                        this.operateContainer(row.container_id, 'start')
                    },
                    handleStop(index, row) {
                        console.log(index, row);
                        this.operateContainer(row.container_id, 'stop')
                    },
                    handleDelete(index, row) {
                        console.log(index, row);
                        let _this = this;
                        _this.currentId = row.container_id;
                        _this.$confirm('此操作将永久删除该容器, 是否继续?', '提示', {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            type: 'warning'
                        }).then(() => {
                            _this.dialogFormVisible = true;

                        }).catch(() => {

                        });
                    },
                    operateContainer(id, type) {
                        let _this = this;
                        let _duration = 1000;
                        _this.loading = true;
                        axios.post('/container/operate', {
                            id: id,
                            type: type
                        }).then(function (response) {
                            let res = response.data;
                            if(res.status === 0) {
                                _this.$message({
                                    message: res.message,
                                    type: 'success',
                                    duration: _duration
                                });
                                _this.loading = false;
                                _this.getContainers();

                            }else {
                                _this.$message.error(res.message);
                                _this.loading = false;
                            }
                        }).catch(function (error) {
                            _this.loading = false;
                        });
                    },
                    getContainers() {
                        let _this = this;
                        _this.loading = true;
                        axios.get('/containers', {'params': _this.pagination}).then(function (response) {
                            let data = response.data;
                            _this.tableData = data.data;
                            _this.total = parseInt(data.total);
                            _this.loading = false;
                        }).catch(function (error) {

                        })
                    },
                    handleCurrentChange(val) {
                        this.pagination.page = val;
                        this.getContainers();
                    },
                    passwordSubmit() {
                        let _this = this;
                        _this.dialogFormVisible = false;
                        _this.loading = true;
                        axios.post('/validate/password', {
                            'password': _this.password
                        }).then(function (response) {
                            let res = response.data;
                            if(res) {
                                _this.operateContainer(_this.currentId, 'delete');
                            }else {
                                _this.$message.error('密码错误');
                            }
                            _this.password = '';
                            _this.loading = false;
                        }).catch(function (error) {
                            _this.loading = false;
                        });
                    },
                    openTerminal(index, row) {
                        console.log(row);
                        if(row.terminalPort) {
                            let url = 'http://' + row.hosts + ':' + row.terminalPort;
//                    this.terminalSrc = url;
                            window.open(url, '_blank', 'top=200,left=500,width=800,height=600,menubar=no,scrollbars=no,toolbar=yes,status=no,location=no');
                        }
//                this.terminalVisible = true;
                    }
                },

                mounted() {
                    this.getMenus();
                    this.getContainers();
                }
            }).$mount('#app');
        </script>
    </body>
</html>