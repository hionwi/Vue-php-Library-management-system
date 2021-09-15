<template>
  <el-main>
    <el-button
      v-if="search_flag == true"
      icon="el-icon-back"
      style="float: left; text-align: left"
      @click="back"
      circle
    ></el-button>
    <el-input
      v-model="u_ID"
      placeholder="请输入要查询的用户名"
      style="width: 40%"
    ></el-input>
    <el-button type="primary" @click="search_id">查询</el-button>
    <el-button type="danger" @click="check_w">查看违规用户</el-button>
    <template>
      <p></p>
      <el-table
        v-loading="loading"
        :data="
          tabledata.slice((currentPage - 1) * pagesize, currentPage * pagesize)
        "
        style="width: 100%"
      >
        <el-table-column label="用户名">
          <template slot-scope="scope">
            <span>{{ scope.row.u_ID }}</span>
          </template>
        </el-table-column>
        <el-table-column label="姓名">
          <template slot-scope="scope">
            <span>{{ scope.row.u_name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="借阅书籍数量">
          <template slot-scope="scope">
            <span>{{ scope.row.u_number }}</span>
          </template>
        </el-table-column>
        <el-table-column label="违规次数">
          <template slot-scope="scope">
            <span>{{ scope.row.u_fail }}</span>
          </template>
        </el-table-column>
        <el-table-column label="操作">
          <template slot-scope="scope">
            <el-button size="mini" @click="handleEdit(scope.$index, scope.row)"
              >重置违规次数</el-button
            >
            <el-button
              size="mini"
              type="danger"
              @click="handleDelete(scope.$index, scope.row)"
              >删除</el-button
            >
          </template>
        </el-table-column>
      </el-table>
    </template>
    <el-pagination
      :current-page="currentPage"
      :page-sizes="[8, 20, 50, 100]"
      :page-size="pagesize"
      :total="tabledata.length"
      layout="total, sizes, prev, pager, next, jumper"
      @size-change="handleSizeChange"
      @current-change="handleCurrentChange"
      style="text-align: center"
    />
  </el-main>
</template>

<script>
export default {
  data() {
    return {
      loading: true,
      u_ID: "", //查找的用户名
      tabledata: [], //表格的数据
      currentPage: 1, //  el-pagination 初始页
      pagesize: 8, //  el-pagination 每页的数据
      idnex: 0,
      search_flag: false,
    };
  },
  mounted() {
    this.submit();
  },
  methods: {
    check_w() {
      this.loading = true;
      this.tabledata.splice(0, this.tabledata.length);
      this.$axios
        .post("http://119.29.62.3/s_failuser.php") //查看违规用户
        .then((Response) => {
          let res = Response.data;
          if (res[0].status == "1") {
            for (var i = 0; i < res.length; i++) this.tabledata.push(res[i]);
            this.loading = false;
            this.currentPage = 1;
          } else {
            this.$message.error("当前没有违规用户");
            this.loading = false;
          }
        });
      this.search_flag = true;
    },
    submit() {
      this.loading = true;
      this.$axios.post("http://119.29.62.3/s_alluser.php").then((Response) => {
        let res = Response.data;
        for (var i = 0; i < res.length; i++) this.tabledata.push(res[i]);
        this.loading = false;
      });
      this.search_flag = false;
    },
    back() {
      this.loading = true;
      this.$axios.post("http://119.29.62.3/s_alluser.php").then((Response) => {
        let res = Response.data;
        this.tabledata.splice(0, this.tabledata.length);
        for (var i = 0; i < res.length; i++) this.tabledata.push(res[i]);
        this.loading = false;
      });
      this.search_flag = false;
    },
    handleEdit(index, row) {
      this.idnex = index;
      this.u_ID = row.u_ID;
      this.$axios
        .post("http://119.29.62.3/u_user.php", {
          u_ID: this.u_ID,
        })
        .then((Response) => {
          let res = Response.data;
          if (res.status == "1") {
            row.u_fail = 0;
            this.$message.success("重置成功");
          } else if (res.status == "0") {
            this.$message.error("重置失败");
          }
        });
    },
    handleDelete(index, row) {
      this.u_ID = row.u_ID;
      this.$axios
        .post("http://119.29.62.3/d_user.php", {
          u_ID: this.u_ID,
        })
        .then((Response) => {
          let res = Response.data;
          if (res.status == "1") {
            this.back();
            this.$message.success("删除成功");
          } else if (res.status == "0") {
            this.$message.error("删除失败");
          }
        });
      //删除u_ID对应的
    },
    handleSizeChange: function (size) {
      this.pagesize = size;
      console.log(this.pagesize); // 每页下拉显示数据
    },
    handleCurrentChange: function (currentPage) {
      this.currentPage = currentPage;
      console.log(this.currentPage); // 点击第几页
    },
    search_id() {
      if (!this.u_ID) {
        this.$message.error("用户名为空");
      } else {
        this.search_flag = true;
        this.loading = true;
        this.$axios
          .post("http://119.29.62.3/s_user.php", {
            u_ID: this.u_ID,
          })
          .then((Response) => {
            let res = Response.data;
            if (res[0].status == "1") {
              this.tabledata.splice(0, this.tabledata.length);
              this.currentPage = 1;
              for (var i = 0; i < res.length; i++) {
                this.tabledata.push(res[i]);
                this.loading = false;
              }
            } else {
              this.$message.error("未查询到该用户");
              this.search_flag = false;
              this.loading = false;
            }
          });
      }
    },
  },
};
</script>