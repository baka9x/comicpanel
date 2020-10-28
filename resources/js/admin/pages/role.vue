<template>
	<div>
		<div class="content">
			<div class="container-fluid"><!--fluid-->
				
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Role Management   <Button @click="addModal=true"> <Icon type="md-add" /> Add new a role</Button></p>

					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>ID</th>
								<th>Role type</th>
								<th>Created at</th>
								<th>Action</th>
								</tr>
								<!-- TABLE TITLE -->
								<!-- ITEMS -->
							<tr v-for="(role, i) in roles" :key="i" v-if="roles.length">
								<td>{{role.id}}</td>
								<td class="_table_name">{{role.roleName}}</td>
								<td>{{role.created_at}}</td>
								<td>
									
									 <Button type="info" size="small" @click="showEditModal(role, i)">Edit</Button>
									 <Button type="error" size="small" @click="showDeletingModal(role, i)">Delete</Button>
								</td>
								</tr>
								<!-- ITEMS -->
						</table>
					</div>
				</div>
				 <Page :total="100" />
				 <!--TAG ADDING MODAL -->
				<Modal
			        v-model="addModal"
			        title="Add new role"
					:closable="false"
			       	>
				        <Input v-model="data.roleName" placeholder="Enter role name..."/>
				        <div slot="footer">
				        	<Button type="default" size="small" @click="addModal=false">Close</Button>
				        	<Button type="primary" size="small" @click="addRole" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding..' : 'Add Role'}}</Button>
				        </div>
				    </Modal>
		    <!--TAG EDITING MODAL -->
				<Modal
			        v-model="editModal"
			        title="Edit tag"
					:closable="false"
			       	>
			        <Input v-model="editData.roleName" placeholder="Enter role name..."/>
			        <div slot="footer">
			        	<Button type="default" size="small" @click="editModal=false">Close</Button>
			        	<Button type="primary" size="small" @click="editRole" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Editing..' : 'Edit role'}}</Button>
			        </div>
			    	</Modal>

		    <!--TAG DELETING MODAL -->
		    <deleteModal></deleteModal>


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
				roleName: ''
			},
			addModal: false,
			editModal: false,
			showDeleteModal: false,
			isAdding: false,
			roles: [],
			editData: {
				roleName: ''
			},
			index: -1,
			deleteIndex : -1,
			deleteItem: {},
			isDeleting: false,
		}
	},

	methods: {
	async addRole(){
			if (this.data.roleName.trim()=='') return this.w('Role name is required.')
				const res = await this.callApi('post', 'app/create_role', this.data)
			if (res.status === 201){
				this.roles.unshift(res.data)
				this.s('Role has been added successfully!')
				this.addModal = false
				this.data.roleName = ''
			}else{
				if(res.status == 422){
					if(res.data.errors.roleName){
						this.e(res.data.errors.roleName[0]);
					}
				}else{
					this.e()
				}
				
			}
	},
	async editRole(){
			if (this.editData.roleName.trim()=='') return this.w('Role name is required.')
				const res = await this.callApi('post', 'app/edit_role', this.editData)
			if (res.status === 200){
				this.roles[this.index].roleName = this.editData.roleName
				this.s('Tag has been edited successfully!')
				this.editModal = false
			}else{
				if(res.status == 422){
					if(res.data.errors.roleName){
						this.e(res.data.errors.roleName[0]);
					}
				}else{
					this.e()
				}
				
			}
		},
	showEditModal(role, index){
		let obj = {
			id: role.id,
			roleName: role.roleName,
			}
		this.editData = obj
		this.editModal = true
		this.index = index
	},

	showDeletingModal(role, i){
		const deleteModalObj = {
			showDeleteModal: true,
			deleteUrl: '/app/delete_role',
			data: role,
			deleteIndex: i,
			isDeleted: false,
			}
		this.$store.commit('setDeleteModalObj', deleteModalObj)
		},
	},

	async created(){
		const res = await this.callApi('get', '/app/get_roles');
		if(res.status == 200){
			this.roles = res.data
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
				this.roles.splice(obj.deleteIndex, 1)
			}
		}
	}
}
</script>