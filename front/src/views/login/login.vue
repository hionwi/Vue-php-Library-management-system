<template>
  <div class="login-container">
    <el-form ref="form" :model="form" label-width="60px" class="login-form">
      <h2 class="login-title">图书管理系统</h2>
      <el-form-item label="用户名">
        <el-input v-model="form.username"></el-input>
      </el-form-item>
      <el-form-item label="密码">
        <el-input v-model="form.password" show-password></el-input>
      </el-form-item>

      <el-form-item style="text-align: center">
        <el-button type="primary" @click="login" style="width: 250px"
          >登录</el-button
        >
      </el-form-item>
    </el-form>
  </div>
</template>
  <script>
export default {
  data() {
    return {
      form: {
        username: "",
        password: "",
      },
      errormsg: "",
    };
  },
  methods: {
    login() {
      if (!this.form.username || !this.form.password) {
        this.errormsg = "账号或密码为空";
        this.$message.error(this.errormsg);
      } else {
        this.$axios
          .post("http://119.29.62.3/login.php", {
            u_ID: this.form.username,
            u_pwd: $.md5(this.form.password),
          })
          .then((Response) => {
            let res = Response.data;
            if (res.status == "1") {
              //用户
              this.errormsg = "登陆成功";
              sessionStorage.setItem("userid", this.form.username);
              sessionStorage.setItem("flag", 1); //判断是否为管理员 用户1 管理员2
              sessionStorage.setItem("password", this.form.password);
              this.$message.success(this.errormsg);
              this.$router.replace("/home_user");
            } else if (res.status == "0") {
              //登陆失败
              this.errormsg = "用户名或密码错误";
              this.$message.error(this.errormsg);
            } else if (res.status == "-1") {
              //管理员
              this.errormsg = "登陆成功";
              sessionStorage.setItem("userid", this.form.username);
              sessionStorage.setItem("flag", 2); //判断是否为管理员 用户1 管理员2
              this.$message.success(this.errormsg);
              this.$router.replace("/home_ad");
            }
          });
      }
    },
  },
};
</script>

<style acoped>
.login-form {
  width: 400px;
  margin: 160px auto; /* 上下间距160px，左右自动居中*/
  background-color: rgb(255, 255, 255, 0.8); /* 透明背景色 */
  padding: 30px;
  border-radius: 20px; /* 圆角 */
}

/* 背景 */
.login-container {
  position: absolute;
  width: 100%;
  height: 100%;
  background: url("../../assets/login.jpg");
}
/* 标题 */
.login-title {
  color: #303133;
  text-align: center;
}
</style> -->