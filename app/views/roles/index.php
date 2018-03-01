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
                    <el-col :span="24" style="padding-left: 20px;margin-bottom: 20px">
                        <el-button type="primary" @click="newRoles">新增角色</el-button>
                    </el-col>
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
                                label="角色名"
                                width="170">
                                <template scope="scope">
                                    <span style="margin-left: 10px">{{ scope.row.name }}</span>
                                </template>
                            </el-table-column>
                            <!--<el-table-column-->
                            <!--label="角色描述"-->
                            <!--width="170">-->
                            <!--<template scope="scope">-->
                            <!--<span style="margin-left: 10px">{{ scope.row.description }}</span>-->
                            <!--</template>-->
                            <!--</el-table-column>-->
                            <el-table-column
                                label="角色权限" width="">
                                <template scope="scope">
                                    <span style="margin-left: 10px">{{ scope.row.menuNames }}</span>
                                </template>
                            </el-table-column>
                            <el-table-column label="操作"
                                             width="220">
                                <template scope="scope">
                                    <el-button
                                        size="small"
                                        type="warning"
                                        @click="editRoles(scope.row)">编辑</el-button>
                                    <el-button
                                        size="small"
                                        type="warning"
                                        @click="handleDelete(scope.row)">删除</el-button>
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

                    <el-dialog :title="dialogTitle"
                               :visible.sync="dialogFormVisible"
                               :before-close="handleClose"
                    >
                        <el-form :model="dialogForm" :rules="dialogFormRules" ref="dialogForm">
                            <el-form-item label="Id" label-width="120px" style="display: none;">
                                <el-input v-model="dialogForm.id"></el-input>
                            </el-form-item>
                            <el-form-item label="角色名" label-width="120px" prop="name">
                                <el-input v-model="dialogForm.name"></el-input>
                            </el-form-item>
                            <el-form-item label="角色描述" label-width="120px" prop="description">
<!--                                <quill-editor v-model="dialogForm.description"-->
<!--                                              ref="myQuillEditor"-->
<!--                                              :options="editorOption"-->
<!--                                              style="min-height: 20em"-->
<!--                                >-->
<!--                                </quill-editor>-->
                                <textarea style="border: 1px gray solid" name="" id="" cols="110" rows="10"></textarea>
                            </el-form-item>
                            <el-form-item label="角色权限" label-width="120px">
                                <el-transfer
                                    v-model="dialogForm.roles"
                                    :data="roles"
                                    :titles="['所有权限', '已拥有权限']"
                                    :button-texts="['取消权限', '配置权限']"
                                ></el-transfer>
                            </el-form-item>
                        </el-form>
                        <div slot="footer" class="dialog-footer">
                            <el-button @click="resetDialogForm('dialogForm')">取 消</el-button>
                            <el-button type="primary" @click.prevent="rolesSubmit('dialogForm')">确 定</el-button>
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
            editorOption: {
                placeholder: "请输入角色描述",
            },
            loading: false,
            dialogFormVisible: false,
            dialogTitle: '',
            tableData: [],
            roles: [],
            pagination: {
                page: 1,
                perPage: 15,
            },
            total: 0,
            dialogForm: {
                id: 0,
                name: '',
                description: '',
                roles: [],
            },
            dialogFormRules: {
                name: [
                    { required: true, message: '请输入角色名称', trigger: 'blur' }
                ],
                description: [
                    { required: true, message: '请输入角色描述', trigger: 'blur' }
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
            newRoles() {
                this.dialogTitle = '新增角色';
                this.dialogForm.id = 0;
                this.dialogFormVisible = true;
            },
            editRoles(row) {
                this.dialogTitle = '编辑角色';
                this.dialogForm.id = row.id;
                this.dialogForm.name = row.name;
                this.dialogForm.description = row.description;
                this.dialogForm.roles = row.menuIds;
                this.dialogFormVisible = true;
            },
            handleCurrentChange(val) {
                this.pagination.page = val;
                this.getRoles();
            },
            handleDelete(row) {
                let _this = this;

                _this.$confirm('此角色将永久被删除, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    window.axios.delete('/roles/' + row.id).then(function (response) {
                        let res = response.data;
                        if(res.status === 0) {
                            _this.$message({
                                type: 'success',
                                message: res.message,
                            });
                            _this.getRoles();
                        }else {
                            _this.$message.error(res.message);
                        }
                    }).catch(function (error) {

                    })
                }).catch(() => {

                });
            },
            getRoles() {
                let _this = this;
                _this.loading = true;
                axios.get('/roles/getRoles', {params:_this.pagination}).then(function (response) {
                    let res = response.data;
                    _this.total = res.total;
                    _this.tableData = res.data;
                    _this.loading = false;
                }).catch(function (error) {
                    _this.loading = false;
                })
            },
            getParentMenus() {
                let _this = this;
                _this.loading = true;
                axios.get('/menu/parent').then(function (response) {
                    let roles = response.data;
                    for(let i in roles) {
                        _this.roles.push({
                            key: roles[i].id,
                            label: roles[i].name,
                        });
                    };
                }).catch(function (error) {
                })
            },
            rolesSubmit(dialogForm) {
                let _this = this;
                _this.$refs[dialogForm].validate((valid) => {
                    if (valid) {
                        if(parseInt(_this.dialogForm.id) > 0) {
                            window.axios.put('/roles/update', _this.dialogForm).then(function (response) {
                                let res = response.data;
                                if(res.status === 0) {
                                    _this.$message({
                                        type: 'success',
                                        message: res.message,
                                    });
                                    _this.getRoles();
                                    _this.dialogFormVisible = false;
                                }else {
                                    _this.$message.error(res.message);
                                }
                            }).catch(function (error) {
                            });
                        }else {
                            window.axios.get('/roles/create', {
                                params: _this.dialogForm
                            }).then(function (response) {
                                let res = response.data;
                                if(res.status === 0) {
                                    _this.$message({
                                        type: 'success',
                                        message: res.message,
                                    });
                                    _this.getRoles();
                                    _this.dialogFormVisible = false;
                                }else {
                                    _this.$message.error(res.message);
                                }
                            }).catch(function (error) {
                            });
                        }

                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
            handleClose(done) {
                this.dialogForm = {
                    id: 0,
                    name: '',
                    description: ''
                };
                done();
            },
            resetDialogForm(dialogForm) {
//                this.$refs[dialogForm].resetFields();
                this.dialogForm = {
                    id: 0,
                    name: '',
                    description: ''
                };
                this.dialogFormVisible = false;
            }
        },
        mounted() {
            this.getMenus();
            this.getRoles();
            this.getParentMenus();
        }
    }).$mount('#app');
</script>
</body>
</html>