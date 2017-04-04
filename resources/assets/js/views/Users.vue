<template>
	<section>
        <!-- filters -->
		<el-col :span="24" class="toolbar" style="padding-bottom: 0px;">
			<el-form :inline="true" :model="filters">
				<el-form-item>
					<el-input v-model="filters.name" placeholder="Name"></el-input>
				</el-form-item>
				<el-form-item>
					<el-button type="primary" v-on:click="getUsers" icon="search">Filter</el-button>
				</el-form-item>
				<el-form-item>
                    <el-button type="primary" @click="handleAdd" icon="plus">Add</el-button>
				</el-form-item>
			</el-form>
		</el-col>

		<!-- table -->
		<el-table :data="users" highlight-current-row v-loading="listLoading" element-loading-text="Loading ..." @sort-change="handleSortChange" @filter-change="handleFilterChange" style="width: 100%;">
			<el-table-column prop="id" width="60" sortable=false> </el-table-column>
            <el-table-column prop="name" label="Name" width="220" sortable=false> </el-table-column>
			<el-table-column prop="email" label="Email" width="250" sortable=false > </el-table-column>
            <el-table-column prop="sex" label="Sex" width="90" column-key="sex" :filters="[{ text: 'Male', value: '1' }, { text: 'Female', value: '0' }]" >
                <template scope="scope">
                    <el-tag :color="scope.row.sex === 1 ? '#00b5ad' : '#e03997'" close-transition v-html="formatSex(scope.row.sex)"></el-tag>
                </template>
            </el-table-column>
			<el-table-column prop="age" label="Age" width="100" sortable=false> </el-table-column>
			<el-table-column prop="birth" label="Birth" width="120" sortable=false> </el-table-column>
			<el-table-column prop="address" label="Address" min-width="180" sortable=false> </el-table-column>
			<el-table-column label="Actions" width="150">
				<template scope="scope">
					<el-button size="small" @click="handleEdit(scope.$index, scope.row)" icon="edit"></el-button>
					<el-button type="danger" size="small" @click="handleDel(scope.$index, scope.row)" icon="delete"></el-button>
				</template>
			</el-table-column>
		</el-table>

		<!-- pagination -->
		<el-col :span="24" class="toolbar">
			<el-pagination layout="prev, pager, next" @current-change="handleCurrentChange" :page-size="20" :total="total" style="float:right;">
			</el-pagination>
		</el-col>

		<!-- add dialog -->
		<el-dialog title="New user" v-model="addFormVisible" :close-on-click-modal="false">
			<el-form :model="addForm" label-width="80px" ref="addForm">
				<el-form-item label="Name" prop="name" :error="errors.get('name')">
					<el-input v-model="addForm.name" @change="errors.clear('name')" icon="fa fa-vcard"></el-input>
				</el-form-item>
				<el-form-item label="Email" prop="email" :error="errors.get('email')">
					<el-input v-model="addForm.email"  @change="errors.clear('email')" icon="message"></el-input>
				</el-form-item>
                <el-form-item label="Password" prop="password" :error="errors.get('password')">
                    <el-row>
                        <el-col :span="11">
                            <el-input v-model="addForm.password"  @change="errors.clear('password')" type="password" icon="fa fa-key" placeholder="Ingrese password"></el-input>
                        </el-col>
                        <el-col :span="11" :offset="2">
                            <el-input v-model="addForm.password_confirmation" type="password" icon="fa fa-key" placeholder="Confirmar password"></el-input>
                        </el-col>
                    </el-row>
                </el-form-item>
				<el-form-item label="Sex" :error="errors.get('sex')">
					<el-radio-group v-model="addForm.sex" name="sex" @change="errors.clear('sex')">
						<el-radio class="radio" :label="1">Hombre</el-radio>
						<el-radio class="radio" :label="0">Mujer</el-radio>
					</el-radio-group>
				</el-form-item>
				<el-form-item label="Age" :error="errors.get('age')">
					<el-input-number v-model="addForm.age" @change="errors.clear('age')"></el-input-number>
				</el-form-item>
				<el-form-item label="Birth" :error="errors.get('birth')">
					<el-date-picker type="date" name="birth" placeholder="Seleccione" v-model="addForm.birth" @change="errors.clear('birth')"></el-date-picker>
				</el-form-item>
				<el-form-item label="Address" :error="errors.get('address')">
					<el-input type="textarea" v-model="addForm.address" @change="errors.clear('address')" icon="fa fa-marker"></el-input>
				</el-form-item>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button @click.native="addFormVisible = false">Cancel</el-button>
				<el-button type="primary" @click.native="addSubmit" :loading="addLoading">Create</el-button>
			</div>
		</el-dialog>

		<!-- edit dialog -->
        <el-dialog title="Edit user" v-model="editFormVisible" :close-on-click-modal="false">
			<el-form :model="editForm" label-width="80px" ref="editForm">
				<el-form-item label="Name" prop="name" :error="errors.get('name')">
					<el-input v-model="editForm.name" @change="errors.clear('name')" icon="fa fa-vcard"></el-input>
				</el-form-item>
				<el-form-item label="Email" prop="email" :error="errors.get('email')">
					<el-input v-model="editForm.email"  @change="errors.clear('email')" icon="message"></el-input>
				</el-form-item>
				<el-form-item label="Sex" :error="errors.get('sex')">
					<el-radio-group v-model="editForm.sex" name="sex" @change="errors.clear('sex')">
						<el-radio class="radio" :label="1">Hombre</el-radio>
						<el-radio class="radio" :label="0">Mujer</el-radio>
					</el-radio-group>
				</el-form-item>
				<el-form-item label="Age" :error="errors.get('age')">
					<el-input-number v-model="editForm.age" @change="errors.clear('age')"></el-input-number>
				</el-form-item>
				<el-form-item label="Birth" :error="errors.get('birth')">
					<el-date-picker type="date" name="birth" placeholder="Seleccione" v-model="editForm.birth" @change="errors.clear('birth')"></el-date-picker>
				</el-form-item>
				<el-form-item label="address" :error="errors.get('address')">
					<el-input type="textarea" v-model="editForm.address" @change="errors.clear('address')" icon="fa fa-marker"></el-input>
				</el-form-item>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button @click.native="editFormVisible = false">Cancel</el-button>
				<el-button type="primary" @click.native="editSubmit" :loading="editLoading">Edit</el-button>
			</div>
		</el-dialog>

	</section>
