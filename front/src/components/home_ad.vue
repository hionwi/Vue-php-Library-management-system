<template>
  <el-container style="height: 100%">
    <el-header>
      <span style="text-align: left; font-size: 20px" class="title"
        >图书管理系统</span
      >
      <span style="float: right; text-align: right">欢迎你，管理员</span>
      <el-dropdown style="float: right; text-align: right">
        <i class="el-icon-user" style="margin-right: 15px"></i>
        <el-dropdown-menu slot="dropdown">
          <el-dropdown-item @click.native="change_pwd"
            >修改密码</el-dropdown-item
          >
          <el-dropdown-item @click.native="exit_ad">退出登录</el-dropdown-item>
        </el-dropdown-menu>
      </el-dropdown>
    </el-header>

    <el-container>
      <el-aside width="15%">
        <el-menu
          default-active="2"
          class="el-menu-vertical-demo"
          style="text-align: center"
        >
          <el-submenu index="1">
            <template slot="title">
              <i class="el-icon-menu"></i>
              <span>图书管理</span>
            </template>
            <el-menu-item-group>
              <el-menu-item index="1-1" @click="bookadd">增添图书</el-menu-item>
              <el-menu-item index="1-2" @click="changebook"
                >修改图书</el-menu-item
              >
            </el-menu-item-group>
          </el-submenu>
          <el-submenu index="2">
            <template slot="title">
              <i class="el-icon-user-solid"></i>
              <span>用户管理</span>
            </template>
            <el-menu-item-group>
              <el-menu-item index="2-1" @click="useradd">增添用户</el-menu-item>
              <el-menu-item index="2-2" @click="changeuser"
                >修改用户信息</el-menu-item
              >
            </el-menu-item-group>
          </el-submenu>
          <el-submenu index="3">
            <template slot="title">
              <i class="el-icon-notebook-1"></i>
              <span>借阅管理</span>
            </template>
            <el-menu-item-group>
              <el-menu-item index="3-1" @click="nowborrow"
                >当前借阅</el-menu-item
              >
              <el-menu-item index="3-2" @click="historyborrow"
                >历史借阅</el-menu-item
              >
            </el-menu-item-group>
          </el-submenu>
        </el-menu>
      </el-aside>
      <el-container>
        <el-main>
          <router-view></router-view>
        </el-main>
      </el-container>
    </el-container>
  </el-container>
</template>

<script>
export default {
  methods: {
    historyborrow() {
      this.$router.push("/home_ad/historyborrow");
    },
    nowborrow() {
      this.$router.push("/home_ad/nowborrow");
    },
    bookadd() {
      this.$router.push("/home_ad/bookadd");
    },
    changebook() {
      this.$router.push("/home_ad/booksearch");
    },
    useradd() {
      this.$router.push("/home_ad/useradd");
    },
    changeuser() {
      this.$router.push("/home_ad/usersearch");
    },
    change_pwd() {
      this.$router.push("/home_ad/changepwd");
    },
    exit_ad() {
      sessionStorage.clear();
      this.$router.replace("/");
    },
  },
};
</script>


<style>
.el-header,
.el-footer {
  background-color: #b3c0d1;
  color: #333;
  line-height: 60px;
}

.el-aside {
  background-color: #ffffff;
  color: #333;
  text-align: center;
  line-height: 200px;
}

.el-main {
  background-color: #e9eef3;
  color: #333;
  text-align: center;
}
</style>