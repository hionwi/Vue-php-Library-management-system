<template>
  <el-main>
    <template>
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
        <el-table-column label="起始日期">
          <template slot-scope="scope">
            <i class="el-icon-date"></i>
            <span style="margin-left: 10px">{{ scope.row.start_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="最晚归还日期">
          <template slot-scope="scope">
            <i class="el-icon-date"></i>
            <span style="margin-left: 10px">{{ scope.row.rule_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="是否违规">
          <template slot-scope="scope">
            <span style="margin-left: 10px">{{ scope.row.fail_status }}</span>
          </template>
        </el-table-column>
        <el-table-column label="操作">
          <template slot-scope="scope">
            <el-button
              size="mini"
              type="primary"
              @click="borrow(scope.$index, scope.row)"
              >续借</el-button
            >
            <el-button
              size="mini"
              type="danger"
              @click="back(scope.$index, scope.row)"
              >归还</el-button
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
      ISBN: "", //借阅时的isbn
      author: "", //查找的作者
      type_name: "", //查找的类型
      idnex: 0,
      search_flag: false,
      userid: sessionStorage.getItem("userid"),
    };
  },
  mounted() {
    this.submit();
  },
  methods: {
    submit() {
      this.loading = true;
      this.$axios
        .post("http://119.29.62.3/u_allbooks.php", {
          u_ID: this.userid,
        })
        .then((Response) => {
          let res = Response.data;
          this.tabledata.splice(0, this.tabledata.length);
          if (res[0].status_b == "1") {
            for (var i = 0; i < res.length; i++) {
              if (res[i].fail_status == "1") {
                res[i].fail_status = "违规";
                if (res[i].deal_status == "1") res[i].deal_status = "已处理";
                else if (res[i].deal_status == "0")
                  res[i].deal_status = "未处理";
              } else if (res[i].fail_status == "0") {
                res[i].fail_status = "未违规";
                res[i].deal_status = "";
              }
              this.tabledata.push(res[i]);
            }
          }
          this.loading = false;
        });
    },
    back(index, row) {
      this.$axios
        .post("http://119.29.62.3/user_reback.php", {
          u_ID: this.userid,
          ISBN: row.ISBN,
        })
        .then((Response) => {
          let res = Response.data;
          if (res.status == "1") {
            this.$message.success("归还成功");
            this.tabledata.splice(index, 1);
          } else if (res.status == "0") {
            this.$message.error("归还失败");
          } else if (res.status == "-1") {
            this.$message.warning("归还超时");
            this.tabledata.splice(index, 1);
          }
        });
    },
    borrow(index, row) {
      this.$axios
        .post("http://119.29.62.3/user_retime.php", {
          ISBN: row.ISBN,
          u_ID: this.userid,
        })
        .then((Response) => {
          let res = Response.data;
          if (res.status == "1") {
            this.$message.success("续借成功");
            this.submit();
            //续借
          } else if (res.status == "0") {
            this.$message.error("续借失败，违规次数过多");
          } else if (res.status == "-1") {
            this.$message.warning("续借超时");
            this.submit();
            //续借
          } else if (res.status == "2") {
            this.$message.error("已超过最大续借次数");
          }
        });
    },
  },
};
</script>