</template>

<script>
	import util from '../common/js/util'
	import Errors from '../common/js/Errors';

	import { getUserListPage, addUser, editUser, removeUser } from '../endpoints';

	export default {
		data() {
			return {
                sortBy: 'id,asc',
				filters: {
					name: '',
					sex: ''
				},
				users: [],
                errors: new Errors(),
				total: 0,
				page: 1,
				listLoading: false,
				addFormVisible: false,
				addLoading: false,
				editFormVisible: false,
				editLoading: false,
				addForm: {},
                editForm: {}
			}
		},
		methods: {
			formatSex: function (sex) {
                return sex === 1
                    ? '<i class="fa fa-male"> M</i>'
                    : '<i class="fa fa-female"> F</i>'
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
			handleDel: function (index, row) {
                this.$confirm('This will permanently delete the user. Continue?', 'Warning', {
					type: 'warning'
				}).then(() => {
					this.listLoading = true;
					let params = { id: row.id };
					removeUser(params).then((response) => {
						this.listLoading = false;
                        this.$notify({
                            title: 'Success',
                            message: response.data.message,
                            type: 'success'
                        });
						this.getUsers();
					});
				}).catch(() => {
				});
			},
			handleEdit: function (index, row) {
				this.editFormVisible = true;
				this.editForm = Object.assign({}, row);
                this.errors = new Errors()
			},
			handleAdd: function () {
				this.addFormVisible = true;
				this.addForm = {
					name: '',
					email: '',
                    password: '',
                    password_confirmation: '',
					sex: '',
					age: '',
					birth: '',
					address: ''
				};
                this.errors = new Errors()
			},
			addSubmit: function () {
                this.addLoading = true;
                addUser(this.addForm).then((response) => {
                    this.addLoading = false;
                    this.$notify({
                        title: 'Success',
                        message: response.data.message,
                        type: 'success'
                    });
                    this.$refs['addForm'].resetFields(); // maybe not necessary
                    this.addFormVisible = false;
                    this.getUsers();
                }).catch((error) => {
                    this.errors.record(error.response.data);
                    this.addLoading = false;
                });
            },
			editSubmit: function () {
                editUser(this.editForm).then((response) => {
                    this.editLoading = false;
                    this.$notify({
                        title: 'Success',
                        message: response.data.message,
                        type: 'success'
                    });
                    this.$refs['editForm'].resetFields();
                    this.editFormVisible = false;
                    this.getUsers();
                }).catch((error) => {
                    this.errors.record(error.response.data);
                    this.editLoading = false;
                });
			},
        },
		mounted() {
			this.getUsers();
		}
	}
</script>

<style scoped>
</style>
