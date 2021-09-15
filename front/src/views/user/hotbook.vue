<template>
  <el-main>
    <template>
      <h2 style="text-align: center">近期最热门的图书</h2>
      <el-table v-loading="loading" :data="tabledata" style="width: 100%">
        <el-table-column label="ISBN">
          <template slot-scope="scope">
            <span>{{ scope.row.ISBN }}</span>
          </template>
        </el-table-column>
        <el-table-column label="书名">
          <template slot-scope="scope">
            <span>{{ scope.row.b_name }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="author" label="作者">
          <template slot-scope="scope">
            <span>{{ scope.row.author }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="type_name" label="类型">
          <template slot-scope="scope">
            <span>{{ scope.row.type_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="预计归还日期">
          <template slot-scope="scope">
            <i class="el-icon-date"></i>
            <span style="margin-left: 10px">{{ scope.row.rule_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="操作">
          <template slot-scope="scope">
            <el-button
              size="mini"
              type="primary"
              @click="borrow(scope.$index, scope.row)"
              :disabled="scope.row.status == '0'"
              >借阅</el-button
            >
          </template>
        </el-table-column>
      </el-table>
    </template>
  </el-main>
</template>

<script>
export default {
  data() {
    return {
      loading: true, //加载
      tabledata: [], //表格的数据
      idnex: 0,
      ISBN: "",
    };
  },
  mounted() {
    this.submit();
  },
  methods: {
    submit() {
      this.loading = true;
      this.tabledata.splice(0, this.tabledata.length);
      this.$axios
        .post("http://119.29.62.3/hotbooks.php") //改为近期最热门图书
        .then((Response) => {
          let res = Response.data;
          for (var i = 0; i < res.length; i++) {
            this.tabledata.push(res[i]);
          }
          this.loading = false;
        });
    },
    borrow(index, row) {
      this.ISBN = row.ISBN;
      this.$axios
        .post("http://119.29.62.3/user_get.php", {
          ISBN: this.ISBN,
          u_ID: sessionStorage.getItem("userid"),
        })
        .then((Response) => {
          let res = Response.data;
          if (res.status == "1") {
            this.$message.success("借阅成功");
          } else if (res.status == "-1") {
            this.$message.error("借阅失败，当前借阅数量已达上限");
          } else if (res.status == "0") {
            this.$message.error("借阅失败，违规次数过多");
          } else {
            this.$message.error("借阅失败，请稍后再试");
          }
          this.submit();
        });
    },
  },
};
</script>