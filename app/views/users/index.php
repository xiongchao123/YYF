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
                                label="ID"
                                width="100">
                                <template scope="scope">
                                    <span style="margin-left: 10px">{{ scope.row.id }}</span>
                                </template>
                            </el-table-column>
                            <el-table-column
                                label="用户名"
                                width="170">
                                <template scope="scope">
                                    <span style="margin-left: 10px">{{ scope.row.name }}</span>
                                </template>
                            </el-table-column>
                            <el-table-column
                                label="手机号"
                                width="170">
                                <template scope="scope">
                                    <span style="margin-left: 10px">{{ scope.row.userphone }}</span>
                                </template>
                            </el-table-column>
                            <el-table-column
                                label="邮箱">
                                <template scope="scope">
                                    <span style="margin-left: 10px">{{ scope.row.email }}</span>
                                </template>
                            </el-table-column>
                            <el-table-column
                                label="最大容器数" width="150">
                                <template scope="scope">
                                    <span style="margin-left: 10px">{{ scope.row.max_containers }}</span>
                                </template>
                            </el-table-column>
                            <el-table-column label="操作"
                                             width="240">
                                <template scope="scope">
                                    <el-button
                                        size="small"
                                        type="warning"
                                        @click="editRoles(scope.row)">编辑</el-button>
                                    <el-button
                                        size="small"
                                        type="warning"
                                        @click="handleDelete(scope.$index, scope.row)">删除</el-button>
                                    <!-- <el-dropdown trigger="click" style="margin-left: 5px;color: #20A0FF;">
                                         <span class="el-dropdown-link">
                                             <i class="el-icon-caret-bottom el-icon&#45;&#45;right"></i>
                                         </span>
                                         <el-dropdown-menu slot="dropdown">
                                             <el-dropdown-item @click.native="handleDelete(scope.$index, scope.row)">删除</el-dropdown-item>
                                             &lt;!&ndash;<el-dropdown-item @click.native="openTerminal(scope.$index, scope.row)">终端</el-dropdown-item>&ndash;&gt;
                                         </el-dropdown-menu>
                                     </el-dropdown>-->
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

                    <el-dialog title="编辑" :visible.sync="dialogFormVisible">
                        <el-form :model="form">
                            <el-form-item label="最大容器数" label-width="120px">
                                <el-input v-model="form.number"></el-input>
                            </el-form-item>
                            <el-form-item label="设置权限" label-width="120px">
                                <el-select v-model="form.role" placeholder="请选择用户权限">
                                    <el-option v-for="role in roles" :label="role.name" :value="role.id"></el-option>
                                </el-select>
                            </el-form-item>
                        </el-form>
                        <div slot="footer" class="dialog-footer">
                            <el-button @click="dialogFormVisible = false">取 消</el-button>
                            <el-button type="primary" @click.prevent="rolesSubmit">确 定</el-button>
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
            menus: [],
            loading: false,
            dialogFormVisible:false,
            tableData: [],
            pagination: {
                page: 1,
                perPage: 15,
            },
            total: 0,
            form: {
                id:0,
                number: 0,
                role: ''
            },
            roles: [],
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
                let uri = "<?php echo $url;?>";
                axios.get('/menu/getMenus').then(function (response) {
                    _this.menus = response.data;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            getUsers() {
                let _this = this;
                _this.loading = true;
                axios.get('/users/getUsers', {'params':_this.pagination}).then(function (response) {
                    let data = response.data;
                    _this.tableData = data.data;
                    _this.total = parseInt(data.total);
                    _this.loading = false;
                }).catch(function (error) {

                })
            },
            editRoles(row) {
                this.form.id = row.id;
                this.form.number = row.max_containers;
                this.form.role = row.role;
                this.dialogFormVisible = true;
            },
            handleCurrentChange(val) {
                this.pagination.page = val;
                this.getUsers();
            },
            handleDelete(index, row) {
                let _this = this;
                axios.get('/users/delete', {'params': {
                    id: row.id
                }}).then(function (response) {
                    let res = response.data;
                    if(res.status === 0) {
                        _this.$message({
                            type: 'success',
                            message: res.message,
                        });
                        _this.getUsers();
                    }else {
                        _this.$message.error(res.message);
                    }
                }).catch(function(error) {

                });
            },
            getRoles() {
                let _this = this;
                axios.get('/users/roles', {'params': _this.pagination}).then(function (response) {
                    _this.roles = response.data;
                }).catch(function (error) {

                })
            },
            rolesSubmit() {
                let _this = this;
                axios.get('/users/rolesEdit', {'params':_this.form}).then(function (response) {
                    let res = response.data;
                    if(res.status === 0) {
                        _this.$message({
                            type: 'success',
                            message: res.message,
                        });
                        _this.dialogFormVisible = false;
                        _this.getMenus();
                        _this.getUsers();
                    }else {
                        _this.$message.error(res.message);
                    }
                }).catch(function (error) {

                })
            }
        },
        mounted() {
            this.getMenus();
            this.getUsers();
            this.getRoles();
        }
    }).$mount('#app');
</script>
</body>
</html>