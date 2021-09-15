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
      v-model="searchkey"
      placeholder="请输入关键词"
      style="width: 40%"
    ></el-input>
    <el-button type="primary" @click="search">查询</el-button>
    <el-button type="danger" @click="check_w">查看违规信息</el-button>
    <template>
      <p></p>
      <el-table
        v-loading="loading"
        :data="
          tabledata.slice((currentPage - 1) * pagesize, currentPage * pagesize)
        "
        style="width: 100%"
      >
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
        <el-table-column prop="u_ID" label="借书者学号"> </el-table-column>
        <el-table-column prop="u_name" label="借书者姓名"> </el-table-column>
        <el-table-column label="起始日期">
          <template slot-scope="scope">
            <i class="el-icon-date"></i>
            <span style="margin-left: 10px">{{ scope.row.start_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="最晚还书日期">
          <template slot-scope="scope">
            <i class="el-icon-date"></i>
            <span style="margin-left: 10px">{{ scope.row.rule_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="是否违规">
          <template slot-scope="scope">
            <span>{{ scope.row.fail_status }}</span>
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
      searchkey: "", //查找时的书名
      tabledata: [], //表格的数据
      currentPage: 1, //  el-pagination 初始页
      pagesize: 8, //  el-pagination 每页的数据
      ISBN: "", //删除时的依据isbn
      row: {},
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
        .post("http://119.29.62.3/w_failnow.php") //查看当前违规信息
        .then((Response) => {
          let res = Response.data;
          if (res[0].status == "1") {
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
            this.currentPage = 1;
            this.loading = false;
          } else {
            this.$message.error("当前没有违规信息");
            this.loading = false;
          }
        });
      this.search_flag = true;
    },
    submit() {
      this.loading = true;
      this.$axios
        .post("http://119.29.62.3/w_bro.php") //查看当前借阅的所有图书
        .then((Response) => {
          let res = Response.data;
          for (var i = 0; i < res.length; i++) {
            if (res[i].fail_status == "1") {
              res[i].fail_status = "违规";
              if (res[i].deal_status == "1") res[i].deal_status = "已处理";
              else if (res[i].deal_status == "0") res[i].deal_status = "未处理";
            } else if (res[i].fail_status == "0") {
              res[i].fail_status = "未违规";
              res[i].deal_status = "";
            }
            this.tabledata.push(res[i]);
          }
          this.loading = false;
        });
      this.search_flag = false;
    },
    back() {
      this.loading = true;
      this.$axios.post("http://119.29.62.3/w_bro.php").then((Response) => {
        let res = Response.data;
        this.tabledata.splice(0, this.tabledata.length);
        for (var i = 0; i < res.length; i++) {
          if (res[i].fail_status == "1") {
            res[i].fail_status = "违规";
            if (res[i].deal_status == "1") res[i].deal_status = "已处理";
            else if (res[i].deal_status == "0") res[i].deal_status = "未处理";
          } else if (res[i].fail_status == "0") {
            res[i].fail_status = "未违规";
            res[i].deal_status = "";
          }
          this.tabledata.push(res[i]);
        }
        this.loading = false;
      });
      this.search_flag = false;
    },
    handleSizeChange: function (size) {
      this.pagesize = size;
      console.log(this.pagesize); // 每页下拉显示数据
    },
    handleCurrentChange: function (currentPage) {
      this.currentPage = currentPage;
      console.log(this.currentPage); // 点击第几页
    },
    search() {
      if (!this.searchkey) {
        this.$message.error("关键词不能为空");
      } else {
        let bookname = "%";
        let bookisbn = "%";
        let userid = "%";
        this.search_flag = true;
        this.loading = true;
        if (isNaN(this.searchkey)) {
          bookname = this.searchkey;
        } else {
          if (this.searchkey.length == 13) {
            bookisbn = this.searchkey;
          } else {
            userid = this.searchkey;
          }
        }

        this.$axios
          .post("http://119.29.62.3/s_nowbro.php", {
            //搜索当前借阅
            b_name: bookname,
            ISBN: bookisbn,
            u_ID: userid,
          })
          .then((Response) => {
            let res = Response.data;
            if (res[0].status == "1") {
              this.tabledata.splice(0, this.tabledata.length);
              this.currentPage = 1;
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
                this.loading = false;
              }
            } else {
              this.$message.error("未查询到该信息");
              this.search_flag = false;
              this.loading = false;
            }
          });
      }
    },
  },
};
</script>