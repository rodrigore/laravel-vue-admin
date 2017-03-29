<template>
	<section>
        <!-- filtros -->
		<el-col :span="24" class="toolbar" style="padding-bottom: 0px;">
			<el-form :inline="true" :model="filters">
				<el-form-item>
					<el-input v-model="filters.name" placeholder="Nombre"></el-input>
				</el-form-item>
				<el-form-item>
					<el-button type="primary" v-on:click="getUsers" icon="search">Filtrar</el-button>
				</el-form-item>
				<el-form-item>
                    <el-button type="primary" @click="handleAdd" icon="plus">Agregar</el-button>
				</el-form-item>
			</el-form>
		</el-col>

		<!-- tabla -->
		<el-table :data="users" highlight-current-row v-loading="listLoading" element-loading-text="Cargando..." @sort-change="handleSortChange" @filter-change="handleFilterChange" style="width: 100%;">
			<el-table-column prop="id" width="60" sortable=false> </el-table-column>
            <el-table-column prop="name" label="Nombre" width="220" sortable=false> </el-table-column>
			<el-table-column prop="email" label="Email" width="250" sortable=false > </el-table-column>
            <el-table-column prop="sex" label="Sexo" width="90" column-key="sex" :filters="[{ text: 'Hombre', value: '1' }, { text: 'Mujer', value: '0' }]" >
                <template scope="scope">
                    <el-tag :color="scope.row.sex === 1 ? '#00b5ad' : '#e03997'" close-transition v-html="formatSex(scope.row.sex)"></el-tag>
                </template>
            </el-table-column>
			<el-table-column prop="age" label="Edad" width="100" sortable=false> </el-table-column>
			<el-table-column prop="birth" label="Creado" width="120" sortable=false> </el-table-column>
			<el-table-column prop="address" label="Dirección" min-width="180" sortable=false> </el-table-column>
			<el-table-column label="Acciones" width="150">
				<template scope="scope">
					<el-button size="small" @click="handleEdit(scope.$index, scope.row)" icon="edit"></el-button>
					<el-button type="danger" size="small" @click="handleDel(scope.$index, scope.row)" icon="delete"></el-button>
				</template>
			</el-table-column>
		</el-table>

		<!-- paginacion -->
		<el-col :span="24" class="toolbar">
			<el-pagination layout="prev, pager, next" @current-change="handleCurrentChange" :page-size="20" :total="total" style="float:right;">
			</el-pagination>
		</el-col>

		<!-- editar-->
		<el-dialog title="编辑" v-model="editFormVisible" :close-on-click-modal="false">
			<el-form :model="editForm" label-width="80px" :rules="editFormRules" ref="editForm">
				<el-form-item label="姓名" prop="name">
					<el-input v-model="editForm.name" auto-complete="off"></el-input>
				</el-form-item>
				<el-form-item label="性别">
					<el-radio-group v-model="editForm.sex">
						<el-radio class="radio" :label="1">Hombre</el-radio>
						<el-radio class="radio" :label="0">Mujer</el-radio>
					</el-radio-group>
				</el-form-item>
				<el-form-item label="年龄">
					<el-input-number v-model="editForm.age" :min="0" :max="200"></el-input-number>
				</el-form-item>
				<el-form-item label="生日">
					<el-date-picker type="date" placeholder="选择日期" v-model="editForm.birth"></el-date-picker>
				</el-form-item>
				<el-form-item label="地址">
					<el-input type="textarea" v-model="editForm.address"></el-input>
				</el-form-item>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button @click.native="editFormVisible = false">取消</el-button>
				<el-button type="primary" @click.native="editSubmit" :loading="editLoading">提交</el-button>
			</div>
		</el-dialog>

		<!-- agregar -->
		<el-dialog title="Nuevo usuario" v-model="addFormVisible" :close-on-click-modal="false">
			<el-form :model="addForm" label-width="80px" ref="addForm">
				<el-form-item label="Nombre" prop="name" :error="errors.get('name')">
					<el-input v-model="addForm.name" auto-complete="off" @change="errors.clear('name')"></el-input>
				</el-form-item>
				<el-form-item label="Sexo" :error="errors.get('sex')">
					<el-radio-group v-model="addForm.sex" name="sex" @change="errors.clear('sex')">
						<el-radio class="radio" :label="1">Hombre</el-radio>
						<el-radio class="radio" :label="0">Mujer</el-radio>
					</el-radio-group>
				</el-form-item>
				<el-form-item label="Edad" :error="errors.get('age')">
					<el-input-number v-model="addForm.age" @change="errors.clear('age')"></el-input-number>
				</el-form-item>
				<el-form-item label="Fecha nacimiento" :error="errors.get('birth')">
					<el-date-picker type="date" name="birth" placeholder="Seleccione" v-model="addForm.birth" @change="errors.clear('birth')"></el-date-picker>
				</el-form-item>
				<el-form-item label="Dirección" :error="errors.get('address')">
					<el-input type="textarea" v-model="addForm.address" @change="errors.clear('address')"></el-input>
				</el-form-item>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button @click.native="addFormVisible = false">Cancelar</el-button>
				<el-button type="primary" @click.native="addSubmit" :loading="addLoading">Crear</el-button>
			</div>
		</el-dialog>
	</section>
