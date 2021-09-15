<template>
  <el-main>
    <el-input
      v-model="oldpwd"
      placeholder="请输入旧密码"
      style="width: 30%"
      show-password
    ></el-input>
    <p></p>
    <el-input
      v-model="newpwd"
      placeholder="请输入新密码"
      style="width: 30%"
      show-password
    ></el-input>
    <p></p>
    <el-button type="primary" @click="changepwd">确定</el-button>
  </el-main>
</template>

<script>
export default {
  data() {
    return {
      oldpwd: "",
      newpwd: "",
      uid: sessionStorage.getItem("userid"),
    };
  },
  methods: {
    changepwd() {
      if (!this.oldpwd || !this.newpwd) {
        this.$message.error("密码为空，请重新输入");
      } else {
        this.$axios
          .post("http://119.29.62.3/u_ad.php", {
            a_ID: this.uid,
            a_old_pwd: this.oldpwd,
            a_new_pwd: this.newpwd,
          })
          .then((Response) => {
            let res = Response.data;
            if (res.status == "0") {
              this.$message.error("旧密码错误,请重新输入");
            } else if (res.status == "1") {
              this.$message.success("修改成功，请重新登录");
              sessionStorage.clear();
              this.$router.replace("/");
            }
          });
      }
    },
  },
};
</script>