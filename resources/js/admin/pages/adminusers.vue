<template>
	<div>
		<div class="content">
			<div class="container-fluid"><!--fluid-->
				
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Users   <Button @click="addModal=true"> <Icon type="md-add" /> Add admin</Button></p>

					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>ID</th>
								<th>Name</th>
								<th>Email</th>
								<th>User type</th>
								<th>Created at</th>
								<th>Action</th>
								</tr>
								<!-- TABLE TITLE -->
								<!-- ITEMS -->
							<tr v-for="(user, i) in users" :key="i" v-if="users.length">
							
								<td>{{user.id}}</td>
								<td class="_table_name">{{user.fullName}}</td>
								<td>{{user.email}}</td>
								<td v-for="(r, i) in roles" :key="i" v-if="roles.length && user.role_id === r.id">{{r.roleName}}</td>
								<td>{{user.created_at}}</td>
								<td>
									 <Button type="info" size="small" @click="showEditModal(user, i)">Edit</Button>
									 <Button type="error" size="small" @click="showDeletingModal(user, i)">Delete</Button>
									 <Button type="success" size="small" @click="showPasswordModal(user, i)">Change Password</Button>
								</td>
								</tr>
								<!-- ITEMS -->
						</table>
					</div>
				</div>
				 <Page :total="100" />
				 <!--USER ADDING MODAL -->
				<Modal
			        v-model="addModal"
			        title="Add Admin"
					:closable="false"
			       	>
			       		<div class="mt-2">
			       			<Input type="text" v-model="data.fullName" placeholder="Full name"/>
			       		</div>
			       		<div class="mt-2">
			       			<Input type="email" v-model="data.email" placeholder="Email address"/>
			       		</div>
			       		<div class="mt-2">
			       			<Input type="password" v-model="data.password" placeholder="Password"/>
			       		</div>
			       		<div class="mt-2">
			       			 <Select v-model="data.role_id"  placeholder="Select admin type">
                            	<Option :value="r.id" v-for="(r, i) in roles" :key="i" v-if="roles.length">{{r.roleName}}</Option>
                        	</Select>
			       		</div>
				        <div slot="footer">
				        	<Button type="default" size="small" @click="addModal=false">Close</Button>
				        	<Button type="primary" size="small" @click="addAdmin" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding..' : 'Add Admin'}}</Button>
				        </div>
				    </Modal>
		    <!--USER EDITING MODAL -->
				<Modal
			        v-model="editModal"
			        title="Edit User"
					:closable="false"
			       	>
			       <div class="mt-2">
			       			<Input type="text" v-model="editData.fullName" placeholder="Full name"/>
			       		</div>
			       		<div class="mt-2">
			       			<Input type="email" v-model="editData.email" placeholder="Email address"/>
			       		</div>
			       		<div class="mt-2">
			       			 <Select v-model="editData.role_id" placeholder="Select admin type">
                            	<Option :value="r.id" v-for="(r, i) in roles" :key="i" v-if="roles.length">{{r.roleName}}</Option>
                        	</Select>
			       		</div>
			        <div slot="footer">
			        	<Button type="default" size="small" @click="editModal=false">Close</Button>
			        	<Button type="primary" size="small" @click="editUser" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Editing..' : 'Edit tag'}}</Button>
			        </div>
			    	</Modal>

		    <!--USER DELETING MODAL -->
		    <deleteModal></deleteModal>

		    <!--USER CHANGE PASSWORD MODAL -->
				<Modal
			        v-model="changePassModal"
			        title="Change Password"
					:closable="false"
			       	>
			       		<div class="mt-2">
			       			<Input type="password" v-model="editData.password" placeholder="New Password"/>
			       		</div>
			       		<div class="mt-2">
			       			 <Input type="password" v-model="editData.rePassword" placeholder="Repeat new Password"/>
						    
			       		</div>
			        <div slot="footer">
			        	<Button type="default" size="small" @click="changePassModal=false">Close</Button>
			        	<Button type="primary" size="small" @click="changePasswordUser" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Editing..' : 'Change'}}</Button>
			        </div>
			    	</Modal>


			</div>
		</div>
	</div>
</template>

