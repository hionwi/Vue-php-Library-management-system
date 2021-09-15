<template>
  <el-main>
    <el-input v-model="ISBN" style="width: 40%">
      <template slot="prepend">ISBN</template>
    </el-input>
    <p></p>
    <el-input v-model="b_name" style="width: 40%">
      <template slot="prepend">书名</template>
    </el-input>
    <p></p>
    <el-input v-model="author" style="width: 40%">
      <template slot="prepend">作者</template>
    </el-input>
    <p></p>
    <el-input v-model="type_name" style="width: 40%">
      <template slot="prepend">类型</template>
    </el-input>
    <p></p>
    <el-button type="primary" @click="bookadd">确定</el-button>
  </el-main>
</template>

<script>
export default {
  data() {
    return {
      ISBN: "",
      b_name: "",
      author: "",
      type_name: "",
    };
  },
  methods: {
    bookadd() {
      if (!this.ISBN || !this.b_name || !this.author || !this.type_name) {
        this.$message.error("录入书籍信息不能为空");
      } else if (isNaN(this.ISBN) || this.ISBN.length != 13) {
        this.$message.error("录入格式错误");
      } else {
        this.$axios
          .post("http://119.29.62.3/w_u_book.php", {
            ISBN: this.ISBN,
            b_name: this.b_name,
            author: this.author,
            type_name: this.type_name,
          })
          .then((Response) => {
            let res = Response.data;
            if (res.status == "1") {
              this.$message.success("录入成功");
            } else if (res.status == "0") {
              this.$message.error("录入失败");
            }
          });
      }
    },
  },
};
</script>