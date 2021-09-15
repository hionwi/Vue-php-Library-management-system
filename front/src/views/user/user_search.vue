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
      v-model="search_key"
      placeholder="请输入关键词"
      style="width: 40%"
    ></el-input>
    <el-button type="primary" @click="search">查询</el-button>

    <div>
      <p></p>
      <span>类型</span>
      <el-select v-model="type_name" placeholder="请选择" filterable>
        <el-option
          v-for="item in options_type"
          :key="item.value"
          :label="item.label"
          :value="item.value"
        >
        </el-option>
      </el-select>
      <span>作者</span>
      <el-select v-model="author" placeholder="请选择" filterable>
        <el-option
          v-for="item in options_author"
          :key="item.value"
          :label="item.label"
          :value="item.value"
        >
        </el-option>
      </el-select>
      <p></p>
    </div>

    <template>
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
      search_key: "", //查询时的关键词
      loading: true, //加载
      tabledata: [], //表格的数据
      currentPage: 1, //  el-pagination 初始页
      pagesize: 8, //  el-pagination 每页的数据
      ISBN: "", //借阅时的isbn
      author: "", //查找的作者
      type_name: "", //查找的类型
      idnex: 0,
      search_flag: false,
      options_type: [{ value: "", label: "不限" }],
      options_author: [{ value: "", label: "不限" }],
    };
  },
  mounted() {
    this.submit();
  },
  methods: {
    find_author_type() {
      this.options_type.splice(0, this.options_type.length); //数组清零
      this.options_author.splice(0, this.options_author.length); //数组清零
      this.options_type.push({ value: "", label: "不限" });
      this.options_author.push({ value: "", label: "不限" });
      let type = "";
      let author = "";
      let tem_type = [];
      let tem_author = [];
      for (var i = 0; i < this.tabledata.length; i++) {
        type = this.tabledata[i].type_name; //类型名
        author = this.tabledata[i].author; //作者名
        tem_type.push(type);
        tem_author.push(author);
      }
      tem_type = Array.from(new Set(tem_type)); //类型名数组
      tem_author = Array.from(new Set(tem_author));
      for (var i = 0; i < tem_type.length; i++) {
        type = { value: tem_type[i], label: tem_type[i] };
        this.options_type.push(type); //获得图书的类型
      }
      for (var i = 0; i < tem_author.length; i++) {
        author = { value: tem_author[i], label: tem_author[i] };
        this.options_author.push(author); //获得图书的作者
      }
    },
    submit() {
      this.loading = true;
      this.$axios.post("http://119.29.62.3/all_books.php").then((Response) => {
        let res = Response.data;
        for (var i = 0; i < res.length; i++) {
          this.tabledata.push(res[i]);
        }
        this.find_author_type();
        this.loading = false;
      });
      this.search_flag = false;
    },
    back() {
      this.loading = true;
      this.$axios.post("http://119.29.62.3/all_books.php").then((Response) => {
        let res = Response.data;
        this.tabledata.splice(0, this.tabledata.length);
        for (var i = 0; i < res.length; i++) this.tabledata.push(res[i]);
        this.find_author_type();
        this.loading = false;
      });
      this.search_flag = false;
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
          this.back();
        });
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
      this.search_flag = true;
      this.loading = true;
      let bookname = "%";
      let bookisbn = "%";
      let type = "%";
      let au = "%";
      if (this.search_key !== "") {
        if (!isNaN(this.search_key)) {
          bookisbn = this.search_key;
        } else {
          bookname = this.search_key;
        }
      }
      if (this.type_name !== "") type = this.type_name;
      if (this.author !== "") au = this.author;

      this.$axios
        .post("http://119.29.62.3/us_book.php", {
          b_name: bookname,
          ISBN: bookisbn,
          author: au,
          type_name: type,
        })
        .then((Response) => {
          let res = Response.data;
          if (res[0].status_b == "1") {
            this.tabledata.splice(0, this.tabledata.length);
            this.currentPage = 1;
            for (var i = 0; i < res.length; i++) {
              this.tabledata.push(res[i]);
              this.loading = false;
            }
            this.find_author_type();
          } else {
            this.$message.error("未查询到该书籍");
            this.search_flag = false;
            this.loading = false;
          }
        });
    },
  },
};
</script>