<script>
import deleteModal from '../components/deleteModal'
import { mapGetters } from 'vuex'
export default{
	data(){
		return {
			data: {
				fullName: '',
				email: '',
				password: '',
				role_id: 2,
			},
			addModal: false,
			editModal: false,
			showDeleteModal: false,
			changePassModal: false,
			isAdding: false,
			users: [],
			roles: [],
			editData: {
				fullName: '',
				email: '',
				user_id: null,
				role_id: 2,
				password: '',
				rePassword: '',
			},
			index: -1,
			deleteIndex : -1,
			deleteItem: {},
			isDeleting: false,
		}
	},

	methods: {
	
	async addAdmin(){
			if (this.data.fullName.trim()=='') return this.w('Full name is required.')
			if (this.data.email.trim()=='') return this.w('Email is required.')
			if (this.data.password.trim()=='') return this.w('Password is required.')

			if(!this.data.role_id) return this.w('User type is required')

				const res = await this.callApi('post', 'app/create_user', this.data)
			if (res.status === 201){
				this.users.unshift(res.data)
				this.s('Admin user has been added successfully!')
				this.addModal = false
				this.data.fullName = ''
				this.data.email = ''
				this.data.password = ''
			}else{
				if(res.status == 422){

					for(let i in res.data.errors){
						this.e(res.data.errors.[i][0]);
					}

				}else{
					this.e()
				}
				
			}
	},
	async editUser(){
			if (this.editData.fullName.trim()=='') return this.w('Full name is required.')
			if (this.editData.email.trim()=='') return this.w('Email is required.')
			if (!this.editData.role_id) return this.w('User type is required.')
				const res = await this.callApi('post', 'app/edit_user', this.editData)
			if (res.status === 200){
				this.users[this.index].fullName = this.editData.fullName
				this.users[this.index].email = this.editData.email
				this.users[this.index].role_id = this.editData.role_id
				this.s('User has been edited successfully!')
				this.editModal = false
			}else{
				if(res.status == 422){
					for(let i in res.data.errors){
						this.e(res.data.errors.[i][0]);
					}
				}else{
					this.e()
				}
				
			}
		},


	async changePasswordUser(){
			if (this.editData.password.trim()=='') return this.w('New Password is required.')
			if (this.editData.rePassword.trim()=='') return this.w('Repeat new Password is required.')
			if (this.editData.rePassword.trim()!=this.editData.password.trim()) return this.w('Repeat Password is not contained in New password.')
				const res = await this.callApi('post', 'app/change_password_user', this.editData)
			if (res.status === 200){
				this.users[this.index].fullName = this.editData.fullName
				this.users[this.index].email = this.editData.email
				this.users[this.index].role_id = this.editData.role_id
				this.s('Password has been changed successfully!')
				this.changePassModal = false
			}else{
				if(res.status == 422){
					for(let i in res.data.errors){
						this.e(res.data.errors.[i][0]);
					}
				}else{
					this.e()
				}
				
			}
		},	
	showEditModal(user, index){
		let obj = {
			id: user.id,
			fullName: user.fullName,
			email: user.email,
			role_id: user.role_id,
			}
		this.editData = obj
		this.editModal = true
		this.index = index
	},
	showPasswordModal(user, index){
		let obj = {
			id: user.id,
			fullName: user.fullName,
			email: user.email,
			role_id: user.role_id,
			password: '',
			rePassword: '',
			}
		 this.editData = obj
		this.changePassModal = true
		this.index = index
	},

	showDeletingModal(user, i){
		const deleteModalObj = {
			showDeleteModal: true,
			deleteUrl: '/app/delete_user',
			data: user,
			deleteIndex: i,
			isDeleted: false,
			}
		this.$store.commit('setDeleteModalObj', deleteModalObj)
		},
	},

	async created(){
		const [res, resRole] = await Promise.all([
			this.callApi('get', 'app/get_users'), 
			this.callApi('get', 'app/get_roles')
		])
		if(res.status==200){
			this.users = res.data
		}else{
			this.e()
		}
		if(resRole.status==200){
			this.roles = resRole.data
		}else{
			this.e()
		}
	},

	components: {
		deleteModal,
	},

	computed : {
        ...mapGetters(['getDeleteModalObj'])
    },

	watch : {
		getDeleteModalObj(obj){
			if(obj.isDeleted){
				this.users.splice(obj.deleteIndex, 1)
			}
		}
	}
}
</script>