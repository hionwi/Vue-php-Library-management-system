<template>
  <el-container style="height: 100%">
    <el-header>
      <span style="text-align: left; font-size: 20px" class="title"
        >图书管理系统</span
      >
      <span style="float: right; text-align: right"
        >欢迎你,用户({{ user }})</span
      >
      <el-dropdown style="float: right; text-align: right">
        <i class="el-icon-user" style="margin-right: 15px"></i>
        <el-dropdown-menu slot="dropdown">
          <el-dropdown-item @click.native="change_pwd_next"
            >修改密码</el-dropdown-item
          >
          <el-dropdown-item @click.native="exit_ad">退出登录</el-dropdown-item>
        </el-dropdown-menu>
      </el-dropdown>
    </el-header>

    <el-container>
      <el-aside width="15%">
        <el-menu default-active="2" class="el-menu-vertical-demo">
          <el-submenu index="1">
            <template slot="title">
              <i class="el-icon-menu"></i>
              <span>图书管理</span>
            </template>
            <el-menu-item-group>
              <el-menu-item index="1-1" @click="user_booksearch"
                >借阅图书</el-menu-item
              >
              <el-menu-item index="1-2" @click="user_book"
                >我的图书</el-menu-item
              >
            </el-menu-item-group>
          </el-submenu>
          <el-menu-item index="2" @click="hotbook">
            <i class="iconfont icon-rexiao"></i>
            <span slot="title">&nbsp;&nbsp;&nbsp;热门图书</span>
          </el-menu-item>
          <el-menu-item index="3">
            <i class="el-icon-user-solid" @click="mymsg"></i>
            <span slot="title" @click="mymsg">我的信息</span>
            <el-drawer title="我的用户信息" :visible.sync="drawer">
              <span>用户名:&emsp;{{ user }}</span>
              <p></p>
              <span>姓名:&emsp;{{ name }}</span>
              <p></p>
              <span>借阅书籍数量:&emsp;{{ number }}</span>
              <p></p>
              <span>违规次数:&emsp;{{ fail }}</span>
            </el-drawer>
          </el-menu-item>
          <el-menu-item index="4" @click="log">
            <i class="el-icon-time"></i>
            <span slot="title">历史借阅</span>
          </el-menu-item>
        </el-menu>
      </el-aside>
      <el-container>
        <el-main>
          <el-dialog
            v-if="password == '123456'"
            title="修改密码"
            :visible.sync="dialogVisible"
            width="30%"
            close-on-click-modal="false"
            close-on-press-escape="false"
            show-close="false"
          >
            <el-input
              v-model="newpassword"
              placeholder="请输入新密码"
              show-password
            >
              <template slot="prepend">密码</template>
            </el-input>
            <span slot="footer" class="dialog-footer">
              <el-button type="primary" @click="change_pwd">确 定</el-button>
            </span>
          </el-dialog>
          <router-view></router-view>
        </el-main>
      </el-container>
    </el-container>
  </el-container>
</template>

<script>
export default {
  data() {
    return {
      user: sessionStorage.getItem("userid"),
      password: sessionStorage.getItem("password"),
      newpassword: "",
      dialogVisible: true,
      drawer: false,
      name: "",
      number: "",
      fail: "",
    };
  },
  methods: {
    exit_ad() {
      sessionStorage.clear();
      this.$router.replace("/");
    },
    log() {
      this.$router.push("/home_user/history");
    },
    hotbook() {
      this.$router.push("/home_user/hotbook");
    },
    mymsg() {
      this.drawer = true;
      this.$axios
        .post("http://119.29.62.3/us_user.php", {
          u_ID: this.user,
        })
        .then((Response) => {
          let res = Response.data;
          this.name = res.u_name;
          this.number = res.u_number;
          this.fail = res.u_fail;
        });
    },
    user_booksearch() {
      this.$router.push("/home_user/booksearch");
    },
    user_book() {
      this.$router.push("/home_user/mybook");
    },
    change_pwd() {
      if (!this.newpassword) {
        this.$message.error("密码不能为空");
      } else {
        this.$axios
          .post("http://119.29.62.3/u_upwd.php", {
            u_ID: this.user,
            u_old_pwd: "123456",
            u_new_pwd: this.newpassword,
          })
          .then((Response) => {
            let res = Response.data;
            if (res.status == "1") {
              this.$message.success("更改成功");
              sessionStorage.setItem("password", this.newpassword);
              this.dialogVisible = false;
            } else if (res.status == "0") {
              this.$message.error("更改失败");
            }
          });
      }
    },
    change_pwd_next() {
      this.$router.push("/home_user/changepwd");
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