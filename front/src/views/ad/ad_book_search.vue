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
      v-model="searchbook_name"
      placeholder="请输入关键词"
      style="width: 40%"
    ></el-input>
    <el-button type="primary" @click="search_name">查询</el-button>
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
        <el-table-column label="最晚归还日期">
          <template slot-scope="scope">
            <i class="el-icon-date"></i>
            <span style="margin-left: 10px">{{ scope.row.rule_time }}</span>
          </template>
        </el-table-column>
        <el-table-column label="操作">
          <template slot-scope="scope">
            <el-button size="mini" @click="handleEdit(scope.$index, scope.row)"
              >编辑</el-button
            >
            <el-dialog
              title="修改图书信息"
              :visible.sync="dialogVisible"
              width="30%"
            >
              <el-input v-model="item.b_name" placeholder="请输入修改的书名">
                <template slot="prepend">书名</template>
              </el-input>
              <p></p>
              <el-input v-model="item.author" placeholder="请输入修改的作者">
                <template slot="prepend">作者</template>
              </el-input>
              <p></p>
              <el-input v-model="item.type_name" placeholder="请输入修改的类型">
                <template slot="prepend">类型</template>
              </el-input>
              <span slot="footer" class="dialog-footer">
                <el-button @click="dialogVisible = false">取 消</el-button>
                <el-button type="primary" @click="changebook_">确 定</el-button>
              </span>
            </el-dialog>
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
      searchbook_name: "", //查找时的书名
      tabledata: [], //表格的数据
      currentPage: 1, //  el-pagination 初始页
      pagesize: 8, //  el-pagination 每页的数据
      ISBN: "", //删除时的依据isbn
      dialogVisible: false,
      row: {},
      idnex: 0,
      search_flag: false,
      item: {
        //修改的json
        ISBN: "",
        b_name: "",
        author: "",
        type_name: "",
      },
    };
  },
  mounted() {
    this.submit();
  },
  methods: {
    submit() {
      this.loading = true;
      this.$axios.post("http://119.29.62.3/all_books.php").then((Response) => {
        let res = Response.data;
        for (var i = 0; i < res.length; i++) this.tabledata.push(res[i]);
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
        this.loading = false;
      });
      this.search_flag = false;
    },
    handleEdit(index, row) {
      this.dialogVisible = true;
      this.idnex = index;
      this.row = row;
      this.item.ISBN = "";
      this.item.b_name = "";
      this.item.author = "";
      this.item.type_name = "";
    },
    changebook_() {
      if (!this.item.b_name || !this.item.author || !this.item.type_name) {
        this.$message.error("修改书籍信息不能为空");
      } else {
        this.row.b_name = this.item.b_name;
        this.row.author = this.item.author;
        this.row.type_name = this.item.type_name;
        this.item.ISBN = this.row.ISBN;
        this.$axios
          .post("http://119.29.62.3/w_u_book.php", {
            ISBN: this.item.ISBN,
            b_name: this.item.b_name,
            author: this.item.author,
            type_name: this.item.type_name,
          })
          .then((Response) => {
            let res = Response.data;
            if (res.status == "1") {
              this.$message.success("修改成功");
              this.dialogVisible = false;
            } else if (res.status == "0") {
              this.$message.error("修改失败");
            }
          });
      }
    },
    handleDelete(index, row) {
      this.ISBN = row.ISBN;
      this.$axios
        .post("http://119.29.62.3/d_book.php", {
          ISBN: this.ISBN,
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
      //删除isbn对应的
    },
    handleSizeChange: function (size) {
      this.pagesize = size;
      console.log(this.pagesize); // 每页下拉显示数据
    },
    handleCurrentChange: function (currentPage) {
      this.currentPage = currentPage;
      console.log(this.currentPage); // 点击第几页
    },
    search_name() {
      if (!this.searchbook_name) {
        this.$message.error("关键词不能为空");
      } else {
        let bookname = "%";
        let bookisbn = "%";
        this.search_flag = true;
        this.loading = true;
        if (!isNaN(this.searchbook_name)) {
          bookisbn = this.searchbook_name;
        } else {
          bookname = this.searchbook_name;
        }
        this.$axios
          .post("http://119.29.62.3/s_book.php", {
            b_name: bookname,
            ISBN: bookisbn,
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
            } else {
              this.$message.error("未查询到该书籍");
              this.search_flag = false;
              this.loading = false;
            }
          });
      }
    },
  },
};
</script>