</template>

<script>
	import util from '../../common/js/util'
	import Errors from '../../common/js/Errors';

	import { getUserListPage, removeUser, batchRemoveUser, editUser, addUser } from '../../api/api';

	export default {
		data() {
			return {
                sortBy: ',',
				filters: {
					name: '',
					sex: ''
				},
				users: [],
                errors: new Errors(),
				total: 0,
				page: 1,
				listLoading: false,
				editFormVisible: false,
				editLoading: false,
				editFormRules: {
					name: [
						{ required: true, message: '请输入姓名', trigger: 'blur' }
					]
				},
				editForm: {
					id: 0,
					name: '',
					sex: '',
					age: '',
					birth: '',
					address: ''
				},

				addFormVisible: false,
				addLoading: false,
				addFormRules: {
					name: [
						{ required: true, message: 'El campo nombre es requerido', trigger: 'blur' }
					]
				},
				addForm: {
					name: '',
					sex: '',
					age: '',
					birth: '',
					address: ''
				}

			}
		},
		methods: {
			formatSex: function (sex) {
                return sex === 1
                    ? '<i class="fa fa-male"> H</i>'
                    : '<i class="fa fa-female"> M</i>'
			},
			handleSortChange(val) {
                if (val.prop != null) {
                    var sort = val.order.startsWith('a') ? 'asc' : 'desc';
                    this.sortBy = val.prop + ',' + sort;
                    this.getUsers();
                }
			},
			handleFilterChange(val) {
			    this.filters.sex = val.sex.join();
				this.getUsers();
			},
			handleCurrentChange(val) {
				this.page = val;
				this.getUsers();
			},
			//获取用户列表
			getUsers() {
				let para = {
					page: this.page,
					name: this.filters.name,
					sex: this.filters.sex,
					sortBy: this.sortBy,
				};
				this.listLoading = true;
				getUserListPage(para).then((res) => {
					this.total = res.data.total;
					this.users = res.data.data;
					this.listLoading = false;
				});
			},
			//删除
			handleDel: function (index, row) {
				this.$confirm('确认删除该记录吗?', '提示', {
					type: 'warning'
				}).then(() => {
					this.listLoading = true;
					//NProgress.start();
					let para = { id: row.id };
					removeUser(para).then((res) => {
						this.listLoading = false;
						//NProgress.done();
						this.$message({
							message: '删除成功',
							type: 'success'
						});
						this.getUsers();
					});
				}).catch(() => {

				});
			},
			//显示编辑界面
			handleEdit: function (index, row) {
				this.editFormVisible = true;
				this.editForm = Object.assign({}, row);
			},
			handleAdd: function () {
				this.addFormVisible = true;
				this.addForm = {
					name: '',
					sex: '',
					age: '',
					birth: '',
					address: ''
				};
			},
			editSubmit: function () {
				this.$refs.editForm.validate((valid) => {
					if (valid) {
						this.$confirm('确认提交吗？', '提示', {}).then(() => {
							this.editLoading = true;
							//NProgress.start();
							let para = Object.assign({}, this.editForm);
							para.birth = (!para.birth || para.birth == '') ? '' : util.formatDate.format(new Date(para.birth), 'yyyy-MM-dd');
							editUser(para).then((res) => {
								this.editLoading = false;
								//NProgress.done();
								this.$message({
									message: '提交成功',
									type: 'success'
								});
								this.$refs['editForm'].resetFields();
								this.editFormVisible = false;
								this.getUsers();
							});
						});
					}
				});
			},
			addSubmit: function () {
				this.$refs.addForm.validate((valid) => {
					if (valid) {
						this.addLoading = true;
						addUser(this.addForm).then((res) => {
							this.addLoading = false;
							this.$message({
								message: 'Usuario creado con exito!',
								type: 'success'
							});
							this.$refs['addForm'].resetFields();
							this.addFormVisible = false;
							this.getUsers();
						}).catch((error) => {
							this.errors.record(error.response.data);
							this.addLoading = false;
						});
					}
				});
			}
		},
		mounted() {
			this.getUsers();
		}
	}
</script>

<style scoped>

</style>
