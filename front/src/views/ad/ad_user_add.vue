<template>
  <el-main>
    <el-input v-model="u_ID" style="width: 40%">
      <template slot="prepend">用户名</template>
    </el-input>
    <p></p>
    <el-input v-model="u_name" style="width: 40%">
      <template slot="prepend">姓名</template>
    </el-input>
    <p></p>
    <el-button type="primary" @click="useradd">确定</el-button>
  </el-main>
</template>

<script>
export default {
  data() {
    return {
      u_ID: "",
      u_name: "",
    };
  },
  methods: {
    useradd() {
      if (!this.u_ID || !this.u_name) {
        this.$message.error("录入用户信息不能为空");
      } else if (isNaN(this.u_ID) || this.u_ID.length != 12) {
        this.$message.error("录入格式有误");
      } else {
        this.$axios
          .post("http://119.29.62.3/w_user.php", {
            u_ID: this.u_ID,
            u_name: this.u_name,
          })
          .then((Response) => {
            let res = Response.data;
            if (res.status == "1") {
              this.$message.success("录入成功");
            } else if (res.status == "0") {
              this.$message.error("已有用户信息，录入失败");
            } else if (res.status == "-1") {
              this.$message.error("已有相同的管理员信息，录入失败");
            }
          });
      }
    },
  },
};
</script>